<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/5/2015
 * Time: 11:52 AM
 */

namespace Account\DataAccess;

use Account\Entity\AccountType;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class AccountTypeDataAccess extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = 'tbl_account_type';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new AccountType());
        $this->initialize();
    }

    public function fetchAll()
    {
        $results = $this->select();
        return $results;
    }

    public function getComboData($key, $value, $type)
    {
        $results = $this->select(function(Select $select) use ($type){
            $where = new Where();
            $where->in('baseType', array('B', $type))
                ->and->equalTo('status', 'A');
            $select->where($where)
                ->order('name asc');
        });
        $selectData = array();
        foreach($results as $accountType){
            $data = $accountType->getArrayCopy();
            $selectData[$data[$key]] = $data[$value];
        }

        return $selectData;
    }

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

    public function getChildren($parentId = null, $parentName = "")
    {
        $results = $this->select(function (Select $select) use ($parentId){
            $select->where(array('parentTypeId' => $parentId));
        });

        $resultList = array();
        foreach($results as $accountType)
        {
            $children = $this->getChildren($accountType->getAccountTypeId(), $parentName);
            if(!empty($children)){
                $accountType->setChildren($children);
            }
            $resultList[] = $accountType;
        }
        return $resultList;
    }

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