<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:08 AM
 */

namespace HumanResource\DataAccess;

use Core\SundewTableGateway;
use HumanResource\Entity\Attendance;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class AttendanceDataAccess
 * @package HumanResource\DataAccess
 */
class AttendanceDataAccess extends SundewTableGateway{

    protected $boardView = 'vw_hr_attendance';

    /**
     * @param Adapter $dpAdapter
     */
    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_attendance";
        $this->adapter=$dpAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Attendance());
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false, $filter='',$orderBy='attendanceDate',$order='DESC')
    {
        if($paginated)
        {
            return $this->paginate($filter, $orderBy, $order, $this->boardView);
        }

        $attendanceBoard = new TableGateway($this->boardView, $this->adapter);
        return $attendanceBoard->select();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getAttendance($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('attendanceId'=>$id));
        return $rowset->current();
    }

    /**
     * @param $staffId
     * @param $date
     * @return array|\ArrayObject|null
     */
    public function getAttendanceByStaff($staffId, $date){
        $rowset = $this->select(array('staffId' => $staffId, 'attendanceDate' => $date));
        return $rowset->current();
    }

    /**
     * @param $staffId
     * @param $date
     * @return array|\ArrayObject|null
     */
    public function checkAttendance($staffId, $date)
    {
        $results = $this->select(function (Select $select) use($staffId, $date){
            $where = new Where();
            $where->equalTo('staffId',$staffId)
                ->AND->literal('attendanceDate = ?', $date);
            $select->where($where);
        });
        return $results->current();
    }


    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getWorkHours($from, $to){
        $gateWay = new TableGateway($this->boardView, $this->adapter);
        $results = $gateWay->select(function(Select $select) use ($from, $to){
            $where = new Where();
            $where->between('attendanceDate',$from, $to)
                ->and->isNotNull('inTime')
                ->and->isNotNull('outTime');
            $select->columns(array('staffId', 'staffCode', 'staffName',
                'year' => new Expression('YEAR(attendanceDate)'),
                'month' => new Expression('MONTH(attendanceDate)'),
                'hours' => new Expression("SUM(TIMEDIFF(IF(outTime > '18:00:00', '18:00:00', outTime),inTime) / 3600)")
            ))->where($where)
                ->group(array('staffId',
                    new Expression('YEAR(attendanceDate)'),
                    new Expression('MONTH(attendanceDate)')
                ))->order('staffId, year, month');
        });
        return $results;
    }


    /**
     * @param Attendance $attendance
     * @return Attendance
     */
    public function saveAttendance(Attendance $attendance)
    {
        $id = $attendance->getAttendanceId();
        $data = $attendance->getArrayCopy();

        if ($id > 0) {
            $this->update($data, Array('attendanceId' => $id));
        } else {
            unset($data['attendanceId']);
            $this->insert($data);
        }
        if (!$attendance->getAttendanceId()) {
            $attendance->setAttendanceId($this->getLastInsertValue());
        }
        return $attendance;
    }
}