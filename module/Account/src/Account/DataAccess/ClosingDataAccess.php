<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/22/2015
 * Time: 5:31 PM
 */

namespace Account\DataAccess;

use Account\Entity\Closing;
use Core\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class ClosingDataAccess
 * @package Account\DataAccess
 */
class ClosingDataAccess extends SundewTableGateway
{
    private $view = 'vw_account_closing';

    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table = 'tbl_account_closing';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Closing());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param bool $paginated
     * @param int $currency
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     */
    public function fetchAll($paginated=false,$currency = 0, $orderBy = 'closingDate', $order='desc')
    {
        if($paginated){
            $select=new Select($this->view);
            $select->order($orderBy . ' ' . $order);
            if($currency > 0){
                $where=new Where();
                $where->equalTo('currencyId', $currency);
                $select->where($where);
            }
            return $this->paginateWith($select);
        }
        $select = new Select($this->view);
        return $this->selectOther($select);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getClosing($id)
    {
        $result = $this->select(array('closingId' => $id));

        if(!$result){
            throw new \Exception('Invalid closing id.');
        }

        return $result->current();
    }

    /**
     * @param Closing $closing
     * @return Closing
     */
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
            $closing->setClosingId($this->getLastInsertValue());
        }

        return $closing;
    }

    /**
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getOpenedData()
    {
        $select = new Select($this->view);
        $select->where(array('payableId' => null, 'closingDate' => null, 'closingAmount' => null))
            ->order(array('openingDate DESC'));

        return $this->selectOther($select);
    }

    /**
     * @param array $currency
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getNewOpeningData(array $currency)
    {
        $select = new Select('vw_account_voucher');
        $where = new Where();
        if(empty($currency)){
            $where->equalTo('status', 'A');
        }else{
            $where->notIn('currency', $currency)
                ->AND->equalTo('status','A');
        }
        $select->columns(array('type', 'currency', 'amount' => new Expression('SUM(amount)')))
            ->where($where)
            ->group(array('type', 'currency'))
            ->order('currency asc');

        return $this->selectOther($select);
    }
}