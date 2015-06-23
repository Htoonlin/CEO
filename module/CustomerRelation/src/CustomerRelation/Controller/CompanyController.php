<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 2:16 PM
 */

namespace CustomerRelation\Controller;

use CustomerRelation\Entity\Company;
use CustomerRelation\Helper\CompanyHelper;
use CustomerRelation\DataAccess\CompanyDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CompanyController extends AbstractActionController
{
    private function companyTable()
    {
        $sm=$this->getServiceLocator();
        $adapter=$sm->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CompanyDataAccess($adapter);
        return $dataAccess;
    }

    public function jsonAllAction()
    {
        $companies=$this->companyTable()->fetchAll();
        $data=array();

        foreach($companies as $company)
        {
            $data[]=array('companyId'=>$company->getCompanyId(), 'name'=>$company->getName());
        }
        return new JsonModel($data);
    }

    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort','name');
        $sortBy=$this->params()->fromQuery('by'.'asc');
        $filter=$this->params()->fromQuery('filter', '');

        $paginator=$this->companyTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'page'=>$page,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter'=>$filter,
        ));
    }

    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $helper=new CompanyHelper($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form=$helper->getForm();
        $company=$this->companyTable()->getCompany($id);

        $isEdit=true;
        if(!$company){
            $isEdit=false;
            $company=new Company();
        }
        $form->bind($company);
        $request=$this->getRequest();

        if($request->isPost()){
            $form->setInputFilter($helper->getInputFilter($id));
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter(($isEdit? $post_data['companyId']:0), $post_data['name']));
            if($form->isValid()){
                $this->companyTable()->saveCompany($company);

                $this->flashMessenger()->addSuccessMessage('Save Successful');
                return $this->redirect()->toRoute('cr_company');
            }
        }
        return new ViewModel(array('form'=>$form,
            'id'=>$id, 'isEdit'=>$isEdit));
    }

    public function deleteAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $company=$this->companyTable()->getCompany($id);
        if($company){
            $this->companyTable()->deleteCompany($id);
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }
        return $this->redirect()->toRoute("cr_company");
    }

    public function exportAction()
    {
        $response=$this->getResponse();

        $excelObj=new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet=$excelObj->getActiveSheet();

        $companies=$this->companyTable()->fetchAll(false);
        $columns=array();

        $excelColumn="A";
        $start=2;
        foreach($companies as $row)
        {
            $data=$row->getArrayCopy();
            if(count($columns)==0){
                $columns=array_keys($data);
            }
            foreach($columns as $col){
                $cellId=$excelColumn.$start;
                $sheet->setCellValue($cellId, $data[$col]);
                $excelColumn++;
            }
            $start++;
            $excelColumn="A";
        }
        foreach($columns as $col)
        {
            $cellId=$excelColumn.'1';
            $sheet->setCellValue($cellId, $col);
            $excelColumn++;
        }

        $excelWriter=\PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        ob_start();
        $excelWriter->save('php://output');
        $excelOutput=ob_get_clean();

        $filename='attachment; filename="Company-'.date('Ymdhis').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }

    public function jsonDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $message="success";

        $db=$this->companyTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();

        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->companyTable()->deleteCompany($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }
        return new JsonModel(array("message"=>$message));
    }


}