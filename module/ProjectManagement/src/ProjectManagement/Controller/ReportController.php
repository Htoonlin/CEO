<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 8/15/2015
 * Time: 11:22 AM
 */

namespace ProjectManagement\Controller;

use Application\Service\SundewController;
use Zend\View\Helper\ViewModel;

class ReportController extends SundewController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}