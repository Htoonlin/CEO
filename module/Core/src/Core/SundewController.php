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
use Zend\Session\Container;

/**
 * Class SundewController
 * @package Core
 */
class SundewController extends AbstractActionController
{
    /**
     * @var AdapterInterface
     */
    private $dbAdapter;
    /**
     * @var string
     */
    private $pageSize_NS = 'sundew_grid_size';

    /**
     * @param int $default
     * @return int|mixed
     */
    protected function getPageSize($default = 10){
        $grid_size = new Container($this->pageSize_NS);

        if(!isset($grid_size->pageSize)){
            return $default;
        }

        return $grid_size->pageSize;
    }

    /**
     * @param $size
     */
    protected function setPageSize($size){
        $grid_size = new Container($this->pageSize_NS);
        $grid_size->pageSize = $size;
    }
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

    /**
     * @var ArrayObject
     */
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

    /**
     * @var
     */
    private $staff;

    /**
     * @return \HumanResource\Entity\Staff|null
     */
    protected function getCurrentStaff()
    {
        if(!$this->staff){
            $userId = $this->getAuthUser()->userId;
            $staffDataAccess = new StaffDataAccess($this->getDbAdapter(), $userId);
            $staff = $staffDataAccess->getStaffByUser($userId);
            if(!$staff){
                $staff = new Staff();
            }

            $this->staff = $staff;
        }

        return $this->staff;
    }

    /**
     * @return array
     */
    public function notFoundAction(){

        return $this->indexAction();
    }
}