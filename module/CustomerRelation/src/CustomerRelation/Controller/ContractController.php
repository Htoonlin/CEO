<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/4/2015
 * Time: 2:35 PM
 */

namespace CustomerRelation\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewController;
use Application\Service\SundewExporting;
use CustomerRelation\DataAccess\ContractDataAccess;
use CustomerRelation\Entity\Contract;
use CustomerRelation\Helper\ContractHelper;
use CustomerRelation\DataAccess\CompanyDataAccess;
use CustomerRelation\DataAccess\ContactDataAccess;
use ProjectManagement\DataAccess\ProjectDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ContractController
 * @package CustomerRelation\Controller
 */
class ContractController extends SundewController
{
    private $staffId;
    private $staffName;

    private function contractTable()
    {
        if(!$this->staffId){
            $staff = $this->getCurrentStaff();
            $this->staffId=boolval($staff)?$staff->getStaffId():0;
            $this->staffName=boolval($staff)?$staff->getStaffName():'';
        }
        return new ContractDataAccess($this->getDbAdapter(),$this->staffId);
    }
    private $currencyList;
    private $companyList;
    private $contactList;
    private $statusList;

    private function init_combos()
    {
        if(!$this->currencyList){
            $currencyDataAccess = new CurrencyDataAccess($this->getDbAdapter());
            $this->currencyList = $currencyDataAccess->getComboData('currencyId','code');
        }
        if(!$this->companyList){
            $companyDataAccess = new CompanyDataAccess($this->getDbAdapter()) ;
            $this->companyList = $companyDataAccess->getComboData('companyId','name');
        }
        if(!$this->contactList){
            $contactDataAccess = new ContactDataAccess($this->getDbAdapter());
            $this->contactList = $contactDataAccess->getComboData('contactId','name');
        }
        if(!$this->statusList){
            $constantDataAccess = new ConstantDataAccess($this->getDbAdapter());
            $this->statusList = $constantDataAccess->getComboByName('default_status');
        }
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','contractDate');
        $sortBy = $this->params()->fromQuery('by','desc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator=$this->contractTable()->fetchAll(true,$filter,$sort,$sortBy);
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
        $id = (int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action','');
        $helper = new ContractHelper($this->getDbAdapter());
        $form = $helper->getForm($this->currencyList, $this->companyList,
            $this->contactList, $this->statusList);

        $contract = $this->contractTable()->getContract($id);
        $isEdit = true;
        if(!$contract){
            $isEdit=false;
            $contract = new Contract();
        }
        if($action == 'clone'){
            $isEdit = false;
            $id =0;
            $contract->setContractId(0);
        }

        $form->bind($contract);
        $request = $this->getRequest();
        if($request->isPost()){
            $post_data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id, $post_data['code']));
            $post_data['contractBy'] = $this->staffId;
            if($form->isValid()){
                $this->contractTable()->saveContract($contract);
                $this->flashMessenger()->addSuccessMessage('Save successful');
                return $this->redirect()->toRoute('cr_contract');
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'isEdit'=>$isEdit
        ));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);

        $contract = $this->contractTable()->getContract($id);
        if($contract){
            $this->contractTable()->deleteContract($id);
            $this->flashMessenger()->addMessage('Delete successful!');
        }

        return $this->redirect()->toRoute("cr_contract");
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->contractTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Contract-' . date('YmdHis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return JsonModel
     */
    public function jsonDeleteAction()
    {
        $data=$this->params()->fromPost('chkId',array());
        $message="success";

        $db=$this->contractTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->contractTable()->deleteContract($id);
            }
            $conn->commit();
            $this->flashMessenger()->addMessage('Delete successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }
        return new JsonModel(array("message"=>$message));
    }
    public function downloadAction()
    {
        $id = (int)$this->params()->fromRoute('id', array());
        $contract = $this->contractTable()->getContract($id);
        if(!$contract){
            $this->flashMessenger()->addWarningMessage('Invalid id.');
            return $this->redirect()->toRoute('cr_contract');
        }
        $file = $contract->getContractFile();

        if(!file_exists($file)){
            $this->flashMessenger()->addWarningMessage('Invalid file.');
            return $this->redirect()->toRoute('cr_contract');
        }
        $reponse = $this->getResponse();
        $headers = $reponse->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/octet-stream');
        $headers->addHeaderLine('Content-Length', filesize($file));
        $headers->addHeaderLine('Content-Disposition', 'attachment; filename="' . basename($file) . '"');
        $reponse->setContent(file_get_contents($file));
        return $reponse;
    }
}