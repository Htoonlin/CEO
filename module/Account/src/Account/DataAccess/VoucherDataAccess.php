<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/24/2015
 * Time: 1:31 PM
 */

namespace Account\DataAccess;

use Account\Entity\Closing;
use Account\Entity\Voucher;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class VoucherDataAccess extends AbstractTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "vw_account_voucher";
        $this->adapter = $dbAdapter;
        $this->initialize();
    }

    public function fetchAll($paginated=false,$filter='',$orderBy='voucherNo',$order='ASC')
    {
        if($paginated){
            $select = new Select($this->table);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->equalTo('status', 'R')
                ->AND->literal("Concat_ws(' ',voucherNo,  accountType) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        return $this->select();
    }

    public function getVoucher($voucherNo)
    {
        $result = $this->select(array('voucherNo' => $voucherNo));
        return $result->current();
    }

    public function getInitVoucherByNewCurrency($currency){
        $results = $this->select(function (Select $select) use ($currency){
            $where = new Where();
            $where->equalTo('currency', $currency)
                ->AND->equalTo('type', 'Receivable')
                ->AND->equalTo('status','A');
            $select->where($where)
                ->order('approvedDate ASC')
                ->limit(1);
        });

        return $results->current();
    }

    public function getClosingData($openingDate, $currency)
    {
        $results = $this->select(function (Select $select) use ($openingDate, $currency){
            $where = new Where();
            $where->equalTo('currency', $currency)
                ->AND->greaterThanOrEqualTo('approvedDate', $openingDate)
                ->AND->equalTo('status','A');
            $select->columns(array('type', 'amount' => new Expression('SUM(amount)')))
                ->group(array('type'))
                ->where($where);
        });

        return $results;
    }

    public function getVouchersByDate($fromDate, $toDate, $paginated = false, $filter='',$orderBy='voucherNo',$order='ASC')
    {
        if($paginated)
        {
            $select = new Select($this->table);

            $where = new Where();
            $where->in('status', array('A', 'C', 'F'))
                ->AND->between('approvedDate', $fromDate, $toDate)
                ->AND->literal("Concat_ws(' ', type,  voucherNo, requester, accountType, description, amount, currency) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $select->order($orderBy . ' ' . $order);
            $paginatorAdapter = new DbSelect($select, $this->adapter);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }

        $results = $this->select(function (Select $select) use ($fromDate, $toDate){
            $where = new Where();
            $where->in('status', array('A', 'C', 'F'))
                ->AND->between('approvedDate', $fromDate, $toDate);
            $select->where($where)->order('voucherNo asc');
        });

        return $results;
    }
}