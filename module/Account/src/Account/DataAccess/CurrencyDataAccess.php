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
use Application\Service\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;

/**
 * Class CurrencyDataAccess
 * @package Account\DataAccess
 */
class CurrencyDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table='tbl_account_currency';
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(), new Currency());
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false, $filter='', $orderBy='name', $order='ASC')
    {
        if($paginated){
            return $this->paginate($filter, $orderBy, $order);
        }
        return $this->select();

    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
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

    /**
     * @param $currency
     * @return array|\ArrayObject|null
     */
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

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function  getCurrency($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('currencyId'=>$id));
        $row=$rowset->current();
        
        return $row;
    }

    /**
     * @param Currency $currency
     * @return Currency
     */
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

    /**
     * @param $id
     */
    public function deleteCurrency($id)
    {
        $this->delete(array('currencyId'=>(int)$id));
    }
}