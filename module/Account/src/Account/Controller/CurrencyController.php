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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CurrencyController extends AbstractActionController
{
    private function currencyTable()
    {
        $sm=$this->getServiceLocator();
        $adapter=$sm->get('Zend\Db\Adapter\Adapter');

        $dataAccess=new CurrencyDataAccess($adapter);

        return $dataAccess;
    }

    private function statusCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByGroupCode('default_status');
    }

    public  function jsonAllAction()
    {
        $currencies=$this->currencyTable()->fetchAll();
        $data=array();

        foreach($currencies as $currency)
        {
            $data[]=array('currencyId'=>$currency->getCurrencyId(), 'name'=>$currency->getName());
        }
        return new JsonModel($data);
    }

    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort','name');
        $sortBy=$this->params()->fromQuery('by','asc');
        $filter=$this->params()->fromQuery('filter','');

        $paginator=$this->currencyTable()->fetchAll(true, $filter, $sort, $sortBy);

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

    public function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $helper=new CurrencyHelper($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
        $response=$this->getResponse();

        $excelObj=new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet=$excelObj->getActiveSheet();

        $currencies=$this->currencyTable()->fetchAll(false);
        $columns=array();

        $excelColumn="A";
        $start=2;
        foreach($currencies as $row) {
            $data = $row->getArrayCopy();
            if (count($columns) == 0) {
                $columns = array_keys($data);
            }
            foreach ($columns as $col) {
                $cellId = $excelColumn . '1';
                $sheet->setCellValue($cellId, $col);
                $excelColumn++;
            }

            $start++;
            $excelColumn="A";
        }

        foreach($columns as $col)
        {
            $cellId=$excelColumn.'1';
            $sheet->setCellValue($cellId, $col);
            $excelColumn++;
        }

            $excelWriter=\PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
            ob_start();
            $excelWriter->save('php://output');
            $excelOutput=ob_get_clean();

            $filename='attachment; filename="Currency-'.date('Ymdhms').'.xlsx"';

            $headers=$response->getHeaders();
            $headers->addHeaderLine('Content-Type','application/msexcel; charset=UTF-8');
            $headers->addHeaderLine('Content-Disposition', $filename);
            $response->setContent($excelOutput);

            return $response;
        }

    public function jsonDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $message="success";
        $db=$this->currencyTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();

        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->currencyTable()->deleteCurrency($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }

        return new JsonModel(array("message"=>$message));
    }


}
