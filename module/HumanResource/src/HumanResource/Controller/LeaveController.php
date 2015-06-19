<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 3:25 PM
 */

namespace HumanResource\Controller;


use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\LeaveDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Helper\LeaveHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LeaveController extends AbstractActionController
{
    private function leaveTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new LeaveDataAccess($adapter);
    }

    private $staffList;
    private $statusList;
    private $leaveTypeList;
    private function initCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adatper\Adapter');
        $staffDA = new StaffDataAccess($adapter);
        $constantDA = new ConstantDataAccess($adapter);
        $this->staffList = $staffDA->getComboData('staffId', 'code');
        $this->statusList = $constantDA->getComboByGroupCode('leave_status');
        $this->leaveTypeList = $constantDA->getComboByGroupCode('leave_type');
    }

    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'date');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $paginator = $this->leaveTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator' => $paginator,
            'page' => $page,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter' => $filter,
        ));
    }

    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $helper = new LeaveHelper();
        $this->initCombo();

        $form = $helper->getForm($this->staffList, $this->statusList, $this->leaveTypeList);
        return new ViewModel();
    }
}