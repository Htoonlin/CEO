<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/22/2015
 * Time: 7:39 PM
 */

namespace Account\Controller;


use Account\DataAccess\ClosingDataAccess;
use Account\DataAccess\CurrencyDataAccess;
use Account\DataAccess\PayableDataAccess;
use Account\DataAccess\ReceivableDataAccess;
use Account\DataAccess\VoucherDataAccess;
use Account\Entity\Closing;
use Account\Entity\Payable;
use Account\Entity\Receivable;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Form\Element;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Uri\Http;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class BalanceController extends AbstractActionController
{
    private function voucherTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new VoucherDataAccess($dbAdapter);
    }

    private $staffId;
    private function getStaffId()
    {
        if(!$this->staffId){
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $userId=$this->layout()->current_user->userId;
            $staffDataAccess=new StaffDataAccess($dbAdapter);
            $staff=$staffDataAccess->getStaffByUser($userId);
            $this->staffId=boolval($staff) ? $staff->getStaffId():0;
        }

        return $this->staffId;
    }

    private function payableTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new PayableDataAccess($dbAdapter, $this->getStaffId());
    }

    private function receivableTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new ReceivableDataAccess($dbAdapter, $this->getStaffId());
    }

    private function closingTable()
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new ClosingDataAccess($dbAdapter);
    }

    private function currencyCombo($useAllCurrency = false, $selected = 0)
    {
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new CurrencyDataAccess($dbAdapter);
        $data = $dataAccess->getComboData('currencyId', 'name');
        $currencyCombo = new Element\Select('currency');
        if($useAllCurrency){
            $currencyCombo->setEmptyOption('All Currency');
        }
        $currencyCombo->setValueOptions($data);
        $currencyCombo->setAttributes(array('class' => 'form-control'));
        $currencyCombo->setValue($selected);
        return $currencyCombo;
    }

    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $currency=$this->params()->fromQuery('filter', 0);
        $sort=$this->params()->fromQuery('sort','openingDate');
        $sortBy=$this->params()->fromQuery('by','desc');

        $paginator=$this->closingTable()->fetchAll(true,$currency, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'page'=>$page,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter' => $currency,
            'currencyCombo' => $this->currencyCombo(true,$currency),
        ));
    }

    public function closeAction()
    {
        if($this->getRequest()->isPost())
        {
            $process = $this->params()->fromPost('req', '');
            $data = $this->params()->fromPost('data', '');
            $result = array();
            $gridColumns = array();

            if($process === 'collecting') {
                $value = '20%';
                $nextProcess = 'calculating';
                $message = 'Calculating amount by currency ...';
                $result = $this->getToCloseAccountData();
                $gridColumns = array('date', 'amount', 'currency');
            }else if($process === 'calculating'){
                $value = '50%';
                $nextProcess = 'process';
                $message = 'Waiting to commit process ...';
                $result = $this->getToOpenAccountData($data);
                $gridColumns = array('status', 'currency', 'receive', 'pay', 'amount');
            }else if($process === 'process'){
                $value = '55%';
                $nextProcess = 'commit';
                $message = 'Starting account closing process ...';
                $result = $data;
            }else{
                $this->closingProcess($data);
                $value = '100%';
                $nextProcess = '';
                $message = 'Finished process.';
            }

            return new JsonModel(array(
                'value' => $value,
                'nextProcess' => $nextProcess,
                'message' => $message,
                'result' => $result,
                'columns' => $gridColumns,
            ));
        }

        return new ViewModel();
    }

    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $page=(int)$this->params()->fromQuery('page',1);
        $filter=$this->params()->fromQuery('filter', '');
        $sort=$this->params()->fromQuery('sort','voucherNo');
        $sortBy=$this->params()->fromQuery('by','asc');

        $closing = $this->closingTable()->getClosing($id);

        if(!$closing){
            $this->flashMessenger()->addWarningMessage('Invalid request.');
            return $this->redirect()->toRoute('account_balance');
        }

        $fromDate = $closing->getOpeningDate();
        $toDate = (is_null($closing->getClosingDate())) ? date('Y-m-d H:i:s', time()) : $closing->getClosingDate();

        $paginator = $this->voucherTable()->getVouchersByDate($fromDate, $toDate,
            true, $filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'id' => $id,
            'paginator'=>$paginator,
            'page'=>$page,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter' => $filter
        ));
    }

    public function exportAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $requestUri = new Http($this->getRequest()->getHeader('Referer')->getUri());
        $fromPath = $requestUri->getPath();
        $closing = $this->closingTable()->getClosing($id);
        $filename = '';

        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        if(!$closing && $fromPath = '/account/balance'){
            $results = $this->closingTable()->fetchAll(false);
            $filename = 'attachment; filename="Closing-' . date('Ymdhis') . '.xlsx"';
        }else{
            $fromDate = $closing->getOpeningDate();
            $toDate = (is_null($closing->getClosingDate())) ? date('Y-m-d H:i:s', time()) : $closing->getClosingDate();
            $results = $this->voucherTable()->getVouchersByDate($fromDate, $toDate, false);
            $filename = 'attachment; filename="BalanceReport-' . date('Ymdhis') . '.xlsx"';
        }

        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($results as $row)
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

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }

    private function getToCloseAccountData()
    {
        $data = array();
        $results = $this->closingTable()->getOpenedData();

        foreach($results as $result)
        {
            $data[] = array(
                'closingId' => $result->closingId,
                'currencyId' => $result->currencyId,
                'date' => $result->openingDate,
                'amount' => $result->openingAmount,
                'currency' => $result->currency,
            );
        }

        return $data;
    }

    private function getToOpenAccountData($closeData)
    {
        $returnData = array();

        foreach($closeData as $close)
        {
            $results = $this->voucherTable()->getClosingData($close['date'], $close['currencyId']);
            $pay = 0;
            $receive = 0;

            foreach($results as $result)
            {
                if($result->type == 'Payable') $pay += $result->amount;
                if($result->type == 'Receivable') $receive += $result->amount;
            }

            $returnData[] = array(
                'closingId' => $close['closingId'],
                'currency' => $close['currency'],
                'currencyId' => $close['currencyId'],
                'receive' => $receive,
                'pay' => $pay,
                'amount' => ($receive - $pay),
                'status' => (($receive - $pay) == 0) ? 'C' : 'C=>O',
            );
        }

        $currencies = array();
        foreach($returnData as $data)
        {
            $currencies[] = $data['currencyId'];
        }

        $results = $this->closingTable()->getNewOpeningData($currencies);
        $prevCurId = 0;
        $pay = 0;
        $receive = 0;
        $currency = '';

        foreach($results as $result)
        {
            if($prevCurId > 0 && $prevCurId != $result->currencyId) {
                $returnData[] = array(
                    'closingId' => 0,
                    'currency' => $currency,
                    'currencyId' => $prevCurId,
                    'receive' => $receive,
                    'pay' => $pay,
                    'amount' => ($receive - $pay),
                    'status' => 'O',
                );
                $pay = 0;
                $receive = 0;
            }

            if($result->type == 'Payable') $pay = $result->amount;
            if($result->type == 'Receivable') $receive = $result->amount;

            $currency = $result->currency;
            $prevCurId = $result->currencyId;
        }
        if($prevCurId > 0){
            $returnData[] = array(
                'closingId' => 0,
                'currency' => $currency,
                'currencyId' => $prevCurId,
                'receive' => $receive,
                'pay' => $pay,
                'amount' => ($receive - $pay),
                'status' => 'O',
            );
        }

        return $returnData;
    }

    private function closingProcess($data)
    {
        $db = $this->closingTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        try{
            $conn->beginTransaction();
            foreach($data as $row)
            {
                if((int)$row['amount'] < 0) {
                    continue;
                }

                $voucherDate = date('Y-m-d', time());
                $dbTime = date('Y-m-d H:i:s', time());

                if($row['closingId'] > 0)
                {
                    $payVoucher = $this->payableTable()->getVoucherNo($voucherDate);
                    $payable = new Payable();
                    $payable->exchangeArray(array(
                        "voucherNo" => $payVoucher,
                        "voucherDate" => $voucherDate,
                        "accountType" => 30,
                        "description" => 'Automatic closing.',
                        "amount" => $row['amount'],
                        "currencyId"=> $row['currencyId'],
                        "withdrawBy" => $this->getStaffId(),
                        "approveBy" => $this->getStaffId(),
                        "status" => 'A',
                        "approvedDate" => $dbTime,
                        "requestedDate" => $dbTime
                    ));

                    $payable = $this->payableTable()->savePayable($payable);
                    $closeData = $this->closingTable()->getClosing($row['closingId']);
                    $closeData->setPayableId($payable->getPayVoucherId());
                    $closeData->setClosingDate($dbTime);
                    $closeData->setClosingAmount((int)$row['amount']);
                    $this->closingTable()->saveClosing($closeData);
                }

                $voucherDate = date('Y-m-d', strtotime('+1 day'));
                $dbTime = date('Y-m-d H:i:s', strtotime('+1 day'));
                $receiveVoucher = $this->receivableTable()->getVoucherNo($voucherDate);
                $receive = new Receivable();
                $receive->exchangeArray(array(
                    "voucherNo" => $receiveVoucher,
                    "voucherDate" => $voucherDate,
                    "accountType" => 29,
                    "description" => 'Automatic opening.',
                    "amount" => $row['amount'],
                    "currencyId"=> $row['currencyId'],
                    "depositBy" => $this->getStaffId(),
                    "approveBy" => $this->getStaffId(),
                    "status" => 'A',
                    "approvedDate" => $dbTime,
                    "requestedDate" => $dbTime,
                ));

                $receive = $this->receivableTable()->saveReceivable($receive);

                $opening = new Closing();
                $opening->setReceivableId($receive->getReceiveVoucherId());
                $opening->setCurrencyId($row['currencyId']);
                $opening->setOpeningDate($dbTime);
                $opening->setOpeningAmount($row['amount']);
                $this->closingTable()->saveClosing($opening);
            }
            $conn->commit();
            $this->flashMessenger()->addSuccessMessage('Account closing process successful.');
        }catch(\Exception $ex){
            $conn->rollback();
            $this->flashMessenger()->addErrorMessage($ex->getMessage());
            throw $ex;
        }
    }
}