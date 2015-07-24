<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/24/2015
 * Time: 5:10 PM
 */

namespace ProjectManagement\Controller;


use Application\Service\SundewController;
use Zend\View\Model\ViewModel;

class ManagerController extends SundewController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}