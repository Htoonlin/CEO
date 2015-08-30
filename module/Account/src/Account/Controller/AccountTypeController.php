<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/13/2015
 * Time: 3:52 PM
 */

namespace Account\Controller;

use Account\Helper\AccountTypeHelper;
use Account\Entity\AccountType;
use Account\DataAccess\AccountTypeDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Core\SundewController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountTypeController extends SundewController
{
    /**
     * @return AccountTypeDataAccess
     */
    private function accountTypeTable()
    {
        return new AccountTypeDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function baseTypeCombo()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('account_base_type');
    }

    /**
     * @return array
     */
    private function defaultStatusCombo()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('default_status');
    }

    /**
     * @return JsonModel
     */
    public function jsonAllAction()
    {
        $accountTypes = $this->accountTypeTable()->fetchAll();
        $data = array();

        foreach($accountTypes as $accountType){

            $data[] = array('accountTypeId' => $accountType->getAccountTypeId(), 'name' => $accountType->getName());
        }
        return new JsonModel($data);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $action = $this->params()->fromQuery('action', '');

        if($action == 'delete' && $id > 0){
            $this->accountTypeTable()->deleteAccountType($id);
            $this->flashMessenger()->addInfoMessage('Delete successful!');
            return $this->redirect()->toRoute("account_type");
        }

        $parentAccountType = new AccountType();
        $edit = false;
        if($id > 0){
            $accountType=$this->accountTypeTable()->getAccountType($id);
            if($accountType->getParentTypeId()){
                $parentAccountType = $this->accountTypeTable()->getAccountType($accountType->getParentTypeId());
            }
            $edit = true;
        }else{
            $accountType = new AccountType();
        }

        $accountTypes = $this->accountTypeTable()->getChildren();
        $helper = new AccountTypeHelper();
        $form = $helper->getForm($this->baseTypeCombo(), $this->defaultStatusCombo());
        if($action == 'clone'){
            $edit = false;
            $id = 0;
            $accountType->setAccountTypeId(0);
        }
        $form->bind($accountType);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $form->setData($request->getPost());
            if($form->isValid()){
                $this->accountTypeTable()->saveAccountType($accountType);
                $this->flashMessenger()->addSuccessMessage('Save successful!');
                return $this->redirect()->toRoute("account_type");
            }
        }

        return new ViewModel(array(
            'id' => $id,
            'accountType' => $accountTypes,
            'accountTypes' => $accountTypes,
            'form' => $form,
            'isEdit' => $edit,
            'parent' => $parentAccountType,
        ));
    }
}