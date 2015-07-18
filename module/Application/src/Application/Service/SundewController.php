<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/18/2015
 * Time: 5:22 PM
 */

namespace Application\Service;

use HumanResource\DataAccess\StaffDataAccess;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class SundewController
 * @package Application\Service
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
            $this->dbAdapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        }

        return $this->dbAdapter;
    }

    private $user;

    /**
     * @return mixed
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
            $this->staff = $staffDataAccess->getStaffByUser($userId);
        }

        return $this->staff;
    }
}