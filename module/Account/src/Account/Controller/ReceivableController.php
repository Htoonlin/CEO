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
use Core\SundewController;
use Core\SundewExporting;
use HumanResource\DataAccess\StaffDataAccess;
use Account\DataAccess\ReceivableDataAccess;
use Account\Entity\Receivable;
use Account\Helper\ReceivableHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
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

    /**
     * @return array
     */
    private function currencyCombo()
    {
        $dataAccess = new CurrencyDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('currencyId', 'code');
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
     * @return JsonModel
     */
    public function jsonGenerateAction()
    {
        $date = $this->params()->fromPost('date',date('Y-m-d', time()));
        return new JsonModel(array('receive'=> $date,
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
        $helper = new ReceivableHelper($this->receivableTable());
        $form = $helper->getForm($this->currencyCombo());
        $receivable = new Receivable();
        $generateNo = $this->receivableTable()->getVoucherNo(date('Y-m-d', time()));
        $receivable->setVoucherNo($generateNo);
        $receivable->setDepositBy($this->staffId);
        $form->bind($receivable);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($helper->getInputFilter());
            $post_data = $request->getPost()->toArray();
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
}