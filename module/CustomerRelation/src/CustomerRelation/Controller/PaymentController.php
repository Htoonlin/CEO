<?php
/**
 * Created by PhpStorm.
 * User: kmk
 * Date: 8/17/2015
 * Time: 2:34 PM
 */

namespace CustomerRelation\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Core\Model\ApiModel;
use CustomerRelation\DataAccess\ContactDataAccess;
use CustomerRelation\DataAccess\ContractDataAccess;
use CustomerRelation\DataAccess\PaymentDataAccess;
use Application\DataAccess\ConstantDataAccess;
use CustomerRelation\Entity\Payment;
use CustomerRelation\Helper\PaymentHelper;
use Core\SundewController;
use Core\SundewExporting;
use Zend\Form\Element;
use Zend\View\Model\ViewModel;

/**
 * Class PaymentController
 * @package CustomerRelation\Controller
 */
class PaymentController extends SundewController
{
    /**
     * @return ContractDataAccess
     */
    private function contractTable()
    {
        return new ContractDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);

    }

    /**
     * @return PaymentDataAccess
     */
    private function paymentTable()
    {
        return new PaymentDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
    }

    /**
     * @var
     */
    private $currencyList;
    /**
     * @var
     */
    private $contactList;
    /**
     * @var
     */
    private $contractList;
    /**
     * @var
     */
    private $statusList;

    /**
     *
     */
    private function init_combos()
    {
        if(!$this->currencyList){
            $currencyDataAccess = new CurrencyDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
            $this->currencyList = $currencyDataAccess->getComboData('currencyId', 'code');
        }
        if(!$this->contactList){
            $contactDataAccess = new ContactDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
            $this->contactList = $contactDataAccess->getComboData('contactId', 'name');
        }
        if(!$this->contractList){
            $contractDataAccess = new ContractDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
            $this->contractList = $contractDataAccess->getComboData('contractId', 'code');
        }
        if(!$this->statusList){
            $constantDataAccess = new ConstantDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
            $this->statusList = $constantDataAccess->getComboByName('default_status');
        }
    }

    /**
     * @return ApiModel
     */
    public function apiPaymentAction()
    {
        $id= (int)$this->params()->fromRoute('id', 0);
        $contract = $this->contractTable()->getContract($id);
        return new ApiModel(array(
            'total'=>$contract->getAmount(),
            'paid'=>500,
            'id' => $id,
        ));
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','paymentDate');
        $sortBy = $this->params()->fromQuery('by','desc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

        $paginator = $this->paymentTable()->fetchAll(true,$filter,$sort,$sortBy);
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
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $this->init_combos();
        $id =(int)$this->params()->fromRoute('id',0);

        $helper = new PaymentHelper($this->getDbAdapter());
        $form = $helper->getForm($this->currencyList, $this->contactList,
            $this->contractList, $this->statusList);
        $payment = $this->paymentTable()->getPayment($id);

        $isEdit = true;
        if(!$payment){
            $isEdit = false;
            $payment = new Payment();
        }

        $form->bind($payment);
        $request=$this->getRequest();
        if($request->isPost()){
            $form->setInputFilter($helper->getInputFilter());
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            if($form->isValid()){
                $this->paymentTable()->savePayment($payment);
                $this->flashMessenger()->addInfoMessage('Payment successful');
                return $this->redirect()->toRoute('cr_payment');
            }
        }
        return new ViewModel(array(
            'form'=>$form,
            'staffName'=>$this->getCurrentStaff()->getStaffName(),
            'id' => $id,
            'isEdit' => $isEdit,
        ));
    }


    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->paymentTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment, filename="Payment-' . date('YmdHis') . '.xlsx"';
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type','application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition',$filename);
        $response->setContent($export->getExcel());
        return $response;
    }

}