<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 1:19 PM
 */

namespace HumanResource\DataAccess;

use Application\Service\SundewTableGateway;
use HumanResource\Entity\Leave;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class LeaveDataAccess extends SundewTableGateway
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
            return $this->paginate($filter, $orderBy, $order, $view);
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