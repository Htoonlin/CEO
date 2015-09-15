<?php

namespace Application\Controller;


use Application\DataAccess\ConstantDataAccess;
use Application\Entity\Constant;
use Application\Helper\ConstantHelper;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use Zend\View\Model\ViewModel;

class ConstantController extends SundewController
{
    /**
     * @return ConstantDataAccess
     */
    private function constantTable()
    {
        return new ConstantDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'group_code');
        $sortBy = $this->params()->fromQuery('by', 'asc');
        $filter= $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator = $this->constantTable()->fetchAll(true,$filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator' => $paginator,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter'=>$filter,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $isClone = $this->params()->fromQuery('action', '');

        $helper = new ConstantHelper($this->getDbAdapter());
        $form = $helper->getForm();
        $constant = $this->constantTable()->getConstant($id);
        $form->setAttribute("class", "form-horizontal");

        $isEdit = true;
        if(!$constant){
            $isEdit = false;
            $constant = new Constant();
        }

        if($constant && $isClone == 'clone'){
            $isEdit = false;
            $id = 0;
            $constant->setConstantId($id);
        }

        $form->bind($constant);
        $request = $this->getRequest();

        if($request->isPost()){
            $form->setInputFilter($helper->getInputFilter($id));
            $post_data = $request->getPost()->toArray();
            $form->setData($post_data);
            if($form->isValid()){
                $this->constantTable()->saveConstant($constant);
                $this->flashMessenger()->addSuccessMessage('Save successful');
                return $this->redirect()->toRoute('constant');
            }
        }

        return new ViewModel(array('form' => $form,
            'id' => $id, 'isEdit' => $isEdit));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $constant = $this->constantTable()->getConstant($id);
        if($constant){
            $this->constantTable()->deleteConstant($id);
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }

        return $this->redirect()->toRoute("constant");
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->constantTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Constant-' . date('YmdHis') . '.xlsx"';
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    public function apiDeleteAction()
    {
        $data = $this->params()->fromPost('chkId', array());
        $db = $this->constantTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        $api = new ApiModel();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->constantTable()->deleteConstant($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }catch(\Exception $ex){
            $conn->rollback();
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }
        return $api;
    }
}

