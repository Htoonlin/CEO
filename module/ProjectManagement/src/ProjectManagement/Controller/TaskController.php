<?php
namespace ProjectManagement\Controller;

use Application\Service\SundewController;
use Application\Service\SundewExporting;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use ProjectManagement\Entity\Task;
use ProjectManagement\Helper\TaskHelper;
use ProjectManagement\DataAccess\TaskDataAccess;
use ProjectManagement\DataAccess\ProjectDataAccess;
use Account\DataAccess\CurrencyDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Zend\Form\Element;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-12 12:09:23
 *
 * @package ProjectManagement\Controller
 */
class TaskController extends SundewController
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
    private function projectCombo($selected)
    {
        $combo = new Element\Select();
        $combo->setAttribute('class', 'form-control');
        $combo->setName('projectList');
        $combo->setEmptyOption('-- No Project --');
        $combo->setValueOptions($this->getProjectList());
        $combo->setValue($selected);
        return $combo;
    }

    /**
     * @return \ProjectManagement\DataAccess\TaskDataAccess
     */
    public function taskTable()
    {
        return new TaskDataAccess($this->getDbAdapter());
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $projectId = (int)$this->params()->fromRoute('id', 0);

        $page = (int)$this->params()->fromQuery("page", 1);
        $sort = $this->params()->fromQuery("sort", "name");
        $sortBy = $this->params()->fromQuery("by", "asc");
        $filter = $this->params()->fromQuery("filter", "");
        $pageSize = (int)$this->params()->fromQuery("size", 10);

        $paginator = $this->taskTable()->fetchAll($this->getCurrentStaff()->getStaffId(),
            $projectId, true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'cboProject' => $this->projectCombo($projectId),
        	"paginator" => $paginator,
        	"sort" => $sort,
        	"sortBy" => $sortBy,
        	"filter" => $filter,
            "projectId" => $projectId,
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
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
        	    $task->setManagerId($this->getCurrentStaff()->getStaffId());
        		$this->taskTable()->saveTask($task);
        		$this->flashMessenger()->addSuccessMessage('Save successful');
        		return $this->redirect()->toRoute('pm_task');
        	}
        }
        return new ViewModel(array('form' => $form, 'id' => $id, 'isEdit' => $isEdit));
    }

    /**
     *
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute("id", 0);
        $task = $this->taskTable()->getTask($id);

        if($task){
        	$this->taskTable()->deleteTask($id);
        	$this->flashMessenger()->addInfoMessage('Delete successful');
        }

        return $this->redirect()->toRoute('pm_task');
    }

    /**
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function jsonDeleteAction()
    {
        $data = $this->params()->fromPost("chkId", array());
        $db = $this->taskTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        try{
        	$conn->beginTransaction();
        	foreach($data as $id){
        		$this->taskTable()->deleteTask($id);
        	}
        	$conn->commit();
        	$message = 'success';
        	$this->flashMessenger()->addInfoMessage('Delete successful');
        } catch(\Exception $ex) {
        	$conn->rollback();
        	$message = $ex->getMessage();
        }
        return new JsonModel(array('message' => $message));
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $projectId = (int)$this->params()->fromRoute('id', 0);

        $export = new SundewExporting($this->taskTable()->fetchAll(
            $this->getCurrentStaff()->getStaffId(), $projectId, false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Task-' . date('Ymdhis') . '.xlsx"';
        $headers = $response->getHeaders();
        $headers->addHeaderLine("content-Type", "application/ms-excel; charset=UTF-8");
        $headers->addHeaderLine("Content-Disposition", $filename);
        $response->setContent($export->getExcel());
        return $response;
    }


}
