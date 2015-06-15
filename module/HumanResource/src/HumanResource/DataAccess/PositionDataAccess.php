<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:12 AM
 */

namespace HumanResource\DataAccess;

use HumanResource\Entity\Position;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class PositionDataAccess extends AbstractTableGateway{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table="tbl_hr_position";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Position());
        $this->initialize();
    }


    public function fetchAll($paginated=false,$filter='',$orderBy='name',$order='ASC')
    {

        if($paginated){
            $select = new Select($this->table);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->literal("Concat_ws(' ',name, max_salary, min_salary) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;

        }
        return $this->select();

    }

    public function getComboData($key, $value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $position){
            $data=$position->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getPosition($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('positionId'=>$id));
        return $rowset->current();
    }
    public function savePosition(Position $position)
    {
        $id=$position->getPositionId();
        $data=$position->getArrayCopy();

        if($id>0){
            $this->update($data,array('positionId'=>$id));
        }
        else{
            unset($data['positionId']);

            $this->insert($data);
        }
        if(!$position->getPositionId())
        {
            $position->setPositionId($this->getLastInsertValue());
        }
        return $position;
    }
    public function deletePosition($id)
    {
        $this->delete(array('positionId'=>(int)$id));
    }

}