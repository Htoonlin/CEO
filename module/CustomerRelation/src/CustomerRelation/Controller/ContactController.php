<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/3/2015
 * Time: 9:32 PM
 */

namespace CustomerRelation\Controller;

use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use CustomerRelation\DataAccess\ContactDataAccess;
use CustomerRelation\DataAccess\CompanyDataAccess;
use CustomerRelation\Entity\Contact;
use CustomerRelation\Helper\ContactHelper;
use Zend\View\Model\ViewModel;

/**
 * Class ContactController
 * @package CustomerRelation\Controller
 */
class ContactController extends SundewController{

    /**
     * @return ContactDataAccess
     */
    private function contactTable()
    {
        return new ContactDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
    }

    /**
     * @return array
     */
    private function companyCombos()
    {
        $dataAccess=new CompanyDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        return $dataAccess->getComboData('companyId', 'name');
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','name');
        $sortBy = $this->params()->fromQuery('by', 'asc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

        $paginator=$this->contactTable()->fetchAll(true, $filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter'=>$filter,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action','');
        $helper=new ContactHelper($this->companyCombos(), $this->getDbAdapter());
        $form=$helper->getForm();
        $contact=$this->contactTable()->getContact($id);
        $isEdit=true;

        if(!$contact){
            $isEdit=false;
            $contact=new Contact();
        }
        if($action == 'clone'){
            $isEdit = false;
            $id = 0;
            $contact->setContactId(0);
        }
        $form->bind($contact);
        $request=$this->getRequest();

        if($request->isPost()){
            $post_data=array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter(($isEdit ? $post_data['contactId'] :0), $post_data['name']));
           if($form->isValid()){
               $this->contactTable()->saveContact($contact);
               $this->flashMessenger()->addSuccessMessage('Save Successful');
               return $this->redirect()->toRoute('cr_contact');
           }
        }
        return new ViewModel(array('form'=>$form,
            'id'=>$id, 'isEdit'=>$isEdit));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id=(int)$this->params()->fromRoute('id', 0);

        $contact=$this->contactTable()->getContact($id);
        if($contact){
            $this->contactTable()->deleteContact($id);
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }
        return $this->redirect()->toRoute("cr_contact");
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->contactTable()->fetchAll(false));
        $response=$this->getResponse();
        $filename='attachment; filename="Contact-'. date('YmdHis').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type','application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return ApiModel
     */
    public function apiDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $db=$this->contactTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        $api = new ApiModel();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->contactTable()->deleteContact($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }

        return $api;
    }
}