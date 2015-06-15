<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 1:08 PM
 */

namespace Application\DataAccess;

use Application\Entity\Constant;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class ConstantDataAccess extends AbstractTableGateway {

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
            $select = new Select($this->table);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->literal("Concat_ws(' ',name, value, group_code) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        return $this->select();
    }
    public function getComboByGroupCode($group_code)
    {
        $results = $this->getDataByGroupCode($group_code);
        $selectData = array();
        foreach($results as $role){
            $data = $role->getArrayCopy();
            $selectData[$data['value']] = $data['name'];
        }

        return $selectData;
    }
    public function getDataByGroupCode($group_code)
    {
        return $this->select(array('group_code' => $group_code));
    }
    public function getConstantByName($name, $group_code = '')
    {
        if(empty($group_code))
        {
            return $this->select(array('name' => $name, 'group_code' => $group_code));
        }
        return $this->select(array('name' => $name));
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