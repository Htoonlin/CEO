<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/20/2015
 * Time: 5:45 PM
 */

namespace Application\DataAccess;

use Application\Entity\Route;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class RouteDataAccess
 * @package Application\DataAccess
 */
class RouteDataAccess  extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table = "tbl_route";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Route());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param array $roles
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getRouteData(array $roles)
    {
        $results = $this->select(function(Select $select) use($roles){
            $where = new Where();
            if(!empty($roles)){
                $where->in('rp.roleId', $roles);
            }else{
                $where->expression(' 1 = ?', 0);
            }
            $select->join(array('rp' => 'tbl_route_permission'), 'tbl_route.routeId = rp.routeId',
                array('routeId'), Select::JOIN_INNER)
                ->where($where)->quantifier(Select::QUANTIFIER_DISTINCT);
        });

        return $results;
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
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getRoute($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('routeId'=>$id));
        return $rowset->current();
    }

    /**
     * @param Route $route
     * @return Route
     */
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

    /**
     * @param $id
     */
    public function deleteRoute($id)
    {
        $this->delete(array('routeId'=>(int)$id));
    }
}