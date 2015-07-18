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
use Application\Service\SundewController;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\LeaveDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
use HumanResource\Entity\Leave;
use HumanResource\Helper\PayrollHelper;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use HumanResource\Entity\Staff;

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

        if(!$this->leaveTypeList){
            $result = $constantDataAccess->getConstantByName('leave_type');
            $leaveTypes = json_decode($result->getValue());
            $comboList = array();
            foreach($leaveTypes as $leave){
                $comboList[$leave->id] = $leave->title;
            }
            $this->leaveTypeList = $comboList;
        }
    }

    public function indexAction()
    {
        try{
            $staffId = ($this->staff) ? $this->staff->getStaffId() : 0;

            $this->init_data();
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

            return new ViewModel(array(
                'attendance' => $attendance,
                'leaveForm' => $leaveForm,
                'salaryForm' => $salaryForm,
                'lateList' => $this->lateList,
                'workingHours' => $this->workingHours,
                'leaveValues' => $this->leaveValues,
                'staff' => ($this->staff) ? $this->staff : new Staff(),
            ));
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function jsonAttendanceAction()
    {
        $request = $this->getRequest();

        if($request->isPost())
        {
            $attendance = $this->attendanceTable()->checkAttendance($this->staff->getStaffId(), date('Y-m-d', time()));

            if(!$attendance){
                $attendance = new Attendance();
                $attendance->exchangeArray(array(
                    'staffId' => $this->staff->getStaffId(),
                    'attendanceDate' => date('Y-m-d', time()),
                ));
            }

            $message = 'success';
            try{
                $type = $this->params()->fromPost('status', '');

                if($type == 'I' && !$attendance->getInTime()){
                    $attendance->setInTime(date('h:i:s', time()));
                    $this->attendanceTable()->saveAttendance($attendance);
                }else if($type == 'O' && !$attendance->getOutTime()){
                    $attendance->setOutTime(date('h:i:s', time()));
                    $this->attendanceTable()->saveAttendance($attendance);
                }else{
                    $message = 'You already registered. Please contact to HR if you want to change time.';
                }

            }catch(\Exception $ex) {
                $message = $ex->getMessage();
            }

            return new JsonModel(array('message' => $message));
        }

        return new JsonModel(array('message' => 'Invalid request.'));
    }
}
