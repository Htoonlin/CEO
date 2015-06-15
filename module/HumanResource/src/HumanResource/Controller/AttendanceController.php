<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/8/2015
 * Time: 11:59 AM
 */

namespace HumanResource\Controller;

use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
use HumanResource\Helper\AttendanceBoardHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\ViewModel;

class AttendanceController extends AbstractActionController
{
    private function attendanceTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new AttendanceDataAccess($adapter);
    }
    private function staffCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new StaffDataAccess($adapter);
        return $dataAccess->getComboData('staffId', 'staffCode');
    }

    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'Date');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $paginator = $this->attendanceTable()->fetchAll(true, $filter, $sort, $sortBy);
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
        $helper = new AttendanceBoardHelper();
        $form = $helper->getForm($this->staffCombo());
        $attendance = $this->attendanceTable()->getAttendanceBoard($id);

        if(!$attendance){
            $attendance = new ArrayObject(array(
                'InTime' => '-', 'IsLeave' => 0, 'AttendanceId' => $id
            ));
        }

        $attendance['hour'] = (int) date('H', time());
        $attendance['minute'] = round(((int) date('i', time())) / 5);

        if($attendance['hour'] > 12 && strlen($attendance['InTime']) > 1){
            $attendance['type'] = 'O';
        }else if($attendance['IsLeave'] == 1){
            $attendance['type'] = 'L';
        }else{
            $attendance['type'] = 'I';
        }
        $form->bind($attendance);

        $request = $this->getRequest();
        if($request->isPost()){
            $post_data = $request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter());
            if($form->isValid())
            {
                $time = sprintf('%02d:%02d:00', $attendance['hour'], ($attendance['minute'] * 5));
                $datetime = $attendance['Date'] . ' ' . $time;
                $att = new Attendance();

                $att->exchangeArray(array(
                    'attendanceId' => $id,
                    'staffId' => $attendance['StaffId'],
                    'attendance' => $datetime,
                    'status' => $attendance['type'],
                ));

                $data = $this->attendanceTable()->checkAttendance($att, $attendance['Date']);
                $id = (empty($data)) ? 0 : $data->getAttendanceId();
                $att->setAttendanceId($id);

                $this->attendanceTable()->saveAttendance($att);

                $this->flashMessenger()->addSuccessMessage('Save successful');
                return $this->redirect()->toRoute('hr_attendance');
            }
        }

        return new ViewModel(array('form' => $form, 'id'=>$id));
    }

    public function exportAction()
    {
        $response = $this->getResponse();

        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);

        $sheet = $excelObj->getActiveSheet();

        $positions = $this->attendanceTable()->fetchAll(false);
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

        $filename = 'attachment; filename="Attendance-' . date('Ymdhms') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excelOutput);

        return $response;
    }
}