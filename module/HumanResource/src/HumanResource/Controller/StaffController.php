<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/8/2015
 * Time: 1:50 PM
 */

namespace HumanResource\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\DepartmentDataAccess;
use HumanResource\DataAccess\PositionDataAccess;
use Application\DataAccess\UserDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Staff;
use HumanResource\Helper\StaffHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class StaffController extends AbstractActionController
{
    private  function  staffTable()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new StaffDataAccess($adapter);
    }

    private  function  userCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new UserDataAccess($adapter);
        return $dataAccess->getComboData('userId', 'userName');
    }

    private function statusCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByGroupCode('default_status');
    }
    private function positionCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend/Db/Adapter/Adapter');
        $dataAccess=new PositionDataAccess($adapter);
        return $dataAccess->getComboData('positionId','name');
    }

    private function departmentCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend/Db/Adapter/Adapter');
        $dataAccess=new DepartmentDataAccess($adapter);
        return $dataAccess->getComboData('departmentId','name');
    }

    private function currencyCombo(){
        $adapter = $this->getServiceLocator()->get('Zend/Db/Adapter/Adapter');
        $dataAccess = new CurrencyDataAccess($adapter);
        return $dataAccess->getComboData('currencyId', 'code');
    }

    public function indexAction()
    {
        $page=(int)$this->params()->fromQuery('page',1);
        $sort=$this->params()->fromQuery('sort', 'staffName');
        $sortBy=$this->params()->fromQuery('by', 'asc');
        $filter=$this->params()->fromQuery('filter','');

        $paginator=$this->staffTable()->fetchAll(true, $filter, $sort, $sortBy);
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

    public  function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $helper=new StaffHelper($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form = $helper->getForm($this->userCombos(), $this->positionCombos(),
            $this->departmentCombos(), $this->currencyCombo(), $this->statusCombo());
        $staff = $this->staffTable()->getStaff($id);
        $isEdit = true;

        if(!$staff)
        {
            $isEdit=false;
            $staff=new Staff();
        }

        $form->bind($staff);
        $request = $this->getRequest();

        if($request->isPost())
        {
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id));
            if($form->isValid()){
                $this->staffTable()->saveStaff($staff);
                $this->flashMessenger()->addSuccessMessage('Save Successful');
                return $this->redirect()->toRoute('hr_staff');
            }
        }
        return new ViewModel(array('form'=>$form, 'id'=>$id, 'isEdit'=>$isEdit));
    }

    public function  deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $staff = $this->staffTable()->getStaff($id);
        if ($staff) {
            $this->staffTable()->deleteStaff($id);
            $this->flashMessenger()->addInfoMessage('Delete Successful');
        }
        return $this->redirect()->toRoute("hr_staff");
    }

    public function exportAction()
    {
        $response=$this->getResponse();

        $excelObj=new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet=$excelObj->getActiveSheet();

        $data=$this->staffTable()->fetchAll(false);
        $columns=array();

        $excelColumn="A";
        $start=2;
        foreach($data as $row)
        {
            $data=$row->getArrayCopy();
            if(count($columns)==0) {
                $columns = array_keys($data);
            }
            foreach($columns as $col){
                $cellId=$excelColumn.$start;
                $sheet->setCellValue($cellId, $data[$col]);
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

        $filename='attachment; filename="Staff-'.date('Ymdhis').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }

    public function jsonDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $db=$this->staffTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();

        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->staffTable()->deleteStaff($id);
            }
            $conn->commit();
            $message='success';
            $this->flashMessenger()->addInfoMessage('Delete Successful!');

        }
        catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }
        return new JsonModel(array("message"=>$message));
    }
}
