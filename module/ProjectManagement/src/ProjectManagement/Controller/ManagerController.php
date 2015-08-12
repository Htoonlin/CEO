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
use Zend\View\Model\JsonModel;
use Zend\Form\Element;
use ProjectManagement\DataAccess\ProjectDataAccess;
use ProjectManagement\Helper\TaskHelper;
use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Account\DataAccess\CurrencyDataAccess;
use ProjectManagement\DataAccess\TaskDataAccess;
use ProjectManagement\Entity\Task;
use Application\Service\SundewExporting;

class ManagerController extends SundewController
{

    /**
     *
     * @return multitype:Ambigous <>
     */
    private function getProjectList()
    {
        $dataAccess = new ProjectDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('projectId', 'name');
    }

    /**
     *
     * @return multitype:
     */
    private function getCurrencyList()
    {
        $dataAccess = new CurrencyDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('currencyId', 'code');
    }

    /**
     *
     * @return multitype:Ambigous <>
     */
    private function getStaffList()
    {
        $dataAccess = new StaffDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('staffId', 'staffName');
    }

    /**
     *
     * @return multitype:
     */
    private function getStatusList()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('task_status');
    }

    /**
     *
     * @return \Zend\Form\Element\Select
     */
    private function projectCombo()
    {
        $combo = new Element\Select();
        $combo->setAttribute('class', 'form-control');
        $combo->setName('projectList');
        $combo->setEmptyOption('-- No Project --');
        $combo->setValueOptions($this->getProjectList());
        return $combo;
    }

    public function taskTable()
    {
        return new TaskDataAccess($this->getDbAdapter());
    }

    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery("page", 1);
        $sort = $this->params()->fromQuery("page", "name");
        $sortBy = $this->params()->fromQuery("by", "asc");
        $filter = $this->params()->fromQuery("filter", "");
        $pageSize = (int)$this->params()->fromQuery("size", 10);

        $paginator = $this->projectTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'cboProject' => $this->projectCombo(),
        	"paginator" => $paginator,
        	"sort" => $sort,
        	"sortBy" => $sortBy,
        	"filter" => $filter,
        ));
    }

    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute("id", 0);
        $action = $this->params()->fromQuery("action", "");
        $helper = new TaskHelper();
        $form = $helper->getForm($this->getProjectList(), $this->getStaffList(), $this->getCurrencyList(), $this->getStatusList());
        $task = $this->taskTable()->getTask($id);

        $isEdit = true;
        if(!$task){
        	$isEdit = false;
        	$task = new Task();
        }

        if($action == 'clone'){
        	$isEdit = false;
        	$id = 0;
        	$task->setTaskId(0);
        }

        $form->bind($task);
        $request = $this->getRequest();
        if($request->isPost()){
        	$post_data = $request->getPost()->toArray();
        	$form->setData($post_data);
        	$form->setInputFilter($helper->getInputFilter($id));

        	if($form->isValid()){
        		$this->taskTable()->saveTask(task);
        		$this->flashMessenger()->addSuccessMessage('Save successful');
        		return $this->redirect()->toRoute('ProjectManagement_task');
        	}
        }
        return new ViewModel(array('form' => $form, 'id' => $id, 'isEdit' => $isEdit));
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id", 0);
        $task = $this->taskTable()->getTask($id);

        if($task){
        	$this->taskTable()->deleteTask($id);
        	$this->flashMessenger()->addInfoMessage('Delete successful');
        }

        return $this->redirect()->toRoute('ProjectManagement_task');
    }

    public function jsonDeleteAction()
    {
        $data = $this->params()->fromPost("chkId", array());
        $db = $ths->taskTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        try{
        	$conn->beginTransaction();
        	foreach($data as $id){
        		$this->taskTable()->deleteTask($id);
        	}
        	$conn->commit();
        	$messge = 'success';
        	$this->flashMessenger()->addInfoMessage('Delete successful');
        } catch(\Exception $ex) {
        	$conn->rollback();
        	$message = $ex->getMessage();
        }
        return new JsonModel(array('message' => $message));
    }

    public function exportAction()
    {
        $export = new SundewExporting($this->taskTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Task-' . date('Ymdhis') . '.xlsx"';
        $headers = $response->getHeaders();
        $headers->addHeaderLine("content-Type", "application/ms-excel; charset=UTF-8");
        $headers->addHeaderLine("Content-Disposition", $filename);
        $response->setContent($export->getExcel());
        return $response;
    }
}