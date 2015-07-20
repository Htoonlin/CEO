<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:12 AM
 */

namespace HumanResource\DataAccess;

use Application\Service\SundewTableGateway;
use HumanResource\Entity\Position;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Paginator;

/**
 * Class PositionDataAccess
 * @package HumanResource\DataAccess
 */
class PositionDataAccess extends SundewTableGateway{

    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table="tbl_hr_position";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Position());
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
    public function fetchAll($paginated=false,$filter='',$orderBy='name',$order='ASC')
    {
        $view = 'vw_hr_position';
        if($paginated){
            return $this->paginate($filter, $orderBy, $order, $view);
        }
        $tableGateway = new TableGateway($view, $this->adapter);
        return $tableGateway->select();
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
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

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getPosition($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('positionId'=>$id));
        return $rowset->current();
    }

    /**
     * @param Position $position
     * @return Position
     */
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

    /**
     * @param $id
     */
    public function deletePosition($id)
    {
        $this->delete(array('positionId'=>(int)$id));
    }

}