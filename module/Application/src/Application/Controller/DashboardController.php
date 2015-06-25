<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/5/2015
 * Time: 6:46 PM
 */

namespace Application\Controller;


use Application\DataAccess\CalendarDataAccess;
use Application\DataAccess\CalendarType;
use Application\DataAccess\ConstantDataAccess;
use Application\Helper\DashboardHelper;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
use HumanResource\Helper\LeaveHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{
    private $staff;
    private function getStaff()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        if(!$this->staff){
            $userId = $this->layout()->current_user->userId;
            $staffDataAccess = new StaffDataAccess($adapter);
            $this->staff = $staffDataAccess->getStaffByUser($userId);
        }
        return $this->staff;
    }
    private function leaveTypeList(){
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByGroupCode('leave_type');
    }
    private function attendanceTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new AttendanceDataAccess($adapter);
    }
    public function indexAction()
    {
        $request = $this->getRequest();
        $attendance = $this->attendanceTable()->checkAttendance($this->getStaff()->getStaffId(), date('Y-m-d', time()));

        if(!$attendance){
            $attendance = new Attendance();
            $attendance->exchangeArray(array(
                'staffId' => $this->getStaff()->getStaffId(),
                'attendanceDate' => date('Y-m-d', time()),
            ));
        }
        $helper = new DashboardHelper();
        $leaveForm = $helper->getLeaveForm($this->leaveTypeList());

        if($request->isPost())
        {
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

        return new ViewModel(array(
            'attendance' => $attendance,
            'leaveForm' => $leaveForm,
        ));
    }

    public function leaveAction(){
        $request = $this->getRequest();

        $this->flashMessenger()->addWarningMessage('Leave request send to HR.');
        return $this->redirect()->toRoute('dashboard');
    }
}
