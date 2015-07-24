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

/**
 * Class ConstantDataAccess
 * @package Application\DataAccess
 */
class ConstantDataAccess extends SundewTableGateway {

    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table="tbl_constant";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Constant());
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false,$filter = '',$orderBy='name',$order='ASC')
    {
        if($paginated){
            return $this->paginate($filter, $orderBy, $order);
        }
        return $this->select();
    }

    /**
     * @param $name
     * @param string $group_code
     * @return array
     */
    public function getComboByName($name, $group_code = '')
    {
        $result = $this->getConstantByName($name, $group_code);
        if($result){
            return get_object_vars(json_decode($result->getValue()));
        }
        return array();
    }

    /**
     * @param $group_code
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getDataByGroupCode($group_code)
    {
        return $this->select(array('group_code' => $group_code));
    }

    /**
     * @param $name
     * @param string $group_code
     * @return Constant|array|\ArrayObject|null
     */
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

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public  function getConstant($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('constantId'=>$id));
        return $rowset->current();
    }

    /**
     * @param Constant $constant
     * @return Constant
     */
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

    /**
     * @param $id
     */
    public function deleteConstant($id)
    {
        $this->delete(array('constantId'=>(int)$id));
    }
}