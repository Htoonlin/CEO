<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 2/16/2015
 * Time: 5:21 PM
 */

namespace Application\DataAccess;


use Application\Entity\User;
use Application\Service\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class UserDataAccess extends SundewTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_user";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new User());
        $this->initialize();
    }
    public function fetchAll($paginated = false, $filter = '', $orderBy = 'userName', $order = 'ASC')
    {
        $view = 'vw_user';
        if($paginated){
            return $this->paginate($filter, $orderBy, $order, $view);
        }

        $userView = new TableGateway($view, $this->adapter);
        return $userView->select();
    }

    public function getComboData($key, $value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $user){
            $data=$user->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getUser($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('userId' => $id));
        return $rowset->current();
    }

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

    public function deleteUser($id)
    {

        $this->delete(array('userId' => (int)$id));
    }
}