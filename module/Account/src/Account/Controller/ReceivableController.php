<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/26/2015
 * Time: 3:19 PM
 */

namespace Account\Controller;

use Account\DataAccess\AccountTypeDataAccess;
use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use Account\DataAccess\ReceivableDataAccess;
use Account\Entity\Receivable;
use Account\Helper\ReceivableHelper;
use Zend\View\Model\ViewModel;

/**
 * Class ReceivableController
 * @package Account\Controller
 */
class ReceivableController extends SundewController
{
    private $staffId;
    private $staffName;

    /**
     * @return ReceivableDataAccess
     */
    private function receivableTable()
    {
        if(!$this->staffId){
            $staff = $this->getCurrentStaff();
            $this->staffId = boolval($staff) ? $staff->getStaffId() : 0;
            $this->staffName = boolval($staff) ? $staff->getStaffName() : '';
        }

        return new ReceivableDataAccess($this->getDbAdapter(), $this->staffId);
    }

    /**
     * @return array
     */
    private function accountTypes()
    {
        $dataAccess=new AccountTypeDataAccess($this->getDbAdapter());
        return $dataAccess->getChildren("I");
    }
    private $currencyList;
    private $constantList;
    private $paymentTypeList;
    private function init_combos()
    {
        if(!$this->currencyList){
            $currencyDataAccess = new CurrencyDataAccess($this->getDbAdapter());
            $this->currencyList = $currencyDataAccess->getComboData('currencyId', 'code');
        }
        if(!$this->paymentTypeList){
            $paymentTypeDataAccess = new ConstantDataAccess($this->getDbAdapter());
            $this->paymentTypeList = $paymentTypeDataAccess->getComboByName('payment_type');
        }
    }


    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'voucherDate');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator = $this->receivableTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);
        return new ViewModel(array(
            'paginator' => $paginator,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter'=>$filter,
        ));
    }

    /**
     * @return ApiModel
     */
    public function apiGenerateAction()
    {
        $date = $this->params()->fromPost('date',date('Y-m-d', time()));
        return new ApiModel(array('receive'=> $date,
            'generatedNo' => $this->receivableTable()->getVoucherNo($date)));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $recievable = $this->receivableTable()->getReceivableView($id);

        if($recievable == null){
            $this->flashMessenger()->addWarningMessage('Invalid receivable voucher.');
            return $this->redirect()->toRoute('account_receivable');
        }
        return new ViewModel(array(
            'voucher' => $recievable,
            'staffName' => $this->staffName,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function requestAction()
    {
        $this->init_combos();
        $helper = new ReceivableHelper($this->receivableTable());
        $form = $helper->getForm($this->currencyList, $this->paymentTypeList);
        $receivable = new Receivable();
        $generateNo = $this->receivableTable()->getVoucherNo(date('Y-m-d', time()));
        $receivable->setVoucherNo($generateNo);
        $receivable->setDepositBy($this->staffId);
        $form->bind($receivable);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post_data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setInputFilter($helper->getInputFilter($post_data['voucherNo']));
            $form->setData($post_data);
            if ($form->isValid()) {
                $generateNo = $this->receivableTable()->getVoucherNo($receivable->getVoucherDate());
                $receivable->setVoucherNo($generateNo);
                $this->receivableTable()->saveReceivable($receivable);
                $this->flashMessenger()->addInfoMessage('Requested voucher by ' . $generateNo . '.');
                return $this->redirect()->toRoute('account_receivable');
            }
        }
        return new ViewModel(array(
            'form' => $form,
            'staffName' => $this->staffName,
            'accountTypes' => $this->accountTypes(),
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->receivableTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Receivable-' . date('YmdHis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }
    public function downloadAction()
    {
        $id = (int)$this->params()->fromRoute('id', array());
        $receivable = $this->receivableTable()->getReceivable($id);
        if(!$receivable){
            $this->flashMessenger()->addWarningMessage('Invalid id. ');
            return $this->redirect()->toRoute('account_receivable');
        }
        $file = $receivable->getAttachmentFile();

        if(!file_exists($file)){
            $this->flashMessenger()->addWarningMessage('Invalid file. ');
            return $this->redirect()->toRoute('account_receivable');
        }
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type','application/octet-stream');
        $headers->addHeaderLine('Content-Length', filesize($file));
        $headers->addHeaderLine('Content-Disposition', 'attachment; filename="' . basename($file) . '"');
        $response->setContent(file_get_contents($file));
        return $response;
    }
}