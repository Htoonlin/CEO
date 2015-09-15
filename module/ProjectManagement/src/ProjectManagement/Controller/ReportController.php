<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-17
 * Time: 01:16 PM
 */

namespace ProjectManagement\Controller;


use Application\DataAccess\ConstantDataAccess;
use Core\Helper\ChartHelper;
use Core\Helper\View\ConstantConverter;
use Core\Helper\ChartHelper as Chart;
use Core\Model\ApiModel;
use Core\SundewController;
use ProjectManagement\DataAccess\ReportDataAccess;

/**
 * Class ReportController
 * @package ProjectManagement\Controller
 */
class ReportController extends SundewController
{
    /**
     * @var array
     */
    protected $colorTemplate = array(
        'T' => 'black',
        'A' => 'yellow',
        'P' => 'blue',
        'F' => 'orange',
        'C' => 'cyan',
        'L' => 'red'
    );

    private function getColor($status){
        return ChartHelper::getColor($this->colorTemplate[$status]);
    }

    /**
     * @param $status
     * @return mixed
     */
    private function statusConverter($status){
        $constantDA = new ConstantDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
        $converter = new ConstantConverter($constantDA);
        return $converter($status, 'task_status');
    }

    /**
     * @param $status
     * @param $data
     * @return array
     */
    private function getWorkloadData($status, $data){
        $colors = $this->getColor($status);
        $label = ($status == 'T') ? 'Total' : $this->statusConverter($status);
        return array(
            'label' => $label,
            'fillColor' => $colors['color'],
            'highlightFill' => $colors['highlight'],
            'data' => $data,
        );
    }

    /**
     * @param $status
     * @param $value
     * @return mixed
     */
    private function getProgressData($status, $value)
    {
        $result = $this->getColor($status);
        $result['value'] = $value;
        if($status == 'T'){
            $result['label'] = 'Total';
        }else{
            $result['label'] =  $this->statusConverter($status);
        }
        return $result;
    }

    /**
     * @return ReportDataAccess
     */
    private function reportTable()
    {
        return new ReportDataAccess($this->getDbAdapter(), $this->getAuthUser()->userId);
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->redirect()->toRoute('pm_project', array('action' => 'report', 'id' => -1));
    }

    /**
     * @return ApiModel
     */
    public function progressAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        $data = array();
        $results = $this->reportTable()->getProgress($id);

        foreach($results as $record){
            $data[] = $this->getProgressData($record->status, $record->count);
        }

        return new ApiModel(array(
            'id' => $id,
            'title' => 'Progress report',
            'icon' => 'fa fa-pie-chart',
            'type' => 'Pie',
            'data' => $data,
            'options' => Chart::pieChartOption(),
        ));
    }

    /**
     * @return ApiModel
     */
    public function overdueAction(){
        $id = (int)$this->params()->fromRoute('id', -1);
        $times = $this->reportTable()->getTime($id);

        $overdue = 0;
        $now = 0;
        $week = 0;
        $upcoming = 0;

        foreach($times as $time)
        {
            $dateDiff = strtotime($time->toTime) - strtotime(date('Y-m-d', time()));
            $days = floor($dateDiff / (60*60*24));
            if($days < 0){
                $overdue++;
            }else if($days <= 7 && $days > 1){
                $week++;
            }else if($days > 7){
                $upcoming++;
            }else{
                $now++;
            }
        }

        $data = array();
        if($overdue > 0){
            $json = $this->getProgressData('C', $overdue);
            $json['label'] = 'Overdue';
            $data[] = $json;
        }

        if($now > 0){
            $json = $this->getProgressData('F', $now);
            $json['label'] = 'Today';
            $data[] = $json;
        }

        if($week > 0){
            $json = $this->getProgressData('T', $week);
            $json['label'] = 'This week';
            $data[] = $json;
        }

        if($upcoming > 0){
            $json = $this->getProgressData('P', $upcoming);
            $json['label'] = 'Upcoming';
            $data[] = $json;
        }

        return new ApiModel(array(
            'id' => $id,
            'title' => 'Overdue report',
            'icon' => 'fa fa-calendar',
            'type' => 'Pie',
            'data' => $data,
            'options' => Chart::pieChartOption()
        ));
    }

    /**
     * @return ApiModel
     */
    public function workloadAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        $staffList = $this->reportTable()->getActiveStaffs($id);
        $labels = array();
        $totalList = array();
        $assignedList = array();
        $processingList = array();
        $finishedList = array();
        $completedList = array();
        $failedList = array();
        foreach($staffList as $staff){
            $labels[] = $staff->staffName . "({$staff->staffCode})";
            $assign = 0;
            $process = 0;
            $finish = 0;
            $complete = 0;
            $failed = 0;
            $workLoads = $this->reportTable()->getWorkload($staff->staffId, $id);

            foreach($workLoads as $record){
                switch(strtoupper($record->status)){
                    case 'A':
                        $assign += $record->workload;
                        break;
                    case 'P':
                        $process += $record->workload;
                        break;
                    case 'F':
                        $finish += $record->workload;
                        break;
                    case 'C':
                        $complete += $record->workload;
                        break;
                    case 'L':
                        $failed += $record->workload;
                        break;
                }
            }

            $assignedList[] = $assign;
            $processingList[] = $process;
            $finishedList[] = $finish;
            $completedList[] = $complete;
            $failedList[] = $failed;
            $totalList[] = $assign + $process + $finish + $complete + $failed;
        }

        $data = array(
            'labels' => $labels,
            'datasets' => array(
                $this->getWorkloadData('T', $totalList),
                $this->getWorkloadData('A', $assignedList),
                $this->getWorkloadData('F', $finishedList),
                $this->getWorkloadData('P', $processingList),
                $this->getWorkloadData('C', $completedList),
                $this->getWorkloadData('L', $failedList),
            ),
        );

        return new ApiModel(array(
            'id' => $id,
            'title' => 'Workload report',
            'icon' => 'fa fa-balance-scale',
            'type' => 'Bar',
            'data' => $data,
            'options' => Chart::barChartOption()
        ));
    }
}