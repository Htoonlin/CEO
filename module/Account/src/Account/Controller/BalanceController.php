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
use Application\DataAccess\ConstantDataAccess;
use Core\SundewController;
use Core\SundewExporting;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Form\Element;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Uri\Http;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class BalanceController
 * @package Account\Controller
 */
class BalanceController extends SundewController
{
    /**
     * @return VoucherDataAccess
     */
    private function voucherTable()
    {
        return new VoucherDataAccess($this->getDbAdapter());
    }

    private $staffId;

    /**
     * @return int
     */
    private function getStaffId()
    {
        if(!$this->staffId){
            $staff=$this->getCurrentStaff();
            $this->staffId=boolval($staff) ? $staff->getStaffId():0;
        }

        return $this->staffId;
    }

    /**
     * @return CurrencyDataAccess
     */
    private function currencyTable()
    {
        return new CurrencyDataAccess($this->getDbAdapter());
    }

    /**
     * @return PayableDataAccess
     */
    private function payableTable()
    {
        return new PayableDataAccess($this->getDbAdapter(), $this->getStaffId());
    }

    /**
     * @return ReceivableDataAccess
     */
    private function receivableTable()
    {
        return new ReceivableDataAccess($this->getDbAdapter(), $this->getStaffId());
    }

    /**
     * @return ClosingDataAccess
     */
    private function closingTable()
    {
        return new ClosingDataAccess($this->getDbAdapter());
    }

    /**
     * @param $type
     * @return int
     */
    private function getClosingTypes($type)
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        $result = json_decode($dataAccess->getConstantByName('closing_type_id')->getValue());

        if($result){
            return $result->{$type};
        }

        return 0;
    }

    /**
     * @param bool $useAllCurrency
     * @param int $selected
     * @return Element\Select
     */
    private function currencyCombo($useAllCurrency = false, $selected = 0)
    {
        $dataAccess = new CurrencyDataAccess($this->getDbAdapter());
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

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $currency = $this->params()->fromQuery('filter', 0);
        $sort = $this->params()->fromQuery('sort','openingDate');
        $sortBy = $this->params()->fromQuery('by','desc');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator=$this->closingTable()->fetchAll(true,$currency, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter' => $currency,
            'currencyCombo' => $this->currencyCombo(true,$currency),
        ));
    }

    /**
     * @return JsonModel|ViewModel
     * @throws \Exception
     */
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

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $page=(int)$this->params()->fromQuery('page',1);
        $filter=$this->params()->fromQuery('filter', '');
        $sort=$this->params()->fromQuery('sort','voucherNo');
        $sortBy=$this->params()->fromQuery('by','asc');
        $pageSize = $this->params()->fromQuery('size', 10);

        $closing = $this->closingTable()->getClosing($id);

        if(!$closing){
            $this->flashMessenger()->addWarningMessage('Invalid request.');
            return $this->redirect()->toRoute('account_balance');
        }

        $currency = $closing->getCurrencyId();
        $fromDate = $closing->getOpeningDate();
        $toDate = (is_null($closing->getClosingDate())) ? date('Y-m-d 23:59:59', time()) : $closing->getClosingDate();

        $paginator = $this->voucherTable()->getVouchersByDate($fromDate, $toDate, $currency, array(),
            true, $filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'id' => $id,
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter' => $filter
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     * @throws \Exception
     */
    public function exportAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $closing = $this->closingTable()->getClosing($id);

        $response = $this->getResponse();

        if(!$closing && $fromPath = '/account/balance'){
            $results = $this->closingTable()->fetchAll(false);
            $filename = 'attachment; filename="Closing-' . date('YmdHis') . '.xlsx"';
        }else{
            $currency = $closing->getCurrencyId();
            $fromDate = $closing->getOpeningDate();
            $toDate = (is_null($closing->getClosingDate())) ? date('Y-m-d H:i:s', time()) : $closing->getClosingDate();
            $results = $this->voucherTable()->getVouchersByDate($fromDate, $toDate, $currency, array(), false);
            $filename = 'attachment; filename="BalanceReport-' . date('YmdHis') . '.xlsx"';
        }

        $export = new SundewExporting($results);
        $excel = $export->getExcel();

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excel);

        return $response;
    }

    /**
     * @return array
     */
    private function getToCloseAccountData()
    {
        $data = array();
        $results = $this->closingTable()->getOpenedData();
        foreach($results as $result)
        {
            $data[] = array(
                'closingId' => $result->closingId,
                'date' => $result->openingDate,
                'amount' => $result->openingAmount,
                'currency' => $result->currency,
            );
        }

        return $data;
    }

    /**
     * @param $closeData
     * @return array
     */
    private function getToOpenAccountData($closeData)
    {
        $returnData = array();

        if(!empty($closeData)){
            foreach($closeData as $close)
            {
                $results = $this->voucherTable()->getClosingData($close['date'], $close['currency']);
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
                    'receive' => $receive,
                    'pay' => $pay,
                    'amount' => ($receive - $pay),
                    'status' => (($receive - $pay) == 0) ? 'C' : 'C=>O',
                );
            }
        }

        $currencies = array();

        foreach($returnData as $data)
        {
            $currencies[] = $data['currency'];
        }

        $results = $this->closingTable()->getNewOpeningData($currencies);
        $pay = 0;
        $receive = 0;
        $currency = '';
        foreach($results as $result)
        {
            if($currency != $result->currency && !empty($currency)){
                $returnData[] = array(
                    'closingId' => 0,
                    'currency' => $currency,
                    'receive' => $receive,
                    'pay' => $pay,
                    'amount' => ($receive - $pay),
                    'status' =>  'O=>C=>O',
                );

                $receive = 0;
                $pay = 0;
            }

            if($result->type == 'Payable') $pay = $result->amount;
            if($result->type == 'Receivable') $receive = $result->amount;

            $currency = $result->currency;
        }

        if(!empty($currency)){
            $returnData[] = array(
                'closingId' => 0,
                'currency' => $currency,
                'receive' => $receive,
                'pay' => $pay,
                'amount' => ($receive - $pay),
                'status' => 'O=>C=>O',
            );
        }

        return $returnData;
    }

    /**
     * @param $data
     * @throws \Exception
     */
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

                if(empty($row['closingId']))
                {
                    /*
                     * Opening Process by first-voucher date
                     */
                    $initVoucher = $this->voucherTable()->getInitVoucherByNewCurrency($row['currency']);
                    $openDate = empty($initVoucher['approvedDate']) ? $initVoucher['requestedDate'] : $initVoucher['approvedDate'];
                    $opening = new Closing();
                    $opening->setReceivableId($initVoucher['voucherId']);
                    $opening->setCurrencyId($initVoucher['currencyId']);
                    $opening->setOpeningDate(date('Y-m-d 00:00:59', strtotime($openDate)));
                    $opening->setOpeningAmount($initVoucher['amount']);
                    $closing = $this->closingTable()->saveClosing($opening);
                    $row['closingId'] = $closing->getClosingId();
                }

                $voucherDate = date('Y-m-d', time());
                $dbTime = date('Y-m-d 23:59:59', time());
                $currencyId = $this->currencyTable()->getLastCurrency($row['currency'])->getCurrencyId();
                /*
                 * Closing Process
                 */
                $payVoucher = $this->payableTable()->getVoucherNo($voucherDate);
                $payable = new Payable();
                $payable->exchangeArray(array(
                    "voucherNo" => $payVoucher,
                    "voucherDate" => $voucherDate,
                    "accountType" => $this->getClosingTypes('close'),
                    "description" => 'Automatic closing.',
                    "amount" => $row['amount'],
                    "currencyId"=> $currencyId,
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

                /*
                 * Reopening Process in next day
                 */
                $voucherDate = date('Y-m-d', strtotime('+1 day'));
                $dbTime = date('Y-m-d 00:00:59', strtotime('+1 day'));
                $receiveVoucher = $this->receivableTable()->getVoucherNo($voucherDate);
                $receive = new Receivable();
                $receive->exchangeArray(array(
                    "voucherNo" => $receiveVoucher,
                    "voucherDate" => $voucherDate,
                    "accountType" =>  $this->getClosingTypes('open'),
                    "description" => 'Automatic opening.',
                    "amount" => $row['amount'],
                    "currencyId"=> $currencyId,
                    "depositBy" => $this->getStaffId(),
                    "approveBy" => $this->getStaffId(),
                    "status" => 'A',
                    "approvedDate" => $dbTime,
                    "requestedDate" => $dbTime,
                ));

                $receive = $this->receivableTable()->saveReceivable($receive);

                $opening = new Closing();
                $opening->setReceivableId($receive->getReceiveVoucherId());
                $opening->setCurrencyId($currencyId);
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