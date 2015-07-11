/**
 * Created by NyanTun on 7/10/2015.
 */
(function($){
    //Convert time to minute (1:00 => 60)
    function parseTimeToMinute(s){
        if(s == null || s.length <= 0)
            return 0;

        var c = s.split(':');
        return parseInt(c[0]) * 60 + parseInt(c[1]);
    }

    var payroll_data = {
        weeklyHoliday   : {},
        workingHours    : {},
        lateList        : {},
        leaveValues     : {}
    };

    var payroll_url = {
        attendance  : '',
        leave       : ''
    };

    $.setPayrollData = function(options){
        payroll_data = options;
    };

    $.setPayrollUrl = function(options){
        payroll_url = options;
    };

    function checkHoliday(date){
        var isHoliday = false;
        $.each(payroll_data.weeklyHoliday, function(){
            var holiday = $(this)[0].day - 1;
            if(date.weekday() == holiday){
                isHoliday = true;
                return false;
            }
        });
        return isHoliday;
    }

    function getAttendance(attendance, dayOfWeek){
        var full_day = payroll_data.workingHours[dayOfWeek];

        if(!full_day) return 0;

        var total_minute = parseTimeToMinute(full_day.to) - parseTimeToMinute(full_day.from);
        var half_day = parseTimeToMinute(full_day.from) + (total_minute / 2);

        var in_time = parseTimeToMinute(attendance.inTime);
        var out_time = parseTimeToMinute(attendance.outTime);

        if(in_time > half_day || out_time < half_day){
            return 0.5;
        }else{
            var hasLate = false;
            $.each(payroll_data.lateList, function(idx, late){
                var ptr = '#' + late.code + '-' + attendance.staffId;
                var currentLate = parseInt($(ptr).html());

                var late_from = parseTimeToMinute(full_day.from) + late.minute;
                if(in_time > late_from){
                    console.log(attendance);
                    $(ptr).html(currentLate + 1);
                    hasLate = true;
                }

                var late_to = parseTimeToMinute(full_day.to) - late.minute;
                if(out_time < late_to){
                    console.log(attendance);
                    $(ptr).html(currentLate + 1);
                    hasLate = true;
                }
                if(hasLate){
                    return false;
                }
            });
            return 1;
        }
    }

    $.fn.collectAttendance = function(options){
        var settings = $.extend({
            start: moment(),
            end: moment().subtract(1, 'month'),
            progress: function(per){console.log('collectAttendance => ' + per + '%');}
        }, options);

        var staffId = $(this).attr('data-id');

        if(staffId == 0) return;

        var current_row = $(this);

        current_row.children('td#M_WD').html(0);
        current_row.children('td#S_WD').html(0);
        current_row.children('td#Leave').html(0);
        current_row.children('td#Absent').html(0);
        $.each(payroll_data.lateList, function(idx, late){
            current_row.children('td#' + late.code + '-' + staffId).html(0);
        });

        settings.progress(5);

        var Holiday = 0;
        var M_WD = 0;
        var S_WD = 0;
        var Leave = 0;
        var Absent = 0;

        var totalDay = settings.end.diff(settings.start, 'days', true);
        var increment = 90 / totalDay;
        var currentProgress = 5;
        settings.end.add(2, 'day');
        //Loop Start to End by day
        for(var d = settings.start; d < settings.end; d.add(1, 'day')) {
            currentProgress += increment;
            //Check this date is holiday
            if (checkHoliday(d)) {
                //Increment to holiday count
                Holiday++;
                continue;
            }

            //Increment to monthly working day
            current_row.children('td#M_WD').html(++M_WD);

            //Check attendance by staff and date
            $.ajax({
                'url': payroll_url.attendance,
                'data': {date: d.format('YYYY-MM-DD'), staffId: staffId},
                'type': 'get',
                'async': false,
                'success': function (data) {
                    if (data.status) {
                        //Validate late minutes and update Staff Working Day
                        S_WD += getAttendance(data.result, d.weekday());
                    }
                    current_row.children('td#S_WD').html(S_WD);
                }
            });

            //Check leave by staff and date
            $.ajax({
                'url': payroll_url.leave,
                'data': {date: d.format('YYYY-MM-DD'), staffId: staffId},
                'type': 'get',
                'async': false,
                'success': function (data) {
                    if (data.status) {
                        $.each(payroll_data.leaveValues, function (idx, leave) {
                            if (leave.id == data.result.leaveType) {

                                //Update and increment leave count
                                Leave += leave.value;
                                current_row.children('td#Leave').html(Leave);
                            }
                        });
                    }

                    //Calculate and update to absent count for staff
                    Absent = M_WD - (S_WD + Leave);
                    current_row.children('td#Absent').html(Absent);
                }
            });

            settings.progress(currentProgress);
        }

        settings.progress(100);
    };

}(jQuery));
