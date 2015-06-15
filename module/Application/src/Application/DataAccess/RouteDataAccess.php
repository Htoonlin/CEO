<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/20/2015
 * Time: 5:45 PM
 */

namespace Application\DataAccess;

use Application\Entity\Route;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class RouteDataAccess  extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_route";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Route());
        $this->initialize();
    }

    public function getRouteData($roleId)
    {
        $results = $this->select(function(Select $select) use($roleId){
            $select->join(array('mp' => 'tbl_route_permission'), 'tbl_route.routeId = mp.routeId')
                ->where(array('mp.roleId' => $roleId));
        });

        return $results;
    }

    public function fetchAll($paginated=false,$filter = '',$orderBy='name',$order='ASC')
    {
        if($paginated){
            $select = new Select($this->table);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->literal("Concat_ws(' ',name, route, controller) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        return $this->select();
    }

    public function getRoute($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('routeId'=>$id));
        return $rowset->current();
    }
    public function saveRoute(Route $route)
    {
        $id=$route->getRouteId();
        $data=$route->getArrayCopy();
        if($id>0)
        {
            $this->update($data,array('routeId'=>$id));
        }else{
            unset($data['routeId']);
            $this->insert($data);
        }
        if(!$route->getRouteId())
        {
            $route->setRouteId($this->getLastInsertValue());
        }
        return $route;
    }
        public function deleteRoute($id)
        {
            $this->delete(array('routeId'=>(int)$id));
        }
}