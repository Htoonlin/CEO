<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:08 AM
 */

namespace HumanResource\DataAccess;

use HumanResource\Entity\Attendance;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class AttendanceDataAccess extends AbstractTableGateway{

    protected $boardView = 'vw_hr_attendance';

    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_Attendance";
        $this->adapter=$dpAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Attendance());
        $this->initialize();
    }
    public function getComboData($key, $value)
    {
        $results = $this->select();
        $selectData = array();
        foreach($results as $staff)
        {
            $data = $staff->getArrayCopy();
            $selectData[$data[$key]] = $data[$value];
        }

        return $selectData;
    }
    public function fetchAll($paginated=false, $filter='',$orderBy='attendanceDate',$order='DESC')
    {
        if($paginated)
        {
            $select = new Select($this->boardView);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->literal("concat_ws(' ', StaffCode, StaffName, attendanceDate) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $attendanceBoard = new TableGateway($this->boardView, $this->adapter);
        return $attendanceBoard->select();
    }
    public function getAttendance($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('attendanceId'=>$id));
        return $rowset->current();
    }
    public function getAttendanceByStaff($staffId, $date){
        $rowset = $this->select(array('staffId' => $staffId, 'attendanceDate' => $date));
        return $rowset->current();
    }
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