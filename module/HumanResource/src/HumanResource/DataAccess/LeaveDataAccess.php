<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 1:19 PM
 */

namespace HumanResource\DataAccess;

use HumanResource\Entity\Leave;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class LeaveDataAccess extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter){
        $this->table = 'tbl_hr_leave';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Leave());
        $this->initialize();
    }

    public function fetchAll($paginated=false,$filter='',$orderBy='date', $order='DESC')
    {
        $view = 'vw_hr_leave';
        if($paginated){
            $select = new Select($view);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->literal("CONCAT_WS(' ', status, staffCode, staffname, date, leaveType) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            return new Paginator($paginatorAdapter);
        }
        $tableGateway = new TableGateway($view, $this->adapter);
        return $tableGateway->select();
    }
    public function getLeave($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('leaveId' => $id));
        return $rowset->current();
    }
    public function getLeaveByStaff($staffId, $date)
    {
        $rowset = $this->select(array('staffId' => $staffId, 'date' => $date));
        return $rowset->current();
    }
    public function saveLeave(Leave $leave)
    {
        $id = $leave->getLeaveId();
        $data = $leave->getArrayCopy();
        if(empty($data['status']))
            $data['status'] = 'A';

        if($id > 0){
            $this->update($data, array('leaveId' => $id));
        }else{
            unset($data['leaveId']);
            $this->insert($data);
        }

        if(!$leave->getLeaveId()){
            $leave->setLeaveId($this->lastInsertValue);
        }

        return $leave;
    }
}