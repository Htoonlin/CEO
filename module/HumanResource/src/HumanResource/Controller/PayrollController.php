<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/15/2015
 * Time: 3:29 PM
 */

namespace HumanResource\Controller;

use Application\DataAccess\CalendarDataAccess;
use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\PayrollDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Helper\PayrollHelper;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PayrollController extends AbstractActionController{

    private $staffTable;
    private $calendarTable;
    private $payrollTable;
    private $lateList;
    private $workingHours;
    private $leaveValues;
    private $formulaList;
    private $staffId;

    private function init_data(){
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        if(!$this->staffTable)
            $this->staffTable = new StaffDataAccess($adapter);

        if(!$this->staffId){
            $userId = $this->layout()->current_user->userId;
            $staff = $this->staffTable->getStaffByUser($userId);
            $this->staffId = boolval($staff) ? $staff->getStaffId() : 0;
        }

        if(!$this->payrollTable)
            $this->payrollTable = new PayrollDataAccess($adapter);

        if(!$this->calendarTable)
            $this->calendarTable = new CalendarDataAccess($adapter);

        $constantTable = new ConstantDataAccess($adapter);

        if(!$this->formulaList){
            $this->formulaList = $constantTable->getComboByName('payroll_formula', 'payroll');
        }

        if(!$this->workingHours){
            $constant = $constantTable->getConstantByName('work_hour');
            $this->workingHours = $constant->getValue();
        }

        if(!$this->leaveValues){
            $constant = $constantTable->getConstantByName('leave_type');
            $this->leaveValues = $constant->getValue();
        }

        $constantDataAccess = new ConstantDataAccess($adapter);
        if(!$this->lateList){
            $lateData = $constantDataAccess->getConstantByName('late_condition','payroll');
            $lateList = Json::decode($lateData->getValue());
            usort($lateList,  function($a, $b){
                if($a->minute == $b->minute){
                    return 0;
                }
                return $a->minute > $b->minute ? -1 : 1;
            });

            $this->lateList = $lateList;
        }
    }

    public function indexAction(){
        $this->init_data();
        $helper = new PayrollHelper();
        $form = $helper->getForm($this->formulaList);

        return new ViewModel(array(
            'staffs' => $this->staffTable->fetchAll(false),
            'lateList' => $this->lateList,
            'workingHours' => $this->workingHours,
            'leaveValues' => $this->leaveValues,
            'form' => $form,
        ));
    }

    public function jsonSaveAction()
    {
        try{
            $this->init_data();

            $lates = $this->params()->fromPost('late', array());
            $payroll = $this->params()->fromPost();
            $payroll['late'] = json_encode($lates);
            $payroll['managerId'] = $this->staffId;
            $payroll['status'] = 'A';

            $existPayroll = $this->payrollTable->checkPayroll($payroll['fromDate'],
                $payroll['toDate'], $payroll['staffId']);

            if($existPayroll){
                $payroll['payrollId'] = $existPayroll->payrollId;
            }

            $result = $this->payrollTable->savePayroll($payroll);

            return new JsonModel(array(
                'status' => 'success',
                'result' => $result
            ));
        }catch(\Exception $ex){
            return new JsonModel(array(
                'status' => 'error',
                'message' => $ex->getMessage()
            ));
        }
    }
}