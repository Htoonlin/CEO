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
use Application\Service\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

/**
 * Class VoucherDataAccess
 * @package Account\DataAccess
 */
class VoucherDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "vw_account_voucher";
        $this->adapter = $dbAdapter;
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     */
    public function fetchAll($paginated=false,$filter='',$orderBy='voucherNo',$order='ASC')
    {
        if($paginated){
            $select = new Select($this->table);
            $select->order($orderBy . ' ' . $order);
            $where = new Where();
            $where->equalTo('status', 'R')
                ->AND->literal("concat_ws(' ',requester, description, voucherNo, accountType, amount, voucherDate, currency) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            return $this->paginateWith($select);
        }
        return $this->select();
    }

    /**
     * @param $voucherNo
     * @return array|\ArrayObject|null
     */
    public function getVoucher($voucherNo)
    {
        $result = $this->select(array('voucherNo' => $voucherNo));
        return $result->current();
    }

    /**
     * @param $currency
     * @return array|\ArrayObject|null
     */
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

    /**
     * @param $openingDate
     * @param $currency
     * @return \Zend\Db\ResultSet\ResultSet
     */
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

    /**
     * @param $fromDate
     * @param $toDate
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     */
    public function getVouchersByDate($fromDate, $toDate, $currency, $paginated = false, $filter='',$orderBy='voucherNo',$order='ASC')
    {
        if($paginated)
        {
            $select = new Select($this->table);

            $where = new Where();
            $where->in('status', array('A', 'C', 'F'))
                ->AND->equalTo('currencyId', $currency)
                ->AND->between('approvedDate', $fromDate, $toDate)
                ->AND->literal("concat_ws(' ',requester, description, voucherNo, accountType, amount, voucherDate) LIKE ?", '%' . $filter . '%');
            $select->where($where);
            $select->order($orderBy . ' ' . $order);

            return $this->paginateWith($select);
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