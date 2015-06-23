<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:34 PM
 */

namespace CustomerRelation\Controller;

use Account\DataAccess\CurrencyDataAccess;
use CustomerRelation\DataAccess\ProposalDataAccess;
use CustomerRelation\Entity\Proposal;
use CustomerRelation\Helper\ProposalHelper;
use CustomerRelation\DataAccess\CompanyDataAccess;
use CustomerRelation\DataAccess\ContactDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Config\Reader\Json;
use Zend\Crypt\Password\Apache;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\RenameUpload;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Fieldset;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
class ProposalController extends AbstractActionController
{
    private $staffId;
    private $staffName;
    private function proposalTable()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(!$this->staffId){
            $userId=$this->layout()->current_user->userId;
            $staffDataAccess=new StaffDataAccess($adapter);
            $staff=$staffDataAccess->getStaffByUser($userId);
            $this->staffId=boolval($staff)?$staff->getStaffId():0;
            $this->staffName=boolval($staff)?$staff->getStaffName():'';
        }
        return new ProposalDataAccess($adapter,$this->staffId);
    }
    private function currencyCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CurrencyDataAccess($adapter);
        return $dataAccess->getComboData('currencyId','code');
    }
    private function companyCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CompanyDataAccess($adapter);
        return $dataAccess->getComboData('companyId','name');
    }
    private function contactCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new ContactDataAccess($adapter);
        return $dataAccess->getComboData('contactId','name');
    }
    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort','proposalDate');
        $sortBy=$this->params()->fromQuery('by','dsc');
        $filter=$this->params()->fromQuery('filter','');
        $paginator=$this->proposalTable()->fetchAll(true,$filter,$sort,$sortBy);
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
        $proposal=$this->proposalTable()->getProposalView($id);

        if($proposal==null){
            $this->flashMessenger()->addMessage('Invalid proposal voucher.');
            return $this->redirect()->toRoute('cr_proposal');
        }
        return new ViewModel(array(
            'proposal'=>$proposal,
            'staffName'=>$this->staffName,
        ));
    }
    public function requestAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $helper = new ProposalHelper($this->proposalTable());
        $form = $helper->getForm($this->currencyCombos(),$this->companyCombos(),$this->contactCombos());
        $proposal = $this->proposalTable()->getProposalView($id);
        $isEdit = true;
        $hasFile = 'false';
        $currentFile = "";

        if(!$proposal){
            $isEdit = false;
            $proposal = new Proposal();
        }else{
            $hasFile = is_null($proposal->getProposalFile()) ? 'false' : 'true';
            $currentFile = $proposal->getProposalFile();
        }
        $form->bind($proposal);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter(($isEdit ? $post_data['proposalId'] : 0), $post_data['code']));
            if($form->isValid()){
                $file = $proposal->getProposalFile();
                if($post_data['proposalFile'] ==  'false' && empty($file['name'])){
                    $proposal->setContractFile(null);
                }else if($post_data['proposalFile'] == 'true' && empty($file['name']) && $isEdit){
                    $proposal->setProposalFile($currentFile);
                }
                $this->proposalTable()->saveProposal($proposal);
                $this->flashMessenger()->addMessage('Save successful');
                return $this->redirect()->toRoute('cr_proposal');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'proposal'=>$proposal,
            'isEdit' => $isEdit,
            'hasFile' => $hasFile,
            'staffName'=>$this->staffName,));

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
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        $data = $this->proposalTable()->fetchAll(false);
        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($data as $row)
        {
            if(count($columns) == 0){
                $columns = array_keys($row->getArrayCopy());
            }
            foreach($columns as $col){
                $cellId = $excelColumn . $start;
                $sheet->setCellValue($cellId, $row->$col);
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

        $filename = 'attachment; filename="Proposal-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }
}