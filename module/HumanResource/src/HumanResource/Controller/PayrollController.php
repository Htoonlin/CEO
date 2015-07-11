<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/15/2015
 * Time: 3:29 PM
 */

namespace HumanResource\Controller;

use Application\DataAccess\CalendarDataAccess;
use Application\DataAccess\CalendarType;
use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Helper\PayrollHelper;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PayrollController extends AbstractActionController{

    private $staffTable;
    private $lateList;
    private $calendarTable;
    private $workingHours;
    private $leaveValues;
    private function init_data(){
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(!$this->staffTable)
            $this->staffTable = new StaffDataAccess($adapter);

        if(!$this->calendarTable)
            $this->calendarTable = new CalendarDataAccess($adapter);

        $constantTable = new ConstantDataAccess($adapter);

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
        return new ViewModel();
    }

    public function processAction(){
        $this->init_data();
        $helper = new PayrollHelper();

        $weeklyHoliday = $this->calendarTable->getCalendarByType(CalendarType::holiday_weekly);

        return new ViewModel(array(
            'staffs' => $this->staffTable->fetchAll(false),
            'lateList' => $this->lateList,
            'weeklyHoliday' => Json::encode($weeklyHoliday),
            'workingHours' => $this->workingHours,
            'leaveValues' => $this->leaveValues,
            'form' => $helper->getForm(),
        ));
    }

}