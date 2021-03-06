<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/5/2015
 * Time: 4:32 PM
 */

namespace Application\DataAccess;

use Application\Entity\Calendar;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class CalendarType
 * @package Application\DataAccess
 */
abstract class CalendarType
{
    const holiday = 'holiday';
    const holiday_yearly = 'holiday_y';
    const holiday_monthly = 'holiday_m';
    const holiday_weekly = 'holiday_w';
}

/**
 * Class CalendarDataAccess
 * @package Application\DataAccess
 */
class CalendarDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table = 'tbl_calendar';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Calendar());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param Calendar $calendar
     * @return Calendar
     */
    public function saveCalendar(Calendar $calendar)
    {
        $id = $calendar->getCalendarId();
        $data = $calendar->getArrayCopy();
        if($id > 0){
            $this->update($data, array('calendarId' => $id));
        }else{
            unset($data['calendarId']);
            $this->insert($data);
        }

        if(!$calendar->getCalendarId()){
            $calendar->setCalendarId($this->getLastInsertValue());
        }

        return $calendar;
    }

    /**
     * @param $date
     * @return bool
     */
    public function checkHoliday($date)
    {
        $day = (int)date('d', strtotime($date));
        $month = (int)date('m', strtotime($date));
        $year = (int)date('Y', strtotime($date));
        $dow = (int)date('w', strtotime($date));

        //Check holiday
        $holidays = $this->select(array(
            'type' => CalendarType::holiday,
            'day' => $day,
            'month' => $month,
            'year' => $year
        ));
        if(count($holidays) > 0) return true;

        //Check weekly holiday
        $holidays = $this->select(array(
            'type' => CalendarType::holiday_weekly,
            'day' => $dow + 1
        ));
        if(count($holidays) > 0) return true;

        //Check yearly holiday
        $holidays = $this->select(array(
            'type' => CalendarType::holiday_yearly,
            'day' => $day,
            'month' => $month
        ));
        if(count($holidays) > 0) return true;

        //Check monthly holiday
        $holidays = $this->select(array(
            'type' => CalendarType::holiday_monthly,
            'day' => $day,
        ));
        if(count($holidays) > 0) return true;

        return false;
    }

    /**
     * @param $type
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getCalendarByType($type)
    {
        return $this->select(array('type' => $type));
    }

    /**
     * @param $year
     * @return array
     */
    public function getHolidayByYear($year)
    {
        $result = array();

        $yearly = $this->select(function (Select $select) {
            $where = new Where();
            $where->equalTo('type', CalendarType::holiday_yearly);
            $select->where($where)
                ->order('month asc, day asc');
        });

        foreach ($yearly as $calendar) {
            $calendar->setYear($year);
            $result[] = array(
                'id' => $calendar->getCalendarId(),
                'date' => $calendar->getDateString(),
                'title' => $calendar->getTitle(),
                'type' => CalendarType::holiday_yearly
            );
        }

        $monthly = $this->select(function (Select $select) {
            $where = new Where();
            $where->equalTo('type', CalendarType::holiday_monthly);
            $select->where($where)
                ->order('month asc, day asc');
        });

        foreach ($monthly as $calendar) {
            for ($m = 1; $m <= 12; $m++) {
                $calendar->setYear($year);
                $calendar->setMonth($m);
                $result[] = array(
                    'id' => $calendar->getCalendarId(),
                    'date' => $calendar->getDateString(),
                    'title' => $calendar->getTitle(),
                    'type' => CalendarType::holiday_monthly
                );
            }
        }

        $holiday = $this->select(array(
            'type' => CalendarType::holiday,
            'year' => $year));

        foreach ($holiday as $calendar) {
            $result[] = array(
                'id' => $calendar->getCalendarId(),
                'date' => $calendar->getDateString(),
                'title' => $calendar->getTitle(),
                'type' => CalendarType::holiday
            );
        }

        return $result;
    }
}