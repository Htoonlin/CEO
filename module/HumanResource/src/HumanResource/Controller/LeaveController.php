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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class LeaveController extends AbstractActionController
{
    private function leaveTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new LeaveDataAccess($adapter);
    }

    private $staffList;
    private $statusList;
    private $leaveTypeList;
    private function initCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $staffDA = new StaffDataAccess($adapter);
        $constantDA = new ConstantDataAccess($adapter);
        $this->staffList = $staffDA->getComboData('staffId', 'staffCode');
        $this->statusList = $constantDA->getComboByGroupCode('leave_status');
        $this->leaveTypeList = $constantDA->getComboByGroupCode('leave_type');
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
        $helper = new LeaveHelper();
        $this->initCombo();
        $form = $helper->getForm($this->staffList, $this->statusList, $this->leaveTypeList);
        $leave = $this->leaveTable()->getLeave($id);
        $isEdit = true;
        if(!$leave){
            $isEdit = false;
            $leave = new Leave();
        }

        $form->bind($leave);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = $request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($form->getInputFilter());
            if($form->isValid()){
                $this->leaveTable()->saveLeave($leave);
                $this->flashMessenger()->addSuccessMessage('Save successful.');
                return $this->redirect()->toRoute('hr_leave');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'isEdit' => $isEdit,
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

        $filename = 'attachment; filename="Leave-' . date('Ymdhms') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }
}