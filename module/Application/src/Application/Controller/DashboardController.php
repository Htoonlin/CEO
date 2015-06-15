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
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
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
    private function attendanceTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new AttendanceDataAccess($adapter);
    }
    public function indexAction()
    {
        $request = $this->getRequest();
        $attendance = new Attendance();
        $attendance->exchangeArray(array(
            'AttendanceId' => 0,
            'staffId' => $this->getStaff()->getStaffId(),
            'attendance' => date('Y-m-d H:i:s', time()),
            'status' => 'I',
        ));
        $attendanceIn = $this->attendanceTable()->checkAttendance($attendance, date('Y-m-d'));
        $attendance->setStatus('O');
        $attendanceOut = $this->attendanceTable()->checkAttendance($attendance, date('Y-m-d'));

        if($request->isPost())
        {
            $message = 'success';
            try{
                $type = $this->params()->fromPost('status', '');
                $attendance->setStatus($type);

                $attendanceInfo = $this->attendanceTable()->checkAttendance($attendance, date('Y-m-d'));
                if($attendanceInfo) {
                    $message = 'You already registered. Please contact to HR if you want to change time.';
                }else{
                    $this->attendanceTable()->saveAttendance($attendance);
                }
            }catch(\Exception $ex) {
                $message = $ex->getMessage();
            }

            return new JsonModel(array('message' => $message));
        }
        return new ViewModel(array(
            'attendanceIn' => $attendanceIn,
            'attendanceOut' => $attendanceOut,
        ));
    }
}
