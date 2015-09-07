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
use Application\DataAccess\ConstantDataAccess;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use Account\DataAccess\PayableDataAccess;
use Account\Entity\Payable;
use Account\Helper\PayableHelper;
use Zend\View\Model\ViewModel;

class PayableController extends SundewController
{
    /**
     * @var
     */
    private $staffId;
    /**
     * @var
     */
    private $staffName;

    /**
     * @return PayableDataAccess
     */
    private function payableTable()
    {
        if(!$this->staffId){
            $staff = $this->getCurrentStaff();
            $this->staffId=boolval($staff) ? $staff->getStaffId():0;
            $this->staffName=boolval($staff) ? $staff->getStaffName():'';
        }
        return new PayableDataAccess($this->getDbAdapter(), $this->staffId);
    }

    /**
     * @return array
     */
    private function accountTypes()
    {
        $dataAccess=new AccountTypeDataAccess($this->getDbAdapter());
        return $dataAccess->getChildren("E");
    }

    /**
     * @var
     */
    private $currencyList;
    /**
     * @var
     */
    private $paymentList;

    /**
     *
     */
    private function init_combos()
    {
        if(!$this->currencyList){
            $currencyDataAccess = new CurrencyDataAccess($this->getDbAdapter());
            $this->currencyList = $currencyDataAccess->getComboData('currencyId', 'code');
        }
        if(!$this->paymentList){
            $paymentDataAccess = new ConstantDataAccess($this->getDbAdapter());
            $this->paymentList = $paymentDataAccess->getComboByName('payment_type');
        }
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','voucherDate');
        $sortBy = $this->params()->fromQuery('by','desc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

        $paginator=$this->payableTable()->fetchAll(true,$filter,$sort,$sortBy);
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
     * @return ApiModel
     */
    public function apiGenerateAction()
    {
        $date = $this->params()->fromPost('date',date('Y-m-d', time()));
        return new ApiModel(array('payable'=>$date,
            'generatedNo' => $this->payableTable()->getVoucherNo($date)));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
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
            'accountTypes' => $this->accountTypes(),
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function requestAction()
    {
        $this->init_combos();
        $helper=new PayableHelper($this->payableTable()->getAdapter());
        $form=$helper->getForm($this->currencyList, $this->paymentList);
        $payable=new Payable();

        $generateNo=$this->payableTable()->getVoucherNo(date('Y-m-d',time()));
        $payable->setVoucherNo($generateNo);
        $payable->setWithdrawBy($this->staffId);
        $form->bind($payable);

        $request=$this->getRequest();
        if($request->isPost()){
            $post_data=array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($post_data['voucherNo']));
            if($form->isValid()){
                $generateNo = $this->payableTable()->getVoucherNo($payable->getVoucherDate());
                $payable->setVoucherNo($generateNo);
                $this->payableTable()->savePayable($payable);
                $this->flashMessenger()->addInfoMessage('Requested voucher by' . $generateNo .'.');
                return $this->redirect()->toRoute('account_payable');
            }
        }
        return new ViewModel(array(
            'form'=>$form,
            'staffName'=>$this->staffName,
            'accountTypes' => $this->accountTypes(),
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->payableTable()->fetchAll(false));

        $response = $this->getResponse();
        $filename = 'attachment; filename="Payable-' . date('YmdHis') . '.xlsx"';
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function downloadAction()
    {
        $id = (int)$this->params()->fromRoute('id', array());
        $payable = $this->payableTable()->getPayable($id);
        if(!$payable){
            $this->flashMessenger()->addWarningMessage('Invalid id.');
            return $this->redirect()->toRoute('account_payable');
        }
        $file = $payable->getAttachmentFile();

        if(!file_exists($file)){
            $this->flashMessenger()->addWarningMessage('Invalid file.');
            return $this->redirect()->toRoute('account_payable');
        }
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/octet-stream');
        $headers->addHeaderLine('Content-Length', filesize($file));
        $headers->addHeaderLine('Content-Disposition', 'attachment; filename="' . basename($file) . '"');
        $response->setContent(file_get_contents($file));
        return $response;
    }
}