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
use Application\Service\SundewExporting;
use HumanResource\DataAccess\StaffDataAccess;
use Account\DataAccess\ReceivableDataAccess;
use Account\Entity\Receivable;
use Account\Helper\ReceivableHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ReceivableController extends AbstractActionController
{
    private $staffId;
    private $staffName;
    private function receivableTable()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');

        if(!$this->staffId){
            $userId = $this->layout()->current_user->userId;
            $staffDataAccess = new StaffDataAccess($adapter);
            $staff = $staffDataAccess->getStaffByUser($userId);
            $this->staffId = boolval($staff) ? $staff->getStaffId() : 0;
            $this->staffName = boolval($staff) ? $staff->getStaffName() : '';
        }

        return new ReceivableDataAccess($adapter, $this->staffId);
    }

    private function accountTypes()
    {
        $adapter=$this->getServiceLocator()->get('Sundew\Db\Adapter');
        $dataAccess=new AccountTypeDataAccess($adapter);
        return $dataAccess->getChildren();
    }

    private function currencyCombo()
    {
        $adapter = $this->getServiceLocator()->get('Sundew\Db\Adapter');
        $dataAccess = new CurrencyDataAccess($adapter);
        return $dataAccess->getComboData('currencyId', 'code');
    }

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

    public function jsonGenerateAction()
    {
        $date = $this->params()->fromPost('date',date('Y-m-d', time()));
        return new JsonModel(array('receive'=> $date,
            'generatedNo' => $this->receivableTable()->getVoucherNo($date)));
    }

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
    public function exportAction()
    {
        $export = new SundewExporting($this->receivableTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Receivable-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }
}