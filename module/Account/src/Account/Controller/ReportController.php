<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/23/2015
 * Time: 11:30 AM
 */

namespace Account\Controller;


use Application\Service\SundewController;
use Zend\View\Model\ViewModel;

class ReportController extends SundewController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}