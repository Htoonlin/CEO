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


class PayrollDataAccess extends SundewTableGateway{
    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_payroll";
        $this->adapter=$dpAdapter;
        $this->initialize();
    }
    public function checkPayroll($fromDate, $toDate, $staffId){
        $result = $this->select(array(
            'staffId' => $staffId,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ));

        return $result->current();
    }
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
    public function deletePayroll($id)
    {
        $this->delete(array('payrollId'=>(int)$id));
    }

}