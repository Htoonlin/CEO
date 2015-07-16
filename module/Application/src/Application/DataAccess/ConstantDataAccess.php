<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 1:08 PM
 */

namespace Application\DataAccess;

use Application\Entity\Constant;
use Application\Service\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class ConstantDataAccess extends SundewTableGateway {

    public function __construct(Adapter $dbAdapter)
    {
        $this->table="tbl_constant";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Constant());
        $this->initialize();
    }
    public function fetchAll($paginated=false,$filter = '',$orderBy='name',$order='ASC')
    {
        if($paginated){
            return $this->paginate($filter, $orderBy, $order);
        }
        return $this->select();
    }
    public function getComboByName($name, $group_code = '')
    {
        $result = $this->getConstantByName($name, $group_code);
        if($result){
            return get_object_vars(json_decode($result->getValue()));
        }
        return array();
    }
    public function getDataByGroupCode($group_code)
    {
        return $this->select(array('group_code' => $group_code));
    }
    public function getConstantByName($name, $group_code = '')
    {
        if(!empty($group_code))
        {
            $result = $this->select(array('name' => $name, 'group_code' => $group_code));
        }else{
            $result = $this->select(array('name' => $name));
        }
        return $result->current();
    }
    public  function getConstant($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('constantId'=>$id));
        return $rowset->current();
    }
    public function saveConstant(Constant $constant)
    {
        $id=$constant->getConstantId();
        $data=$constant->getArrayCopy();

        if($id>0){
            $this->update($data,array('constantId'=>$id));
        }else{
            unset($data['constantId']);
            $this->insert($data);
        }
        if(!$constant->getConstantId())
        {
            $constant->setConstantId($this->getLastInsertValue());
        }
        return $constant;
    }

    public function deleteConstant($id)
    {
        $this->delete(array('constantId'=>(int)$id));
    }
}