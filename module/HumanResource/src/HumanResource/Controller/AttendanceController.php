<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/8/2015
 * Time: 11:59 AM
 */

namespace HumanResource\Controller;

use Application\Service\SundewExporting;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
use HumanResource\Helper\AttendanceBoardHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\JsonModel;
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
        $sort = $this->params()->fromQuery('sort', 'attendanceDate');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator = $this->attendanceTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator' => $paginator,
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

        $attendance = $this->attendanceTable()->getAttendance($id);

        if(!$attendance){
            $attendance = new ArrayObject(array('attendanceId' => $id));
        }else{
            $attendance = new ArrayObject($attendance->getArrayCopy());
        }

        $attendance['hour'] = (int) date('H', time());
        $attendance['minute'] = round(((int) date('i', time())) / 5);

        if($attendance['hour'] > 12 && strlen($attendance['inTime']) > 1){
            $attendance['type'] = 'O';
        }else{
            $attendance['type'] = 'I';
        }

        $form->bind($attendance);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = $request->getPost();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter());
            if($form->isValid())
            {
                $newData = $this->attendanceTable()->getAttendance($id);

                if(!$newData){
                    $newData = new Attendance();
                    $newData->exchangeArray($attendance->getArrayCopy());
                }

                $time = sprintf('%02d:%02d:00', $attendance['hour'], ($attendance['minute'] * 5));

                if($attendance['type'] == 'I'){
                    $newData->setInTime($time);
                }else{
                    $newData->setOutTime($time);
                }

                $this->attendanceTable()->saveAttendance($newData);
                $this->flashMessenger()->addSuccessMessage('Save successful');
                return $this->redirect()->toRoute('hr_attendance');
            }
        }

        return new ViewModel(array('form' => $form, 'id'=>$id));
    }

    public function exportAction()
    {
        $export = new SundewExporting($this->attendanceTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Attendance-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    public function jsonAttendanceAction()
    {
        $staffId = $this->params()->fromQuery('staffId', 0);
        $date = $this->params()->fromQuery('date', date('Y-m-D', time()));
        $attendance = $this->attendanceTable()->checkAttendance($staffId, $date);
        if($attendance){
            return new JsonModel(array(
                'status' => true,
                'result' => $attendance->getArrayCopy()
            ));
        }
        return new JsonModel(array(
            'status' => false,
            'result' => array('staffId' => $staffId, 'date' => $date)
        ));
    }
}