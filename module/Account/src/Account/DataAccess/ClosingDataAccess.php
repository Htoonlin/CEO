<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/22/2015
 * Time: 5:31 PM
 */

namespace Account\DataAccess;


use Account\Entity\Closing;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

class ClosingDataAccess extends AbstractTableGateway
{
    private $view = 'vw_account_closing';
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = 'tbl_account_closing';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Closing());
        $this->initialize();
    }

    public function fetchAll($paginated=false,$currency = 0, $orderBy = 'openingDate', $order='desc')
    {
        if($paginated){
            $select=new Select($this->view);
            $select->order($orderBy . ' ' . $order);
            if($currency > 0){
                $where=new Where();
                $where->equalTo('currencyId', $currency);
                $select->where($where);
            }
            $paginatorAdapter=new DbSelect($select, $this->adapter);
            $paginator=new Paginator($paginatorAdapter);
            return $paginator;

        }
        $tableGateway = new TableGateway($this->view, $this->adapter);
        return $tableGateway->select();
    }

    public function getClosing($id)
    {
        $result = $this->select(array('closingId' => $id));

        if(!$result){
            throw new \Exception('Invalid closing id.');
        }

        return $result->current();
    }

    public function saveClosing(Closing $closing)
    {
        $id = $closing->getClosingId();
        $data = $closing->getArrayCopy();

        if($id > 0){
            $this->update($data, array('closingId' => $id));
        }else{
            unset($data['closingId']);
            $this->insert($data);
        }

        if(!$closing->getClosingId()){
            $closing->setCurrencyId($this->getLastInsertValue());
        }

        return $closing;
    }

    public function getOpenedData()
    {
        $tableGateway = new TableGateway($this->view, $this->adapter);

        $results = $tableGateway->select(function (Select $select){
            $select->where(array('payableId' => null, 'closingDate' => null, 'closingAmount' => null))
                ->order(array('openingDate DESC'));
        });

        return $results;
    }

    public function getNewOpeningData(array $currency)
    {
        $tableGateway = new TableGateway('vw_account_voucher', $this->adapter);

        $results = $tableGateway->select(function (Select $select) use($currency){
            $where = new Where();
            $where->notIn('currencyId', $currency)
                ->AND->equalTo('status','A');
            $select->columns(array('type', 'currencyId', 'currency', 'amount' => new Expression('SUM(amount)')))
                ->where($where)
                ->group(array('type', 'currencyId', 'currency'))
                ->order('type asc, currencyId asc');
        });

        return $results;
    }
}