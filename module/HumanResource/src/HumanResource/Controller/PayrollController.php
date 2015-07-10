<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/15/2015
 * Time: 3:29 PM
 */

namespace HumanResource\Controller;

use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\LeaveDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PayrollController extends AbstractActionController{

    private $staffTable;
    private $lateList;
    private $leaveTable;
    private $attendanceTable;
    private function init_data(){
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(!$this->staffTable)
            $this->staffTable = new StaffDataAccess($adapter);

        if(!$this->leaveTable)
            $this->leaveTable = new LeaveDataAccess($adapter);

        if(!$this->attendanceTable)
            $this->attendanceTable = new AttendanceDataAccess($adapter);

        $constantDataAccess = new ConstantDataAccess($adapter);
        if(!$this->lateList){
            $lateData = $constantDataAccess->getConstantByName('late_condition','payroll');
            $this->lateList = Json::decode($lateData->getValue());
        }
    }

    public function indexAction(){
        return new ViewModel();
    }

    public function processAction(){
        $this->init_data();
        return new ViewModel(array(
            'staffs' => $this->staffTable->fetchAll(false),
            'lateList' => $this->lateList,
        ));
    }
}