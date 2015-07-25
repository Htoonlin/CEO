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
use Application\Service\SundewController;
use HumanResource\DataAccess\PayrollDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Helper\PayrollHelper;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class PayrollController
 * @package HumanResource\Controller
 */
class PayrollController extends SundewController{

    private $staffTable;
    private $calendarTable;
    private $payrollTable;
    private $lateList;
    private $workingHours;
    private $leaveValues;
    private $formulaList;
    private $staffId;

    private function init_data(){
        if(!$this->staffTable)
            $this->staffTable = new StaffDataAccess($this->getDbAdapter());

        if(!$this->staffId){
            $staff = $this->getCurrentStaff();
            $this->staffId = boolval($staff) ? $staff->getStaffId() : 0;
        }

        if(!$this->payrollTable)
            $this->payrollTable = new PayrollDataAccess($this->getDbAdapter());

        if(!$this->calendarTable)
            $this->calendarTable = new CalendarDataAccess($this->getDbAdapter());

        $constantTable = new ConstantDataAccess($this->getDbAdapter());

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

        $constantDataAccess = new ConstantDataAccess($this->getDbAdapter());
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

    /**
     * @return ViewModel
     */
    public function indexAction(){
        $this->init_data();
        $helper = new PayrollHelper();
        $form = $helper->getForm($this->formulaList);

        return new ViewModel(array(
            'staffs' => $this->staffTable->getActiveStaffs(),
            'lateList' => $this->lateList,
            'workingHours' => $this->workingHours,
            'leaveValues' => $this->leaveValues,
            'form' => $form,
        ));
    }

    /**
     * @return JsonModel
     */
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