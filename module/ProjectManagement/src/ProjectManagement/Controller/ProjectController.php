<?php
/**
 * Created by PhpStorm.
 * User: Sundew
 * Date: 5/25/2015
 * Time: 1:20 PM
 */

namespace ProjectManagement\Controller;

use Application\DataAccess\UserDataAccess;
use Application\Service\SundewExporting;
use ProjectManagement\DataAccess\ProjectDataAccess;
use ProjectManagement\Entity\Project;
use ProjectManagement\Helper\ProjectHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ProjectController extends AbstractActionController{

    /*index action*/
    public function indexAction(){
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','name');
        $sortBy = $this->params()->fromQuery('by','asc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

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

    private function projectTable(){
        $adapter=$this->getServiceLocator()->get('Sundew\Db\Adapter');
        return new ProjectDataAccess($adapter);
    }
    /*index action*/

    /*detail action INSERT/UPDATE */
    public function detailAction(){
        $id=(int)$this->params()->fromRoute('id',0);
        $helper=new ProjectHelper($this->getServiceLocator()->get('Sundew\Db\Adapter'));
        $form=$helper->getform($this->userCombos());
        $project=$this->projectTable()->getProject($id);

        $isEdit=true;
        if(!$project){
            $isEdit=false;
            $project=new Project();
        }

        $form->bind($project);
        $request=$this->getRequest();

        if($request->isPost()){
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id));

            if($form->isValid()){
                var_dump("form is valid");
                $this->projectTable()->saveProject($project);
                $this->flashMessenger()->addSuccessMessage('Save Successful');
                return $this->redirect()->toRoute('pm_project');
            }
        }

        return new ViewModel(array('form'=>$form, 'id'=>$id, 'isEdit'=>$isEdit));
    }

    private function userCombos(){
        $adapter=$this->getServiceLocator()->get('Sundew\Db\Adapter');
        $dataAccess=new UserDataAccess($adapter);
        return $dataAccess->getComboData('userId','userName');
    }
    /*detail action INSERT/UPDATE */

    /*delete action DELETE*/
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
    public function jsonDeleteAction(){
        $data=$this->params()->fromPost('chkId',array());
        $db=$this->projectTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        try{
            $conn->beginTransaction();

            foreach($data as $id){
                $this->projectTable()->deleteProject($id);
            }

            $conn->commit();
            $message='success';
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }
        catch(\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }

        return new JsonModel(array('message'=>$message));
    }
    /*json delete action DELETEs*/

    /*export action EXCEL*/
    public function exportAction(){
        $export = new SundewExporting($this->projectTable()->fetchAll(false));
        $response=$this->getResponse();

        $filename='attachment; filename="Project-'.date('Ymdhms').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }
    /*export action EXCEL*/
}