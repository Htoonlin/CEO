<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/21/2015
 * Time: 11:06 AM
 */

namespace Application\DataAccess;

use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;

/**
 * Class UserRoleDataAccess
 * @package Application\DataAccess
 */
class UserRoleDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_user_role";
        $this->adapter = $dbAdapter;
        $this->initialize();
    }

    /**
     * @param $userId
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function grantRoles($userId)
    {
        return $this->select(array('userId' => $userId));
    }

    /**
     * @param $userId
     * @param array $roles
     */
    public function saveRoles($userId, array $roles)
    {
        $this->delete(array('userId' => $userId));
        foreach($roles as $roleId)
        {
            $data = array(
                'userId' => $userId,
                'roleId' => $roleId,
            );
            $this->insert($data);
        }
    }

    /**
     * @param $userId
     */
    public function deleteRoles($userId)
    {
        $this->delete(array('userId' => $userId));
    }
}