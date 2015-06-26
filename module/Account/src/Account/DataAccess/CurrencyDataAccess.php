<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/23/2015
 * Time: 4:25 PM
 */

namespace Account\DataAccess;

use Account\Entity\AccountType;
use Account\Entity\Currency;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;

class CurrencyDataAccess extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table='tbl_account_currency';
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(), new Currency());
        $this->initialize();
    }

    public function fetchAll($paginated=false, $filter='', $orderBy='name', $order='ASC')
    {
        if($paginated){
            $select=new Select($this->table);
            $select->order($orderBy. ' '. $order);
            $where=new Where();
            $where->literal("Concat_ws(' ',code, name) LIKE ?", '%'.$filter.'%');
            $select->where($where);
            $paginatorAdapter=new DbSelect($select, $this->adapter);
            $paginator=new Paginator($paginatorAdapter);
            return $paginator;

        }
        return $this->select();

    }

    public function getComboData($key, $value)
    {
        $results = $this->select(array('status' => 'A'));
        $selectData=array();
        foreach($results as $currency){
            $data=$currency->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    public function getLastCurrency($currency){
        $results = $this->select(function (Select $select) use ($currency){
            $where = new Where();
            $where->equalTo('code', $currency)
                ->AND->equalTo('status','A');
            $select->where($where)
                ->order('entryDate DESC')
                ->limit(1);
        });

        return $results->current();
    }

    public function  getCurrency($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('currencyId'=>$id));
        $row=$rowset->current();
        
        return $row;
    }

    public function saveCurrency(Currency $currency)
    {
        $id=$currency->getCurrencyId();
        $data=$currency->getArrayCopy();

        if($id>0){
            $this->update($data, array('currencyId'=>$id));
        }else{
            unset($data['currencyId']);
            $this->insert($data);
        }
        if(!$currency->getCurrencyId()){
            $currency->setCurrencyId($this->getLastInsertValue());
        }
        return $currency;
    }

    public function deleteCurrency($id)
    {

        $this->delete(array('currencyId'=>(int)$id));
    }
}