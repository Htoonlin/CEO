<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 2:16 PM
 */

namespace CustomerRelation\Controller;

use Application\DataAccess\ConstantDataAccess;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use CustomerRelation\Entity\Company;
use CustomerRelation\Helper\CompanyHelper;
use CustomerRelation\DataAccess\CompanyDataAccess;
use Zend\View\Model\ViewModel;

class CompanyController extends SundewController
{
    /**
     * @return CompanyDataAccess
     */
    private function companyTable()
    {
        return new CompanyDataAccess($this->getDbAdapter());
    }

    private $statusList;
    private $companyTypes;

    /**
     *
     */
    private function init_combos()
    {
        $constant = new ConstantDataAccess($this->getDbAdapter());

        if(!$this->statusList)
            $this->statusList = $constant->getComboByName('default_status');

        if(!$this->companyTypes)
            $this->companyTypes = $constant->getComboByName('company_types');
    }

    /**
     * @return ApiModel
     */
    public function apiAllAction()
    {
        $companies=$this->companyTable()->fetchAll();
        $data=array();
        foreach($companies as $company)
        {
            $data[]=array('companyId'=>$company->getCompanyId(), 'name'=>$company->getName());
        }

        return new ApiModel($data);
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','name');
        $sortBy = $this->params()->fromQuery('by','asc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator=$this->companyTable()->fetchAll(true, $filter, $sort, $sortBy);
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
        $this->init_combos();
        $id=(int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action','');
        $helper=new CompanyHelper($this->getDbAdapter());
        $form=$helper->getForm($this->companyTypes, $this->statusList);
        $company=$this->companyTable()->getCompany($id);

        $isEdit=true;
        if(!$company){
            $isEdit=false;
            $company=new Company();
        }
        if($action == 'clone'){
            $isEdit = false;
            $id = 0;
            $company->setCompanyId(0);
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

    /**
     * @return \Zend\Http\Response
     */
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

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->companyTable()->fetchAll(false));
        $response=$this->getResponse();
        $filename='attachment; filename="Company-'.date('YmdHis').'.xlsx"';
        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
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
        $db=$this->companyTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();

        $api = new ApiModel();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->companyTable()->deleteCompany($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }
        return $api;
    }


}