<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/5/2015
 * Time: 5:01 AM
 */

namespace Account\Controller;

use Account\DataAccess\AccountTypeDataAccess;
use Account\DataAccess\CurrencyDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Account\DataAccess\PayableDataAccess;
use Account\Entity\Payable;
use Account\Helper\PayableHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PayableController extends  AbstractActionController
{
    private $staffId;
    private $staffName;
    private function payableTable()
    {
        $adapter=$this->getServiceLocator()->get('Zend\DB\Adapter\Adapter');

        if(!$this->staffId){
            $userId=$this->layout()->current_user->userId;
            $staffDataAccess=new StaffDataAccess($adapter);
            $staff=$staffDataAccess->getStaffByUser($userId);
            $this->staffId=boolval($staff) ? $staff->getStaffId():0;
            $this->staffName=boolval($staff) ? $staff->getStaffName():'';
        }
        return new PayableDataAccess($adapter, $this->staffId);
    }
    private function accountTypeCombo()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new AccountTypeDataAccess($adapter);
        return $dataAccess->getComboData('accountTypeId','name','E');
    }

    public function currencyCombo()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CurrencyDataAccess($adapter);
        return $dataAccess->getComboData('currencyId','code');
    }
    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort','voucherDate');
        $sortBy=$this->params()->fromQuery('by','desc');
        $filter=$this->params()->fromQuery('filter','');
        $paginator=$this->payableTable()->fetchAll(true,$filter,$sort,$sortBy);
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
    public function jsonGenerateAction()
    {
        $date = $this->params()->fromPost('date',date('Y-m-d', time()));
        return new JsonModel(array('payable'=>$date,
            'generatedNo' => $this->payableTable()->getVoucherNo($date)));
    }

    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $payable=$this->payableTable()->getPayableView($id);

       if($payable==null){
           $this->flashMessenger()->addWarningMessage('Invalid payable voucher.');
           return $this->redirect()->toRoute('account_payable');
       }
        return new ViewModel(array(
            'voucher'=>$payable,
            'staffName'=>$this->staffName,
        ));
    }
    public function requestAction()
    {
        $helper=new PayableHelper($this->payableTable());
        $form=$helper->getForm($this->accountTypeCombo(),$this->currencyCombo());
        $payable=new Payable();
        $generateNo=$this->payableTable()->getVoucherNo(date('Y-m-d',time()));
        $payable->setVoucherNo($generateNo);
        $payable->setWithdrawBy($this->staffId);
        $form->bind($payable);

        $request=$this->getRequest();
        if($request->isPost()){
            $form->setInputFilter($helper->getInputFilter());
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            if($form->isValid()){
                $this->payableTable()->savePayable($payable);
                $this->flashMessenger()->addInfoMessage('Requested voucher by' . $generateNo .'.');
                return $this->redirect()->toRoute('account_payable');
            }
        }
        return new ViewModel(array(
            'form'=>$form,
            'staffName'=>$this->staffName,
        ));
    }
    public function exportAction()
    {
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        $payables = $this->payableTable()->fetchAll(false);
        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($payables as $row)
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

        $filename = 'attachment; filename="Payable-' . date('Ymdhms') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }

}