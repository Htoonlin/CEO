<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/18/2015
 * Time: 5:22 PM
 */

namespace Core;

use HumanResource\Entity\Staff;
use Zend\Db\Adapter\AdapterInterface;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\ViewModel;
use Zend\View\View;

/**
 * Class SundewController
 * @package Core
 */
class SundewController extends AbstractActionController
{
    private $dbAdapter;

    /**
     * @return mixed
     */
    protected function getDbAdapter()
    {
        if(!$this->dbAdapter){
            $this->dbAdapter = $this->getServiceLocator()->get('SundewDbAdapter');
        }

        return $this->dbAdapter;
    }

    private $user;

    /**
     * @return AdapterInterface
     */
    protected function getAuthUser()
    {
        if(!$this->user){
            $this->user = $this->layout()->current_user;
        }
        return $this->user;
    }

    private $staff;

    /**
     * @return \HumanResource\Entity\Staff|null
     */
    protected function getCurrentStaff()
    {
        if(!$this->staff){
            $userId = $this->getAuthUser()->userId;
            $staffDataAccess = new StaffDataAccess($this->getDbAdapter());
            $staff = $staffDataAccess->getStaffByUser($userId);
            if(!$staff){
                $staff = new Staff();
            }

            $this->staff = $staff;
        }

        return $this->staff;
    }

    public function notFoundAction(){

        return $this->indexAction();
    }
}