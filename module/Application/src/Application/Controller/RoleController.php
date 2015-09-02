<?php

namespace Application\Controller;


use Application\Helper\RoleHelper;
use Application\Entity\Role;
use Application\DataAccess\RoleDataAccess;
use Core\Model\ApiModel;
use Core\SundewController;
use Zend\View\Model\ViewModel;

/**
 * Class RoleController
 * @package Application\Controller
 */
class RoleController extends SundewController
{
    /**
     * @return RoleDataAccess
     */
    private function roleTable()
    {
        return new RoleDataAccess($this->getDbAdapter());
    }

    /**
     * @return ApiModel
     */
    public function apiAllAction()
    {
        return new ApiModel($this->roleTable()->fetchAll());
    }

    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $action = $this->params()->fromQuery('action', '');

        if($action == 'delete' && $id > 0){
            $this->roleTable()->deleteRole($id);
            $this->flashMessenger()->addInfoMessage('Delete successful!');
            return $this->redirect()->toRoute("role");
        }

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

        if($action == 'clone'){
            $edit = false;
            $id = 0;
            $role->setRoleId(0);
        }

        $form->bind($role);
        $request = $this->getRequest();
        if($request->isPost())
        {
            $form->setData($request->getPost());
            if($form->isValid()){
                $this->roleTable()->saveRole($role);
                $this->flashMessenger()->addSuccessMessage('Save successful!');
                return $this->redirect()->toRoute("role");
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

