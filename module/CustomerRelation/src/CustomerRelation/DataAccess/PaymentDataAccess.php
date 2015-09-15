<?php
/**
 * Created by PhpStorm.
 * User: kmk
 * Date: 8/17/2015
 * Time: 2:47 PM
 */

namespace CustomerRelation\DataAccess;

use Core\SundewTableGateway;
use CustomerRelation\Entity\Payment;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class PaymentDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter,$userId)
    {
        $this->table="tbl_cr_payment";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Payment());

        $this->useSoftDelete = true;
        parent::__construct($userId);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPaymentView($id)
    {
        $select=new Select('vw_cr_payment');
        $select->where(array('paymentId'=>$id));
        $statement=$this->sql->prepareStatementForSqlObject($select);
        $result=$statement->execute();
        return $result->current();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return TableGateway|\Zend\Paginator\Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated = false, $filter ='', $orderBy='paymentDate',$order='ASC')
    {
        $view = 'vw_cr_payment';
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
    public function getPayment($id)
    {
        $id=(int)$id;
        $rowSet=$this->select(array('paymentId'=>$id));
        return $rowSet->current();
    }

    /**
     * @param Payment $payment
     * @return Payment
     */
    public function savePayment(Payment $payment)
    {
        $id = $payment->getPaymentId();
        $data = $payment->getArrayCopy();
        if($id > 0){
            $this->update($data, array('paymentId' => $id));
        }else{
            unset($data['paymentId']);
            $this->insert($data);
        }
        if(!$payment->getPaymentId())
        {
            $payment->setPaymentId($this->getLastInsertValue());
        }
        return $payment;
    }
}