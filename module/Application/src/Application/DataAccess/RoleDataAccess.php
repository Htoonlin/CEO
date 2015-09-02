<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/19/2015
 * Time: 4:08 PM
 */

namespace Application\DataAccess;

use Application\Entity\Role;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class RoleDataAccess
 * @package Application\DataAccess
 */
class RoleDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = 'tbl_role';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Role());
        $this->initialize();
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        $results = $this->select();
        return $results;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getRole($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('roleId' => $id));
        $row = $rowset->current();
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
    public function getChildren($parentId = null, $parentName = "")
    {
        $results = $this->select(function (Select $select) use ($parentId){
            $select->where(array('parentId' => $parentId))->order(array('priority ASC'));
        });

        $resultList = array();
        foreach($results as $role)
        {
            $children = $this->getChildren($role->getRoleId(), $parentName);
            if(!empty($children)){
                $role->setChildren($children);
            }
            $resultList[] = $role;
        }
        return $resultList;
    }

    /**
     * @param Role $role
     * @return Role
     */
    public function saveRole(Role $role)
    {
        $id = $role->getRoleId();
        $data = $role->getArrayCopy();

        if($id > 0){
            $this->update($data, array('roleId' => $id));
        }else{
            unset($data['roleId']);
            $this->insert($data);
        }
        if(!$role->getRoleId()){
            $role->setRoleId($this->getLastInsertValue());
        }
        return $role;
    }

    /**
     * @param $id
     */
    public function deleteRole($id)
    {
        $results = $this->select(array("parentId" => $id));
        foreach($results as $role){
            $this->deleteRole($role->getRoleId());
        }
        $this->delete(array('roleId' => (int)$id));
    }
}