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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountTypeController extends AbstractActionController
{

    private function accountTypeTable()
    {
        $sm = $this->getServiceLocator();
        $adapter = $sm->get('Zend\Db\Adapter\Adapter');

        $dataAccess=new AccountTypeDataAccess($adapter);
        return $dataAccess;
    }

    private function baseTypeCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByName('account_base_type');
    }

    private function defaultStatusCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByName('default_status');
    }

    public function jsonAllAction()
    {
        $accountTypes = $this->accountTypeTable()->fetchAll();
        $data = array();

        foreach($accountTypes as $accountType){

            $data[] = array('accountTypeId' => $accountType->getAccountTypeId(), 'name' => $accountType->getName());
        }
        return new JsonModel($data);
    }

    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
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
        $form->bind($accountType);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $isDelete = $request->getPost('is_delete', 'no');
            if($isDelete == 'yes' && $id > 0){
                $this->accountTypeTable()->deleteAccountType($id);
                $this->flashMessenger()->addInfoMessage('Delete successful!');
                return $this->redirect()->toRoute("account_type");
            }else{
                $form->setData($request->getPost());
                if($form->isValid()){
                    $this->accountTypeTable()->saveAccountType($accountType);
                    $this->flashMessenger()->addSuccessMessage('Save successful!');
                    return $this->redirect()->toRoute("account_type");
                }
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