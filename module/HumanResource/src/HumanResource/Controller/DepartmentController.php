<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/7/2015
 * Time: 11:08 AM
 */

namespace HumanResource\Controller;

use Application\Service\SundewController;
use HumanResource\Helper\DepartmentHelper;
use HumanResource\Entity\Department;
use HumanResource\DataAccess\DepartmentDataAccess;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class DepartmentController
 * @package HumanResource\Controller
 */
class DepartmentController extends SundewController
{
    /**
     * @return DepartmentDataAccess
     */
    private function departmentTable()
    {
        $dataAccess=new DepartmentDataAccess($this->getDbAdapter());
        return $dataAccess;
    }

    /**
     * @return JsonModel
     */
    public function jsonAllAction()
    {
        $departments = $this->departmentTable()->fetchAll();
        $data = array();
        foreach($departments as $department){
            $data[] = array('departmentId' => $department->getDepartmentId(), 'name' => $department->getName());
        }
        return new JsonModel($data);
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function indexAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action', '');

        if($action == "delete" && $id > 0){
            $this->departmentTable()->deleteDepartment($id);
            $this->flashMessenger()->addInfoMessage('Delete successful!');
            return $this->redirect()->toRoute("hr_department");
        }

        $parentDepartment=new Department();
        $edit=false;
        if($id>0){
            $department=$this->departmentTable()->getDepartment($id);
            if(!empty($department->getParentId())){
                $parentDepartment=$this->departmentTable()->getDepartment($department->getParentId());
            }
            $edit=true;
        }else{
            $department=new Department();
        }
        $departments=$this->departmentTable()->getChildren();
        $helper=new DepartmentHelper($this->getDbAdapter());
        $form=$helper->getForm();

        if($action == 'clone'){
            $edit = false;
            $id = 0;
            $department->setDepartmentId(0);
        }

        $form->bind($department);
        $request=$this->getRequest();
        if($request->isPost())
        {
            $form->setData($request->getPost());
            $form->setInputFilter($helper->getInputFilter());
            if($form->isValid()){
                $this->departmentTable()->saveDepartment($department);
                $this->flashMessenger()->addSuccessMessage('Save successful!');
                return $this->redirect()->toRoute("hr_department");
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