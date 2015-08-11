<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:13 AM
 */

namespace HumanResource\DataAccess;

use Application\Service\SundewTableGateway;
use HumanResource\Entity\Staff;
use PhpOffice\PhpWord\Exception\Exception;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class StaffDataAccess
 * @package HumanResource\DataAccess
 */
class StaffDataAccess extends SundewTableGateway {
    protected $view = 'vw_hr_staff';

    /**
     * @param Adapter $dpAdapter
     */
    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_staff";
        $this->adapter=$dpAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Staff());
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
    public function fetchAll($paginated=false, $filter='', $orderBy='staffName', $order='ASC')
    {
        if($paginated)
        {
            return $this->paginate($filter, $orderBy, $order, $this->view);
        }

        $staffView=new TableGateway($this->view, $this->adapter);
        return $staffView->select();
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getActiveStaffs(){
        $staffView=new TableGateway($this->view, $this->adapter);
        return $staffView->select(array('Status' => 'A'));
    }

    /**
     * @param $userId
     * @return array|\ArrayObject|null
     * @throws Exception
     */
    public function getStaffByUser($userId)
    {
        $id=(int)$userId;
        $rowset = $this->select(array('userId' => $id));
        if(!$rowset){
            throw new Exception('Invalid userId for staff.');
        }
        return $rowset->current();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getStaff($id)
    {
        $id=(int)$id;
        $rowset = $this->select(array('staffId' => $id));
        return $rowset->current();
    }

    /**
     * @param Staff $staff
     * @return Staff
     */
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

    /**
     * @param $leave
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function updateLeave($leave, $id)
    {
        $staff = $this->getStaff($id);
        if($staff){
            $currentLeave = $staff->getAnnualLeave();
            $staff->setAnnualLeave($currentLeave - $leave);
            $this->update($staff->getArrayCopy(), Array('staffId' => $id));
        }

        return $staff;
    }

    /**
     * @param $id
     */
    public function deleteStaff($id)
    {
        $this->delete(array('staffId'=>(int)$id));
    }
}