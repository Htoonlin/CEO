<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/5/2015
 * Time: 6:46 PM
 */

namespace Application\Controller;

use Application\DataAccess\CalendarDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Application\Helper\DashboardHelper;
use Core\Model\ApiModel;
use Core\SundewController;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\LeaveDataAccess;
use HumanResource\DataAccess\PayrollDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
use HumanResource\Entity\Leave;
use HumanResource\Helper\PayrollHelper;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;
use HumanResource\Entity\Staff;
use ProjectManagement\Entity\Task;
use ProjectManagement\DataAccess\TaskDataAccess;

class DashboardController extends SundewController
{
    private $staff;
    private $leaveTypeList;

    /**
     * @return AttendanceDataAccess
     */
    private function attendanceTable()
    {
        return new AttendanceDataAccess($this->getDbAdapter());
    }
    private function taskTable()
    {
        return new TaskDataAccess($this->getDbAdapter());
    }
    private $staffTable;
    private $calendarTable;
    private $lateList;
    private $workingHours;
    private $leaveValues;
    private $formulaList;
    private $leaveTable;

    /**
     *
     */
    private function init_data(){
        if(!$this->staffTable)
            $this->staffTable = new StaffDataAccess($this->getDbAdapter());

        if(!$this->staff){
            $this->staff = $this->getCurrentStaff();
        }

        if(!$this->leaveTable)
            $this->leaveTable = new LeaveDataAccess($this->getDbAdapter());

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

        if(!$this->lateList){
            $lateData = $constantTable->getConstantByName('late_condition','payroll');
            $lateList = Json::decode($lateData->getValue());
            usort($lateList,  function($a, $b){
                if($a->minute == $b->minute){
                    return 0;
                }
                return $a->minute > $b->minute ? -1 : 1;
            });

            $this->lateList = $lateList;
        }

        if(!$this->leaveTypeList){
            $result = $constantTable->getConstantByName('leave_type');
            $leaveTypes = json_decode($result->getValue());
            $comboList = array();
            foreach($leaveTypes as $leave){
                $comboList[$leave->id] = $leave->title;
            }
            $this->leaveTypeList = $comboList;
        }
    }

    /**
     * @return PayrollDataAccess
     */
    public function payrollTable()
    {
        return new PayrollDataAccess($this->getDbAdapter());
    }

    public function indexAction()
    {
        try{
            $this->init_data();
            $staffId = ($this->staff) ? $this->staff->getStaffId() : 0;
            $request = $this->getRequest();
            $attendance = $this->attendanceTable()->checkAttendance($staffId, date('Y-m-d', time()));

            if(!$attendance){
                $attendance = new Attendance();
                $attendance->exchangeArray(array(
                    'staffId' => $staffId,
                    'attendanceDate' => date('Y-m-d', time()),
                ));
            }

            $helper = new DashboardHelper();
            $leaveForm = $helper->getLeaveForm($this->leaveTypeList);
            $leave = new Leave();
            $leaveForm->bind($leave);

            $salaryHelper = new PayrollHelper();
            $salaryForm = $salaryHelper->getForm(array());

            if($request->isPost()){
                $post_data = $request->getPost()->toArray();
                $leaveForm->setData($post_data);
                $leaveForm->setInputFilter($helper->getLeaveFilter());
                if($leaveForm->isValid()){
                    $leave->setStaffId($staffId);
                    $leave->setStatus('R');
                    $this->leaveTable->saveLeave($leave);
                    $this->flashMessenger()->addWarningMessage('Leave request send to HR.');
                    return $this->redirect()->toRoute('dashboard');
                }
            }

            $todoList = $this->taskTable()->getToDoList($this->getCurrentStaff()->getStaffId());

            return new ViewModel(array(
                'attendance' => $attendance,
                'leaveForm' => $leaveForm,
                'salaryForm' => $salaryForm,
                'lateList' => $this->lateList,
                'workingHours' => $this->workingHours,
                'leaveValues' => $this->leaveValues,
                'staff' => ($this->staff) ? $this->staff : new Staff(),
                'taskList' => $todoList,
            ));
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function taskDetailAction()
    {
        $id = (int)$this->params()->fromRoute("id", 0);

        $task = new Task();
        $task = $this->taskTable()->getTaskByStaff($id, $this->getCurrentStaff()->getStaffId());
        if(!$task){
            $this->flashmessenger()->addErrorMessage('Invalid Task.');
            return $this->redirect()->toRoute('dashboard');
        }

        $request = $this->getRequest();
        if($request->isPost()){
            $post_data = $request->getPost();
            $current = $request->getPost('current', 0);
            $status = $request->getPost('status', 'A');

            $task = $this->taskTable()->getTask($id);
            $task->setCurrent($current);
            $task->setStatus($status);

            $this->taskTable()->saveTask($task);
            $this->flashMessenger()->addSuccessMessage('Save successful');
            return $this->redirect()->toRoute('dashboard');
        }

        return new ViewModel(array('task' => $task));
    }

    /**
     * @return ViewModel
     */
    public function paySlipAction()
    {
        $fromDate = $this->params()->fromQuery('from', date('Y-m-d', time()));
        $toDate = $this->params()->fromQuery('to', date('Y-m-d', strtotime('-1 month')));

        $payroll = $this->payrollTable()->getPayrollByDate($fromDate, $toDate, $this->getCurrentStaff()->getStaffId());

        if(!$payroll){
            $this->flashMessenger()->addWarningMessage("Payslip is not ready for your requested date ({$fromDate} - {$toDate}).");
            return $this->redirect()->toRoute('dashboard');
        }

        return new ViewModel(array(
            'payroll' => $payroll,
        ));
    }

    public function apiAttendanceAction()
    {
        $request = $this->getRequest();
        $api = new ApiModel();

        if($request->isPost())
        {
            $this->init_data();
            $attendance = $this->attendanceTable()->checkAttendance($this->staff->getStaffId(), date('Y-m-d', time()));

            if(!$attendance){
                $attendance = new Attendance();
                $attendance->exchangeArray(array(
                    'staffId' => $this->staff->getStaffId(),
                    'attendanceDate' => date('Y-m-d', time()),
                ));
            }

            try{
                $type = $this->params()->fromPost('status', '');
                if($type == 'I' && !$attendance->getInTime()){
                    $attendance->setInTime(date('H:i:s', time()));
                    $this->attendanceTable()->saveAttendance($attendance);
                }else if($type == 'O' && !$attendance->getOutTime()){
                    $attendance->setOutTime(date('H:i:s', time()));
                    $this->attendanceTable()->saveAttendance($attendance);
                }else{
                    $api->setStatusCode(406);
                    $api->setStatusMessage('You already registered. Please contact to HR if you want to change time.');
                }

            }catch(\Exception $ex) {
                $api->setStatusCode(500);
                $api->setStatusMessage($ex->getMessage());
            }
        }else{
            $api->setStatusCode(405);
        }
        return $api;
    }
}
