<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/7/2015
 * Time: 11:08 AM
 */

namespace HumanResource\Controller;

use HumanResource\Helper\DepartmentHelper;
use HumanResource\Entity\Department;
use HumanResource\DataAccess\DepartmentDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DepartmentController extends AbstractActionController
{
    private function departmentTable()
    {
        $sm=$this->getServiceLocator();
        $adapter=$sm->get('Sundew\Db\Adapter');
        $dataAccess=new DepartmentDataAccess($adapter);
        return $dataAccess;
    }
    public function jsonAllAction()
    {
        $departments = $this->departmentTable()->fetchAll();
        $data = array();
        foreach($departments as $department){
            $data[] = array('departmentId' => $department->getDepartmentId(), 'name' => $department->getName());
        }
        return new JsonModel($data);
    }
    public function indexAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $parentDepartment=new Department();
        $edit=false;
        if($id>0){
            $department=$this->departmentTable()->getDepartment($id);
            if($department->getParentId()){
                $parentDepartment=$this->departmentTable()->getDepartment($department->getParentId());
            }
            $edit=true;
        }else{
            $department=new Department();
        }
        $departments=$this->departmentTable()->getChildren();
        $helper=new DepartmentHelper();
        $form=$helper->getForm();
        $form->bind($department);
        $request=$this->getRequest();
        if($request->isPost())
        {
            $isDelete=$request->getPost('is_delete','no');
            if($isDelete=='yes' && $id>0){
                $this->departmentTable()->deleteDepartment($id);
                $this->flashMessenger()->addInfoMessage('Delete successful!');
                return $this->redirect()->toRoute("hr_department");
            }else{
                $form->setData($request->getPost());
                if($form->isValid()){
                    $this->departmentTable()->saveDepartment($department);
                    $this->flashMessenger()->addSuccessMessage('Save successful!');
                    return $this->redirect()->toRoute("hr_department");
                }
            }
        }
        return new ViewModel(array(
            'id' => $id,
            'departments'=>$departments,
            'form'=>$form,
            'isEdit'=>$edit,
            'parent'=>$parentDepartment,
        ));
    }

}