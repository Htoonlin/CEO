<?php

namespace Application\Controller;


use Application\DataAccess\ConstantDataAccess;
use Application\Entity\Constant;
use Application\Helper\ConstantHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ConstantController extends AbstractActionController
{
    private function constantTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new ConstantDataAccess($adapter);
    }

    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'group_code');
        $sortBy = $this->params()->fromQuery('by', 'asc');
        $filter=$this->params()->fromQuery('filter','');
        $paginator = $this->constantTable()->fetchAll(true,$filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator' => $paginator,
            'page' => $page,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter'=>$filter,
        ));
    }

    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        $helper = new ConstantHelper($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form = $helper->getForm();
        $constant = $this->constantTable()->getConstant($id);
        $form->setAttribute("class", "form-horizontal");

        $isEdit = true;
        if(!$constant){
            $isEdit = false;
            $constant = new Constant();
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

    public function exportAction()
    {
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        $constants = $this->constantTable()->fetchAll(false);
        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($constants as $row)
        {
            $data = $row->getArrayCopy();
            if(count($columns) == 0){
                $columns = array_keys($data);
            }
            foreach($columns as $col){
                $cellId = $excelColumn . $start;
                $sheet->setCellValue($cellId, $data[$col]);
                $excelColumn++;
            }
            $start++;
            $excelColumn = "A";
        }
        foreach($columns as $col)
        {
            $cellId = $excelColumn . '1';
            $sheet->setCellValue($cellId, $col);
            $excelColumn++;
        }

        $excelWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        ob_start();
        $excelWriter->save('php://output');
        $excelOutput = ob_get_clean();

        $filename = 'attachment; filename="Constant-' . date('Ymdhms') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }

    public function jsonDeleteAction()
    {
        $data = $this->params()->fromPost('chkId', array());
        $db = $this->constantTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        $message = 'success';
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->constantTable()->deleteConstant($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }catch(\Exception $ex){
            $conn->rollback();
            $message = $ex->getMessage();
        }
        return new JsonModel(array("message" => $message));
    }
}

