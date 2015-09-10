<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/8/2015
 * Time: 11:59 AM
 */

namespace HumanResource\Controller;

use Core\Helper\ChartHelper;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Attendance;
use HumanResource\Helper\AttendanceBoardHelper;
use Zend\Db\Sql\Where;
use Zend\Stdlib\ArrayObject;
use Zend\View\Model\ViewModel;

/**
 * Class AttendanceController
 * @package HumanResource\Controller
 */
class AttendanceController extends SundewController
{
    /**
     * @return AttendanceDataAccess
     */
    private function attendanceTable()
    {
        return new AttendanceDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function staffCombo()
    {
        $dataAccess = new StaffDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('staffId', 'staffCode');
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'attendanceDate');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

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

    /**
     * @return \Zend\Http\Response|ViewModel
     */
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
                $newData = $this->attendanceTable()->getAttendanceByStaff(
                    $attendance['staffId'], $attendance['attendanceDate']);

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

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->attendanceTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Attendance-' . date('YmdHis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return JsonModel
     */
    public function apiAttendanceAction()
    {
        $staffId = $this->params()->fromQuery('staffId', 0);
        $date = $this->params()->fromQuery('date', date('Y-m-d', time()));
        $attendance = $this->attendanceTable()->checkAttendance($staffId, $date);
        $api = new ApiModel();
        if($attendance){
            $api->setResponseData($attendance);
        }else{
            $api->setStatusCode(406);
        }
        return $api;
    }

    private function getWorkHoursData($label, $data)
    {
        $colors = ChartHelper::getRandomColor();
        return array(
            'label' => $label,
            'fillColor' => 'rgba(220,220,220,0.2)',
            'strokeColor' => $colors['color'],
            'pointColor' => $colors['color'],
            'pointStrokeColor' => "rgba(220,220,220,1)",
            'pointHighlightFill' => $colors['highlight'],
            'pointHighlightStroke' => $colors['highlight'],
            'data' => $data,
        );
    }
    public function apiWorkHoursAction(){
        $from = date('Y-m-01', strtotime("-1 year"));
        $to = date('Y-m-01');
        $results = $this->attendanceTable()->getWorkHours($from, $to);
        $months = array();
        $month = strtotime($from);
        while($month < strtotime($to)){
            $months[date('Y-m', $month)] = 0;
            $month = strtotime('+1 month', $month);
        }
        $data = $months;
        $dataSet = array();
        $currentStaffId = 0;
        $lastLabel = '';
        foreach($results as $result){
            if($currentStaffId != $result->staffId && $currentStaffId > 0){
                $dataSet[] = $this->getWorkHoursData($lastLabel, array_values($data));
                $data = $months;
            }
            $key = sprintf("%04d-%02d",$result->year, $result->month);
            if($result->hours > 0){
                $data[$key] = round($result->hours);
            }
            $currentStaffId = $result->staffId;
            $lastLabel = $result->staffName . ' (' . $result->staffCode . ')';
        }

        if($currentStaffId > 0){
            $dataSet[] = $this->getWorkHoursData($lastLabel, array_values($data));
        }

        return new ApiModel(array(
            'options' => ChartHelper::lineChartOption(),
            'source' => array(
                'labels' => array_keys($months),
                'datasets' => $dataSet,
            )
        ));
    }
}