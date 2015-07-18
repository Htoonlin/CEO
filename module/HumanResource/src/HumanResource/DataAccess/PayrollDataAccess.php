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

/**
 * Class PayrollDataAccess
 * @package HumanResource\DataAccess
 */
class PayrollDataAccess extends SundewTableGateway{
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