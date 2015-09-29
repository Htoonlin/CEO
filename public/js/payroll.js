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

    var workHours;

    var payroll_data = {
        lateList        : {},
        leaveValues     : {}
    };

    var payroll_url = {
        attendance  : '',
        leave       : '',
        holiday     : '',
        payroll     : ''
    };

    $.setPayrollData = function(options){
        payroll_data = options;
    };

    $.setPayrollUrl = function(options){
        payroll_url = options;
    };

    function checkHoliday(date){
        var isHoliday = false;
        $.ajax({
            'url': payroll_url.holiday,
            'data': {date: date.format('YYYY-MM-DD')},
            'type': 'get',
            'async': false,
            'success': function (response) {
                isHoliday = response.data.isHoliday;
            }
        });
        return isHoliday;
    }

    function getAttendance(attendance, dayOfWeek){
        var full_day = workHours[dayOfWeek];

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
                    $(ptr).html(currentLate + 1);
                    hasLate = true;
                }

                var late_to = parseTimeToMinute(full_day.to) - late.minute;
                if(out_time < late_to){
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

        try{
            var staffId = $(this).attr('data-id');
            var salary = $(this).attr('data-salary');
            workHours = JSON.parse($(this).attr('data-workHours'));
        }catch(ex){
            throw ex;
        }

        if(staffId == 0) return;

        var current_row = $(this);

        current_row.find('td#M_WD').html(0);
        current_row.find('td#S_WD').html(0);
        current_row.find('td#Leave').html(0);
        current_row.find('td#Absent').html(0);
        current_row.find('td#Per_Day').html(0);
        $.each(payroll_data.lateList, function(idx, late){
            current_row.find('td#' + late.code + '-' + staffId).html(0);
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
        settings.end.add(1, 'day');
        //Loop Start to End by day
        for(var d = settings.start; d < settings.end; d.add(1, 'day')) {
            currentProgress += increment;
            var dayOff = !workHours[d.weekday()];
            //Check this date is holiday
            if (checkHoliday(d) || dayOff) {
                //Increment to holiday count
                console.log(d.format('DD-MM') + '=> Dayoff');
                Holiday++;
                continue;
            }


            //Increment to monthly working day
            current_row.find('td#M_WD').html(++M_WD);

            var hasLeave = false;

            //Check leave by staff and date
            $.ajax({
                'url': payroll_url.leave,
                'data': {date: d.format('YYYY-MM-DD'), staffId: staffId},
                'type': 'get',
                'async': false,
                'success': function (response) {
                    var data = response.data;
                    $.each(payroll_data.leaveValues, function (idx, leave) {
                        if (leave.id == data.leaveType) {

                            //Update and increment leave count
                            Leave += leave.value;
                            current_row.find('td#Leave').html(Leave);

                            hasLeave = (leave.value == 1);
                        }
                    });
                }
            });

            if(!hasLeave){
                //Check attendance by staff and date
                $.ajax({
                    'url': payroll_url.attendance,
                    'data': {date: d.format('YYYY-MM-DD'), staffId: staffId},
                    'type': 'get',
                    'async': false,
                    'success': function (response) {
                        //Validate late minutes and update Staff Working Day
                        S_WD += getAttendance(response.data, d.weekday());
                        current_row.find('td#S_WD').html(S_WD);
                    }
                });
            }

            //Calculate and update to absent count for staff
            Absent = M_WD - (S_WD + Leave);
            console.log(d.format('DD-MM') + '=> ' + M_WD + '-(' + S_WD + '+' + Leave + ')');
            current_row.find('td#Absent').html(Absent);

            settings.progress(currentProgress);
        }

        current_row.find('td#Per_Day').html(math.round(salary / M_WD, 2));
        settings.progress(100);
    };

    //Calculate payroll by row cell
    $.fn.calculatePayroll = function(options){
        var default_var = {
            S: parseFloat($(this).attr('data-salary')),
            M: parseFloat($(this).find('td#M_WD').html()),
            P: parseFloat($(this).find('td#Per_Day').html()),
            W: parseFloat($(this).find('td#S_WD').html()),
            L: parseFloat($(this).find('td#Leave').html()),
            A: parseFloat($(this).find('td#Absent').html())
        };

        var staffId = $(this).attr('data-id');
        $.each(payroll_data.lateList, function(idx, late){
            var ptr = '#' + late.code + '-' + staffId;
            var lateCount = parseInt($(ptr).html());
            default_var[late.code] = lateCount;
        });

        var settings = $.extend({
            formula : '(S*(W + L)) - ((S * A)',
            staff : staffId,
            start: moment(),
            end: moment().subtract(1, 'month'),
            success: function(salary){
                console.log('Salary for ' + settings.staff + ' => ' + salary);
            }
        }, options);

        var result = math.eval(settings.formula, default_var);
        settings.success(result);
    };

    //Save payroll To Database
    $.fn.savePayroll = function(options){

        var lateList = {};
        var staffId = $(this).attr('data-id');
        var salary = parseFloat($(this).attr('data-salary'));
        var m_wd = parseFloat($(this).find('td#M_WD').html());
        var s_wd = parseFloat($(this).find('td#S_WD').html());
        var leave = parseFloat($(this).find('td#Leave').html());
        var absent = parseFloat($(this).find('td#Absent').html());

        $.each(payroll_data.lateList, function(idx, late){
            var ptr = '#' + late.code + '-' + staffId;
            var lateCount = parseInt($(ptr).html());
            lateList[late.code] = parseInt(lateCount);
        });

        var settings = $.extend({
            formula : '(S*(W + L)) - ((S * A)',
            start: moment(),
            end: moment().subtract(1, 'month'),
            success: function(data){
                console.log(data);
            }
        }, options);

        $.ajax({
            'url': payroll_url.payroll,
            'data': {
                staffId: staffId,
                fromDate:settings.start.format('YYYY-MM-DD'),
                toDate:settings.end.format('YYYY-MM-DD'),
                m_wd:m_wd,
                s_wd:s_wd,
                salary:salary,
                leave:leave,
                absent:absent,
                formula:settings.formula,
                late:lateList
            },
            'type': 'POST',
            'async': true,
            success: function(data){
                settings.success(data);
            }
        });
    }

}(jQuery));
