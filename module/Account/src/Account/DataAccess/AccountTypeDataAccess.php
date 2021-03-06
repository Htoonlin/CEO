<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 11:52 AM
 */

namespace Account\DataAccess;

use Account\Entity\AccountType;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class AccountTypeDataAccess
 * @package Account\DataAccess
 */
class AccountTypeDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table = 'tbl_account_type';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new AccountType());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
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
    public function getAccountType($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('accountTypeId' => $id));
        $row = $rowset->current();
        if(!$row){
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     * @param null $parentId
     * @param string $parentName
     * @param string $type
     * @return array
     */
    public function getChildren($type = '', $parentId = null, $parentName = "")
    {
        $results = $this->select(function (Select $select) use ($parentId, $type){
            if(empty($type)){
                $select->where(array('parentTypeId' => $parentId));
            }else{
                $where = new Where();
                if(empty($parentId)){
                    $where->isNull('parentTypeId');
                }else{
                    $where->equalTo('parentTypeId', $parentId);
                }
                $where->in('baseType', array('B', $type));
                $select->where($where);
            }
        });

        $resultList = array();
        foreach($results as $accountType)
        {
            $children = $this->getChildren($type, $accountType->getAccountTypeId(), $parentName);
            if(!empty($children)){
                $accountType->setChildren($children);
            }
            $resultList[] = $accountType;
        }
        return $resultList;
    }

    /**
     * @param AccountType $accountType
     * @return AccountType
     */
    public function saveAccountType(AccountType $accountType)
    {
        $id = $accountType->getAccountTypeId();
        $data = $accountType->getArrayCopy();

        if($id > 0){
            $this->update($data, array('accountTypeId' => $id));
        }else{
            unset($data['accountTypeId']);
            $this->insert($data);
        }
        if(!$accountType->getAccountTypeId()){
            $accountType->setAccountTypeId($this->getLastInsertValue());
        }
        return $accountType;
    }

    /**
     * @param $id
     */
    public function deleteAccountType($id)
    {
        $results = $this->select(array("parentTypeId" => $id));
        foreach($results as $accountType){
            $accountType->setParentTypeId(null);
            $data = $accountType->getArrayCopy();
            $this->update($data, array('accountTypeId' => $id));
        }
        $this->delete(array('accountTypeId' => (int)$id));
    }
}