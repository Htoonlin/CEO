<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:13 AM
 */

namespace HumanResource\DataAccess;

use HumanResource\Entity\Staff;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class StaffDataAccess extends AbstractTableGateway {
    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_staff";
        $this->adapter=$dpAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Staff());
        $this->initialize();
    }
    public function fetchAll($paginated=false, $filter='', $orderBy='staffName', $order='ASC')
    {
        $view='vw_hr_staff';
        if($paginated)
        {
            $select=new Select($view);
            $select->order($orderBy .' '. $order);
            $where=new Where();
            $where->literal("Concat_ws(' ',staffCode, staffName, UserName, Position, Department, Salary, Currency) LIKE ?", '%'.$filter.'%');
            $select->where($where);
            $paginatorAdapter=new DbSelect($select, $this->adapter);
            $paginator=new Paginator($paginatorAdapter);
            return $paginator;
        }
        $staffView=new TableGateway($view, $this->adapter);
        return $staffView->select();
    }
    public function getComboData($key, $value)
    {
        $data = $this->select();
        $result = array();
        foreach($data as $staff)
        {
            $staff = $staff->getArrayCopy();
            $result[$staff[$key]] = $staff[$value];
        }
        return $result;
    }
    public function getStaffByUser($userId)
    {
        $id=(int)$userId;
        $rowset = $this->select(array('userId' => $id));
        return $rowset->current();
    }
    public function getStaff($id)
    {
        $id=(int)$id;
        $rowset = $this->select(array('staffId' => $id));
        return $rowset->current();
    }
    public function saveStaff(Staff $staff)
    {
        $id = $staff->getStaffId();
        $data = $staff->getArrayCopy();
        if ($id > 0) {
            $this->update($data, Array('staffId' => $id));
        } else {
            unset($data['staffId']);
            $this->insert($data);
        }
        if (!$staff->getStaffId()) {
            $staff->setStaffId($this->getLastInsertValue());
        }
        return $staff;
    }
    public function deleteStaff($id)
    {
        $this->delete(array('staffId'=>(int)$id));
    }


}