<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/23/2015
 * Time: 4:22 PM
 */

namespace Account\Controller;

use Account\Helper\CurrencyHelper;
use Account\Entity\Currency;
use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use Zend\View\Model\ViewModel;

class CurrencyController extends SundewController
{
    /**
     * @return CurrencyDataAccess
     */
    private function currencyTable()
    {
        return new CurrencyDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
    }

    /**
     * @return array
     */
    private function statusCombo()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        return $dataAccess->getComboByName('default_status');
    }

    /**
     * @return ApiModel
     */
    public  function apiAllAction()
    {
        $currencies=$this->currencyTable()->fetchAll();
        $data=array();

        foreach($currencies as $currency)
        {
            $data[]=array('currencyId'=>$currency->getCurrencyId(), 'name'=>$currency->getName());
        }
        return new ApiModel($data);
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','name');
        $sortBy = $this->params()->fromQuery('by','asc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

        $paginator=$this->currencyTable()->fetchAll(true, $filter, $sort, $sortBy);

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
        $id=(int)$this->params()->fromRoute('id',0);
        $helper=new CurrencyHelper($this->getDbAdapter());
        $form=$helper->getForm($this->statusCombo());
        $currency=$this->currencyTable()->getCurrency($id);

        $isEdit=true;
        if(!$currency){
            $isEdit=false;
            $currency=new Currency();
        }

        $form->bind($currency);
        $request=$this->getRequest();

        if($request->isPost()){
            $form->setInputFilter($helper->getInputFilter($id));
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            $newId= (int)($isEdit?$post_data['currencyId']:0);
            $form->setInputFilter($helper->getInputFilter($newId, $post_data['name']));
            if($form->isValid()){
                if(boolval($post_data['changedRate']) && $isEdit){
                    $db=$this->currencyTable()->getAdapter();
                    $conn=$db->getDriver()->getConnection();
                    try{
                        $conn->beginTransaction();
                        $oldCurrency = $this->currencyTable()->getCurrency($id);
                        $oldCurrency->setStatus('D');
                        $this->currencyTable()->saveCurrency($oldCurrency);

                        $currency->setCurrencyId(0);
                        $this->currencyTable()->saveCurrency($currency);
                        $conn->commit();
                    }catch(\Exception $ex){
                        $conn->rollback();
                        $this->flashMessenger()->addErrorMessage($ex->getMessage());
                        return $this->redirect()->toRoute('account_currency');
                    }
                }else{
                    $this->currencyTable()->saveCurrency($currency);
                }
                $this->flashMessenger()->addSuccessMessage('Save Successful');
                return $this->redirect()->toRoute('account_currency');
            }
        }

        return new ViewModel(array('form'=>$form,
            'id'=>$id, 'isEdit'=>$isEdit));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $currency=$this->currencyTable()->getCurrency($id);
        if($currency){
            $this->currencyTable()->deleteCurrency($id);
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }
        return $this->redirect()->toRoute("account_currency");
    }
    public function exportAction()
    {
        $export = new SundewExporting($this->currencyTable()->fetchAll());
        $filename = 'attachment; filename="Currency-' . date('YmdHis') . '.xlsx"';
        $excel = $export->getExcel();

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excel);

        return $response;
    }

    /**
     * @return ApiModel
     */
    public function apiDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $db=$this->currencyTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        $api = new ApiModel();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->currencyTable()->deleteCurrency($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }

        return $api;
    }
}
