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
use Application\Service\SundewExporting;
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
    private function accountTypes()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new AccountTypeDataAccess($adapter);
        return $dataAccess->getChildren();
    }

    public function currencyCombo()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CurrencyDataAccess($adapter);
        return $dataAccess->getComboData('currencyId','code');
    }
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','voucherDate');
        $sortBy = $this->params()->fromQuery('by','desc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

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
            'accountTypes' => $this->accountTypes(),
        ));
    }
    public function requestAction()
    {
        $helper=new PayableHelper($this->payableTable());
        $form=$helper->getForm($this->currencyCombo());
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
            'accountTypes' => $this->accountTypes(),
        ));
    }
    public function exportAction()
    {
        $export = new SundewExporting($this->payableTable()->fetchAll(false));

        $response = $this->getResponse();
        $filename = 'attachment; filename="Payable-' . date('Ymdhis') . '.xlsx"';
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

}