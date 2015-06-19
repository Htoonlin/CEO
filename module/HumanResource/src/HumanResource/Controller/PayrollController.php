<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/15/2015
 * Time: 3:29 PM
 */

namespace HumanResource\Controller;

use HumanResource\DataAccess\StaffDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PayrollController extends AbstractActionController{

    private function staffTable(){
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new StaffDataAccess($adapter);
    }

    public function indexAction(){
        $staffData = $this->staffTable()->fetchAll(false);
        return new ViewModel(array('staffs' => $staffData));
    }
}