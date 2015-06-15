<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/6/2015
 * Time: 9:12 AM
 */

namespace HumanResource\DataAccess;

use HumanResource\Entity\Payroll;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;


class PayrollDataAccess extends AbstractTableGateway{
    public function __construct(Adapter $dpAdapter)
    {
        $this->table="tbl_hr_payroll";
        $this->adapter=$dpAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Payroll());
        $this->initialize();
    }
    public function fetchAll()
    {
        return $this->select()->toArray();
    }
    public function getPayroll($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('payrollId'=>$id));
        return $rowset->current();
    }
    public function savePayroll(Payroll $payroll)
    {
        $id = $payroll->getPayrollId();
        $data = $payroll->getArrayCopy();

        if ($id > 0) {
            $this->update($data, Array('payrollId' => $id));
        } else {
            unset($data['payrollId']);
            $this->insert($data);
        }
        if (!$payroll->getPayrollId()) {
            $payroll->setPayrollId($this->getLastInsertValue());
        }
        return $payroll;
    }
    public function deletePayroll($id)
    {
        $this->delete(array('payrollId'=>(int)$id));
    }

}