<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 12:27 AM
 */

namespace Application\DataAccess;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;


/**
 * Class MenuPermissionDataAccess
 * @package Application\DataAccess
 */
class MenuPermissionDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_menu_permission";
        $this->adapter = $dbAdapter;
        $this->initialize();
    }

    /**
     * @param $menuId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function grantRoles($menuId)
    {
        return $this->select(array('menuId' => $menuId));
    }

    /**
     * @param $menuId
     * @param array $roles
     */
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

    /**
     * @param $menuId
     */
    public function deleteRoles($menuId)
    {
        $this->delete(array('menuId' => $menuId));
    }
}