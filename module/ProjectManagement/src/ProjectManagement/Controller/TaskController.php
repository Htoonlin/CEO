<?php
namespace ProjectManagement\Controller;

use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use Zend\View\Model\ViewModel;
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
     * @return array
     */
    private function getProjectList()
    {
        $dataAccess = new ProjectDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        return $dataAccess->getComboData('projectId', 'name');
    }

    /**
     * @return array
     */
    private function getCurrencyList()
    {
        $dataAccess = new CurrencyDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        return $dataAccess->getComboData('currencyId', 'code');
    }

    /**
     * @return array
     */
    private function getStaffList()
    {
        $dataAccess = new StaffDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        return $dataAccess->getComboData('staffId', 'staffName');
    }

    /**
     * @return array
     */
    private function getStatusList()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        return $dataAccess->getComboByName('task_status');
    }

    /**
     *
     * @return \Zend\Form\Element\Select
     */
    private function projectCombo($selected)
    {
        $projectList = $this->getProjectList();
        $projectList[0] = '- No Project -';
        $projectList[-1] = '- All -';
        ksort($projectList);
        $combo = new Element\Select();
        $combo->setAttribute('class', 'form-control');
        $combo->setName('projectList');
        $combo->setValueOptions($projectList);
        $combo->setValue($selected);
        return $combo;
    }

    /**
     * @return \ProjectManagement\DataAccess\TaskDataAccess
     */
    public function taskTable()
    {
        return new TaskDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $projectId = (int)$this->params()->fromRoute('id', -1);

        $page = (int)$this->params()->fromQuery("page", 1);
        $sort = $this->params()->fromQuery("sort", "status");
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
        $projectId = $this->params()->fromQuery("projectId", 0);
        $helper = new TaskHelper();
        $form = $helper->getForm($this->getProjectList(), $this->getStaffList(), $this->getCurrencyList(), $this->getStatusList());
        $task = $this->taskTable()->getTask($id);

        $isEdit = true;
        if(!$task){
        	$isEdit = false;
        	$task = new Task();
            $task->setProjectId($projectId);
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
        		return $this->redirect()->toRoute('pm_task', array('action' => 'index', 'id' => $projectId));
        	}
        }
        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'isEdit' => $isEdit,
            'projectId' => $projectId,
        ));
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
     * @return ApiModel
     */
    public function apiDeleteAction()
    {
        $data = $this->params()->fromPost("chkId", array());
        $db = $this->taskTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        $api = new ApiModel();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->taskTable()->deleteTask($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete successful');
        } catch(\Exception $ex) {
            $conn->rollback();
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }
        return $api;
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
