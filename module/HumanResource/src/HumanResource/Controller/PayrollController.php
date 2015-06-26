<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/15/2015
 * Time: 3:29 PM
 */

namespace HumanResource\Controller;

use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PayrollController extends AbstractActionController{

    private $staffTable;
    private $lateList;
    private function init_data(){
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(!$this->staffTable)
            $this->staffTable = new StaffDataAccess($adapter);

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
        $staffData = $this->staffTable->fetchAll(false);
        return new ViewModel(array(
            'staffs' => $staffData,
            'lateList' => $this->lateList,
        ));
    }
}