<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 2/16/2015
 * Time: 5:21 PM
 */

namespace Application\DataAccess;


use Application\Entity\User;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class UserDataAccess
 * @package Application\DataAccess
 */
class UserDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table = "tbl_user";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new User());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated = false, $filter = '', $orderBy = 'userName', $order = 'ASC')
    {
        $view = 'vw_user';
        if($paginated){
            return $this->paginate($filter, $orderBy, $order, $view);
        }

        $select = new Select($view);
        return $this->selectOther($select);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getUser($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('userId' => $id));
        return $rowset->current();
    }

    /**
     * @param User $user
     * @return User
     */
    public function saveUser(User $user)
    {
        $id = $user->getUserId();
        $data = $user->getArrayCopy();

        if(is_array($user->getImage())){
            $data['image'] = $user->getImage()['tmp_name'];
        }

        if($id > 0){
            $this->update($data, array('userId' => $id));
        }else{
            unset($data['userId']);
            $this->insert($data);
        }

        if(!$user->getUserId())
        {
            $user->setUserId($this->getLastInsertValue());
        }

        return $user;
    }

    /**
     * @param $id
     */
    public function deleteUser($id)
    {

        $this->delete(array('userId' => (int)$id));
    }
}