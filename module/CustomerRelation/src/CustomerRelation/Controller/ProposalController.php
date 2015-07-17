<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:34 PM
 */

namespace CustomerRelation\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewExporting;
use CustomerRelation\DataAccess\ProposalDataAccess;
use CustomerRelation\Entity\Proposal;
use CustomerRelation\Helper\ProposalHelper;
use CustomerRelation\DataAccess\CompanyDataAccess;
use CustomerRelation\DataAccess\ContactDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProposalController extends AbstractActionController
{
    private $staffId;
    private function proposalTable()
    {
        $adapter=$this->getServiceLocator()->get('Sundew\Db\Adapter');
        if(!$this->staffId){
            $userId=$this->layout()->current_user->userId;
            $staffDataAccess=new StaffDataAccess($adapter);
            $staff=$staffDataAccess->getStaffByUser($userId);
            $this->staffId=boolval($staff)?$staff->getStaffId():0;
        }
        return new ProposalDataAccess($adapter,$this->staffId);
    }
    private $currencyList;
    private $companyList;
    private $contactList;
    private $statusList;

    private function init_combos()
    {
        $adapter=$this->getServiceLocator()->get('Sundew\Db\Adapter');
        if(!$this->currencyList){
            $currencyDataAccess = new CurrencyDataAccess($adapter);
            $this->currencyList = $currencyDataAccess->getComboData('currencyId', 'code');
        }

        if(!$this->companyList){
            $companyDataAccess = new CompanyDataAccess($adapter);
            $this->companyList = $companyDataAccess->getComboData('companyId', 'name');
        }

        if(!$this->contactList){
            $contactDataAccess = new ContactDataAccess($adapter);
            $this->contactList = $contactDataAccess->getComboData('contactId', 'name');
        }

        if(!$this->statusList){
            $constantDataAccess = new ConstantDataAccess($adapter);
            $this->statusList = $constantDataAccess->getComboByName('default_status');
        }
    }

    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','proposalDate');
        $sortBy = $this->params()->fromQuery('by','desc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator = $this->proposalTable()->fetchAll(true,$filter,$sort,$sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter'=>$filter,
        ));
    }
    public function detailAction()
    {
        $this->init_combos();
        $id = (int)$this->params()->fromRoute('id', 0);

        $helper = new ProposalHelper($this->proposalTable()->getAdapter());
        $form = $helper->getForm($this->currencyList, $this->companyList,
            $this->contactList, $this->statusList);
        $proposal = $this->proposalTable()->getProposal($id);
        $isEdit = true;
        if(!$proposal){
            $isEdit= false;
            $proposal = new Proposal();
        }

        $form->bind($proposal);
        $request = $this->getRequest();
        if($request->isPost()){
            $post_data = array_merge_recursive(
                $request->getPost()->toArray(),
            $request->getFiles()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id, $post_data['code']));
            $post_data['proposalBy'] = $this->staffId;
            if($form->isValid()){
                $this->proposalTable()->saveProposal($proposal);
                $this->flashMessenger()->addSuccessMessage('Save successful.');
                return $this->redirect()->toRoute('cr_proposal');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'isEdit' => $isEdit
        ));
    }

    public function deleteAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);

        $proposal=$this->proposalTable()->getProposal($id);
        if($proposal){
            $this->proposalTable()->deleteProposal($id);
            $this->flashMessenger()->addMessage('Delete successful!');
        }

        return $this->redirect()->toRoute("cr_proposal");
    }
    public function jsonDeleteAction()
    {
        $data=$this->params()->fromRoute('chkId',array());
        $message="success";

        $db=$this->proposalTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();

        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->proposalTable()->deleteProposal($id);
            }
            $conn->commit();
            $this->flashMessenger()->addMessage('Delete successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }
        return new JsonModel(array("message"=>$message));
    }
    public function exportAction()
    {
        $export = new SundewExporting($this->proposalTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Proposal-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }
}