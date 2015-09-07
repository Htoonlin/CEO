<?php
/**
 * Created by PhpStorm.
 * User: Sundew
 * Date: 5/25/2015
 * Time: 1:20 PM
 */

namespace ProjectManagement\Controller;

use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use HumanResource\DataAccess\StaffDataAccess;
use ProjectManagement\DataAccess\ProjectDataAccess;
use ProjectManagement\Entity\Project;
use ProjectManagement\Helper\ProjectHelper;
use Zend\View\Model\ViewModel;

class ProjectController extends SundewController{

    /**
     * @return ProjectDataAccess
     */
    private function projectTable(){
        return new ProjectDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function userCombos(){
        $dataAccess=new StaffDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('staffId', 'staffCode');
    }

    /* Insert Action */
    /**
     * @return ViewModel
     */
    public function indexAction(){
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','name');
        $sortBy = $this->params()->fromQuery('by','asc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

        $paginator=$this->projectTable()->fetchAll(true,$filter,$sort,$sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter'=>$filter
        ));
    }

    public function reportAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);

        try{
            if($id > 0){
                $project = $this->projectTable()->getProject($id);
                if(!$project){
                    return 'Invalid project';
                }
                $title = ' ' . $project->getName() . "({$project->getCode()})";
            }else if($id == 0){
                $title = ' (No Project)';
            }else{
                $title = ' (all project)';
            }
        }catch(\Exception $ex){
            return 'Error => ' . $ex->getMessage();
        }

        return new ViewModel(array(
            'projectId' => $id,
            'title' => 'Reports for ' . $title,
        ));
    }

    /*detail action INSERT/UPDATE */
    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction(){
        $id=(int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action', '');

        $helper=new ProjectHelper($this->getDbAdapter());
        $form=$helper->getform($this->userCombos());
        $project=$this->projectTable()->getProject($id);

        $isEdit=true;
        if(!$project){
            $isEdit = false;
            $project = new Project();
            $staffId = $this->getCurrentStaff()->getStaffId();
            $project->setManagerId($staffId);
        }

        if($action == 'clone'){
            $isEdit = false;
            $id = 0;
            $project->setProjectId(0);
        }

        $form->bind($project);
        $request=$this->getRequest();

        if($request->isPost()){
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id));

            if($form->isValid()){
                $this->projectTable()->saveProject($project);
                $this->flashMessenger()->addSuccessMessage('Save Successful');
                return $this->redirect()->toRoute('pm_project');
            }
        }

        return new ViewModel(array('form'=>$form, 'id'=>$id, 'isEdit'=>$isEdit));
    }

    /*detail action INSERT/UPDATE */

    /*delete action DELETE*/
    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction(){
        $id=(int)$this->params()->fromRoute('id', 0);
        $project=$this->projectTable()->getProject($id);

        if($project){
            $this->projectTable()->deleteProject($id);
            $this->flashMessenger()->addMessage('Delete successful!');
        }

        return $this->redirect()->toRoute('pm_project');
    }
    /*delete action DELETE*/

    /*json delete action DELETEs*/
    public function apiDeleteAction(){
        $data=$this->params()->fromPost('chkId',array());
        $db=$this->projectTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        $api = new ApiModel();
        try{
            $conn->beginTransaction();

            foreach($data as $id){
                $this->projectTable()->deleteProject($id);
            }

            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }
        catch(\Exception $ex){
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }

        return $api;
    }
    /*json delete action DELETEs*/

    /*export action EXCEL*/
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction(){
        $export = new SundewExporting($this->projectTable()->fetchAll(false));
        $response=$this->getResponse();

        $filename='attachment; filename="Project-'.date('Ymdhis').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }
    /*export action EXCEL*/
}