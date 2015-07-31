<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:12 AM
 */

namespace HumanResource\DataAccess;

use Application\Service\SundewTableGateway;
use HumanResource\Entity\Payroll;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class PayrollDataAccess
 * @package HumanResource\DataAccess
 */
class PayrollDataAccess extends SundewTableGateway{

    protected $view = 'vw_hr_payroll';
    /**
     * @param Adapter $dpAdapter
     */
    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_payroll";
        $this->adapter=$dpAdapter;
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated = false, $filter='', $orderBy='fromDate', $order='DESC'){
        if($paginated)
        {
            return $this->paginate($filter, $orderBy, $order, $this->view);
        }

        $staffView=new TableGateway($this->view, $this->adapter);
        return $staffView->select();
    }

    public function getPayroll($id){
        $view = new TableGateway($this->view, $this->adapter);
        $result = $view->select(array('payrollId' => $id));
        return $result->current();
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $staffId
     * @return array|\ArrayObject|null
     */
    public function checkPayroll($fromDate, $toDate, $staffId){
        $result = $this->select(array(
            'staffId' => $staffId,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ));

        return $result->current();
    }

    /**
     * @param array $payroll
     * @return array
     */
    public function savePayroll(array $payroll)
    {
        $id = isset($payroll['payrollId']) ? $payroll['payrollId'] : 0;

        if ($id > 0) {
            $this->update($payroll, Array('payrollId' => $id));
        } else {
            unset($payroll['payrollId']);
            $this->insert($payroll);
        }
        if (!isset($payroll['payrollId'])) {
            $payroll['payrollId'] = $this->getLastInsertValue();
        }
        return $payroll;
    }

    /**
     * @param $id
     */
    public function deletePayroll($id)
    {
        $this->delete(array('payrollId'=>(int)$id));
    }

}