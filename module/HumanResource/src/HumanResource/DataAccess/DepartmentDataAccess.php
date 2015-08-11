<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:12 AM
 */

namespace HumanResource\DataAccess;

use Application\Service\SundewTableGateway;
use HumanResource\Entity\Department;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class DepartmentDataAccess
 * @package HumanResource\DataAccess
 */
class DepartmentDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table='tbl_hr_department';
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Department());
        $this->initialize();
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function fetchAll()
    {
        $results=$this->select();
        return $results;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getDepartment($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('departmentId'=>$id));
        $row=$rowset->current();
        if(!$row){
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param null $parentId
     * @param string $parentName
     * @return array
     */
    public function getChildren($parentId=null,$parentName="")
    {
        $results=$this->select(function (Select $select) use ($parentId){
            $select->where(array('parentId'=>$parentId))->order(array('priority ASC'));
        });
        $resultList=array();
        foreach($results as $department)
        {
            $children=$this->getChildren($department->getDepartmentId(),$parentName);
            if(!empty($children)){
                $department->setChildren($children);
            }
            $resultList[]=$department;
        }
        return $resultList;
    }

    /**
     * @param Department $department
     * @return Department
     */
    public function saveDepartment(Department $department)
    {
        $id=$department->getDepartmentId();
        $data=$department->getArrayCopy();
        $data['status']='A';
        if($id>0){
            $this->update($data,array('departmentId'=>$id));
        }else{
            unset($data['departmentId']);
            $this->insert($data);
        }
        if(!$department->getDepartmentId()){
            $department->setDepartmentId($this->getLastInsertValue());
        }
        return $department;
    }

    /**
     * @param $id
     */
    public function deleteDepartment($id)
    {
        $results=$this->select(array("parentId"=>$id));
        foreach($results as $department){
            $this->deleteDepartment($department->getDepartmentId());
        }
        $this->delete(array('departmentId'=>(int)$id));
    }
}