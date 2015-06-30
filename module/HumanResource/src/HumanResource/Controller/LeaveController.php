<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 3:25 PM
 */

namespace HumanResource\Controller;

use Application\DataAccess\ConstantDataAccess;
use HumanResource\DataAccess\LeaveDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Leave;
use HumanResource\Helper\LeaveHelper;
use Zend\Form\View\Helper\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LeaveController extends AbstractActionController
{
    private function leaveTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new LeaveDataAccess($adapter);
    }

    private function staffTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new StaffDataAccess($adapter);
    }
    private $annualLeave;
    private $staffList;
    private $statusList;
    private $leaveTypeList;
    private function initCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $staffDA = new StaffDataAccess($adapter);
        $constantDA = new ConstantDataAccess($adapter);
        $this->staffList = $staffDA->getComboData('staffId', 'staffCode');
        $this->statusList = $constantDA->getComboByName('leave_status');
        unset($this->statusList['R']);

        $result = $constantDA->getConstantByName('leave_type');
        $leaveTypes = json_decode($result->getValue());

        if(!$this->leaveTypeList){
            $comboList = array();
            foreach($leaveTypes as $leave){
                $comboList[$leave->id] = $leave->title;
                $this->annualLeave[$leave->id] = $leave->value;
            }
            $this->leaveTypeList = $comboList;
        }
    }

    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'date');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $paginator = $this->leaveTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);

        return new ViewModel(array(
            'paginator' => $paginator,
            'page' => $page,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter' => $filter,
        ));
    }

    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $staff = (int)$this->params()->fromQuery('staff', 0);

        $helper = new LeaveHelper();
        $this->initCombo();
        $leave = $this->leaveTable()->getLeave($id);
        $isSave = false;
        if(!$leave){
            $leave = new Leave();
            $isSave = true;
            $form = $helper->getForm($this->staffList, $this->statusList, $this->leaveTypeList);
        }else{
            $formType = 'V';
            if($leave->getStatus() == 'R'){
                $isSave = true;
                $formType = 'R';
            }
            $form = $helper->getForm($this->staffList, $this->statusList, $this->leaveTypeList, $formType);
        }

        if($staff > 0){
            $leave->setStaffId($staff);
        }

        $form->bind($leave);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = array_merge($leave->getArrayCopy(), $request->getPost()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($form->getInputFilter());
            if($form->isValid()){
                $message = 'Save successful.';
                if( $leave->getStatus() == 'A' &&
                    in_array($leave->getLeaveType(), array_keys($this->annualLeave))){
                    $db = $this->leaveTable()->getAdapter();
                    $conn = $db->getDriver()->getConnection();
                    $conn->beginTransaction();
                    try{
                        $deduct = $this->annualLeave[$leave->getLeaveType()];
                        $this->staffTable()->updateLeave($deduct, $leave->getStaffId());
                        $this->leaveTable()->saveLeave($leave);
                        $conn->commit();
                    }catch(\Exception $ex){
                        $conn->rollback();
                        $message = $ex->getMessage();
                    }
                }else{
                    $this->leaveTable()->saveLeave($leave);
                }
                $this->flashMessenger()->addSuccessMessage($message);
                return $this->redirect()->toRoute('hr_leave');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'isSave' => $isSave,
            'backLink' => $this->getRequest()->getHeader('Referer')->getUri(),
        ));
    }

    public function exportAction()
    {
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        $positions = $this->leaveTable()->fetchAll(false);
        $columns = array();

        $excelColumn = "A";
        $start = 2;
        foreach($positions as $row)
        {
            $data = $row->getArrayCopy();
            if(count($columns) == 0){
                $columns = array_keys($data);
            }
            foreach($columns as $col){
                $cellId = $excelColumn . $start;
                $sheet->setCellValue($cellId, $data[$col]);
                $excelColumn++;
            }
            $start++;
            $excelColumn = "A";
        }
        foreach($columns as $col)
        {
            $cellId = $excelColumn . '1';
            $sheet->setCellValue($cellId, $col);
            $excelColumn++;
        }

        $excelWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
        ob_start();
        $excelWriter->save('php://output');
        $excelOutput = ob_get_clean();

        $filename = 'attachment; filename="Leave-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }
}