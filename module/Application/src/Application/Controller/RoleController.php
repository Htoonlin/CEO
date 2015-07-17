<?php

namespace Application\Controller;


use Application\Helper\RoleHelper;
use Application\Entity\Role;
use Application\DataAccess\RoleDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class RoleController extends AbstractActionController
{
    private function roleTable()
    {
        $sm = $this->getServiceLocator();
        $adapter = $sm->get('Sundew\Db\Adapter');
        $dataAccess = new RoleDataAccess($adapter);
        return $dataAccess;
    }

    public function jsonAllAction()
    {
        return new JsonModel($this->roleTable()->fetchAll());
    }

    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $parentRole = new Role();
        $edit = false;
        if($id > 0){
            $role = $this->roleTable()->getRole($id);
            if($role->getParentId()){
                $parentRole = $this->roleTable()->getRole($role->getParentId());
            }
            $edit = true;
        }else{
            $role = new Role();
        }

        $roles = $this->roleTable()->getChildren();
        $helper = new RoleHelper();
        $form = $helper->getForm();
        $form->bind($role);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $isDelete = $request->getPost('is_delete', 'no');
            if($isDelete == 'yes' && $id > 0){
                $this->roleTable()->deleteRole($id);
                $this->flashMessenger()->addInfoMessage('Delete successful!');
                return $this->redirect()->toRoute("role");
            }else{
                $form->setData($request->getPost());
                if($form->isValid()){
                    $this->roleTable()->saveRole($role);
                    $this->flashMessenger()->addSuccessMessage('Save successful!');
                    return $this->redirect()->toRoute("role");
                }
            }
        }

        return new ViewModel(array(
            'id' => $id,
            'roles' => $roles,
            'form' => $form,
            'isEdit' => $edit,
            'parent' => $parentRole,
        ));
    }
}

