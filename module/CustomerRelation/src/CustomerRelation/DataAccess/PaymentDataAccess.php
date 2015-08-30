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
    protected $staffId;

    /**
     * @param Adapter $dbAdapter
     * @param $staffId
     */
    public function __construct(Adapter $dbAdapter,$staffId)
    {
        $this->staffId=$staffId;
        $this->table="tbl_cr_payment";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Payment());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPaymentView($id)
    {
        $select=new Select('vw_payment');
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
        return $paymentView=new TableGateway($view, $this->adapter);
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getPayment($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('paymentId'=>$id));
        return $rowset->current();
    }

    /**
     * @param Payment $payment
     * @return Payment
     */
    public function savePayment(Payment $payment)
    {
        $id = $payment->getPaymentId();
        $data = $payment->getArrayCopy();
        $data['staffId']=$this->staffId;
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