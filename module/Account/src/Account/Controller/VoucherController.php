<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/25/2015
 * Time: 10:51 AM
 */

namespace Account\Controller;

use Account\DataAccess\PayableDataAccess;
use Account\DataAccess\ReceivableDataAccess;
use Account\DataAccess\VoucherDataAccess;
use Account\Entity\Voucher;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class VoucherController extends AbstractActionController
{
    private function voucherTable()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new VoucherDataAccess($adapter);
    }
    private $staffId;
    private function receivableTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        if(!$this->staffId && $this->staffId <= 0){
            $userId = $this->layout()->current_user->userId;
            $staffDataAccess = new StaffDataAccess($adapter);
            $staff = $staffDataAccess->getStaffByUser($userId);
            $this->staffId = boolval($staff) ? $staff->getStaffId() : 0;
        }

        return new ReceivableDataAccess($adapter, $this->staffId);
    }
    private function payableTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        if(!$this->staffId && $this->staffId <= 0){
            $userId = $this->layout()->current_user->userId;
            $staffDataAccess = new StaffDataAccess($adapter);
            $staff = $staffDataAccess->getStaffByUser($userId);
            $this->staffId = boolval($staff) ? $staff->getStaffId() : 0;
        }

        return new PayableDataAccess($adapter, $this->staffId);
    }

    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort','voucherDate');
        $sortBy=$this->params()->fromQuery('by','desc');
        $filter=$this->params()->fromQuery('filter','');
        $paginator=$this->voucherTable()->fetchAll(true,$filter,$sort,$sortBy);
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

    public  function  detailAction()
    {
        $voucherNo = $this->params()->fromRoute('voucher','');
        $voucher = $this->voucherTable()->getVoucher($voucherNo);

        $backPath = $this->getRequest()->getHeader('Referer')->getUri();

        if($voucher == null){
            $this->flashMessenger()->addWarningMessage('Invalid  voucher.');
            return $this->redirect()->toRoute('account_voucher');
        }

        return new ViewModel(array(
            'voucher'=>$voucher,
            'redirect' => $backPath,
        ));
    }
    public function approveAction()
    {
        $voucherNo = $this->params()->fromRoute('voucher','');
        $voucher = $this->voucherTable()->getVoucher($voucherNo);

        if($voucher == null){
            $this->flashMessenger()->addWarningMessage('Invalid  voucher.');
        }else{
            if($voucher->type == 'Receivable'){
                $approveVoucher = $this->receivableTable()->getReceivable($voucher->voucherId, false);
                if($approveVoucher){
                    $approveVoucher->setStatus('A');
                    $this->receivableTable()->saveReceivable($approveVoucher);
                }
            }else{
                $approveVoucher = $this->payableTable()->getPayable($voucher->voucherId, false);
                if($approveVoucher){
                    $approveVoucher->setStatus('A');
                    $this->payableTable()->savePayable($approveVoucher);
                }
            }
            $this->flashMessenger()->addInfoMessage('Approved ' . $voucherNo . ' for ' . $voucher->requester . '.');
        }

        return $this->redirect()->toRoute('account_voucher');
    }
    public function cancelAction()
    {
        $voucherNo = $this->params()->fromRoute('voucher','');
        $voucher = $this->voucherTable()->getVoucher($voucherNo);
        $request = $this->getRequest();
        $message = "";

        if($request->isPost()){
            $reason = $this->params()->fromPost('reason', '');
            if(empty($reason)){
                $message = "Voucher can't be canceled without reason.";
            }else{
                if($voucher->type == 'Receivable'){
                    $approveVoucher = $this->receivableTable()->getReceivable($voucher->voucherId, false);
                    if($approveVoucher){
                        $approveVoucher->setReason($reason);
                        $approveVoucher->setStatus('C');
                        $this->receivableTable()->saveReceivable($approveVoucher);
                    }
                }else{
                    $approveVoucher = $this->payableTable()->getPayable($voucher->voucherId, false);
                    if($approveVoucher){
                        $approveVoucher->setReason($reason);
                        $approveVoucher->setStatus('C');
                        $this->payableTable()->savePayable($approveVoucher);
                    }
                }
                $this->flashMessenger()->addInfoMessage('Canceled ' . $voucherNo . ' for ' . $voucher->requester . '.');
                return $this->redirect()->toRoute('account_voucher');
            }
        }

        return new ViewModel(array(
            'voucher'=>$voucher,
            'message'=>$message,
        ));
    }

    public function exportAction()
    {
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

       $vouchers = $this->voucherTable()->fetchAll(false);
        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($vouchers as $row)
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

        $filename = 'attachment; filename="Voucher-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }
}
