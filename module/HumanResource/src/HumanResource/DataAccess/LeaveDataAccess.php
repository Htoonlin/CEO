<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 1:19 PM
 */

namespace HumanResource\DataAccess;

use Core\SundewTableGateway;
use HumanResource\Entity\Leave;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class LeaveDataAccess
 * @package HumanResource\DataAccess
 */
class LeaveDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId){
        $this->table = 'tbl_hr_leave';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Leave());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false,$filter='',$orderBy='date', $order='DESC')
    {
        $view = 'vw_hr_leave';
        if($paginated){
            return $this->paginate($filter, $orderBy, $order, $view);
        }
        $select = new Select($view);
        return $this->selectOther($select);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getLeave($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('leaveId' => $id));
        return $rowset->current();
    }

    /**
     * @param $staffId
     * @param $date
     * @return array|\ArrayObject|null
     */
    public function getLeaveByStaff($staffId, $date)
    {
        $rowset = $this->select(array('staffId' => $staffId, 'date' => $date));
        return $rowset->current();
    }

    /**
     * @param Leave $leave
     * @return Leave
     */
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