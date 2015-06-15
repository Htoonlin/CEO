<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 12:27 AM
 */

namespace Application\DataAccess;
use Application\Entity\MenuPermission;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;



class MenuPermissionDataAccess extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_menu_permission";
        $this->adapter = $dbAdapter;
        $this->initialize();
    }

    public function grantRoles($menuId)
    {
        return $this->select(array('menuId' => $menuId));
    }

    public function saveMenuPermission($menuId, array $roles)
    {
        $this->delete(array('menuId' => $menuId));
        foreach($roles as $roleId)
        {
            $data = array(
                'menuId' => $menuId,
                'roleId' => $roleId,
            );
            $this->insert($data);
        }
    }

    public function deleteRoles($menuId)
    {
        $this->delete(array('menuId' => $menuId));
    }
}