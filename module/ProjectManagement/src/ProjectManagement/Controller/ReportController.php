<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-17
 * Time: 01:16 PM
 */

namespace ProjectManagement\Controller;


use Application\DataAccess\ConstantDataAccess;
use Application\Helper\View\ConstantConverter;
use Application\Service\SundewController;
use PhpOffice\PhpWord\Exception\Exception;
use ProjectManagement\DataAccess\ProjectDataAccess;
use ProjectManagement\DataAccess\ReportDataAccess;
use Zend\View\Model\JsonModel;

class ReportController extends SundewController
{
    protected $dataTemplate = array(
        'T' => array(
            'color' => '#4076A0',
            'highlight' => '#5D96C1',
        ),
        'A' => array(
            'color' => '#4D5360',
            'highlight' => '#616774',
        ),
        'P' => array(
            'color' => '#46BFBD',
            'highlight' => '#5AD3D1',
        ),
        'F' => array(
            'color' => '#FDB45C',
            'highlight' => '#FFC870',
        ),
        'C' => array(
            'color' => '#F7464A',
            'highlight' => '#FF5A5E',
        ),
    );

    private function pieChartOption()
    {
        return array(
            'segmentShowStroke' => true,
            'segmentStrokeColor' => "#fff",
            'segmentStrokeWidth' => 2,
            'percentageInnerCutout' => 50,
            'animationSteps' => 100,
            'animationEasing' => "easeOutSine",
            'animateRotate' => true,
            'animateScale' => false,
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
        );
    }

    private function lineChartOption()
    {
        return array(
            'scaleShowGridLines' => true,
            'scaleGridLineColor' => "rgba(0,0,0,.05)",
            'scaleGridLineWidth' => 1,
            'scaleShowHorizontalLines' => true,
            'scaleShowVerticalLines' => true,
            'bezierCurve' => false,
            'bezierCurveTension' => 0.4,
            'pointDot' => true,
            'pointDotRadius' => 4,
            'pointDotStrokeWidth' => 1,
            'pointHitDetectionRadius' => 20,
            'datasetStroke' => true,
            'datasetStrokeWidth' => 2,
            'datasetFill' => true,
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
        );
    }

    private function barChartOption()
    {
        return array(
            'scaleBeginAtZero' => true,
            'scaleShowGridLines' => true,
            'scaleGridLineColor' => "rgba(0,0,0,.05)",
            'scaleGridLineWidth' => 1,
            'scaleShowHorizontalLines' => true,
            'scaleShowVerticalLines' => true,
            'barShowStroke' => false,
            'barStrokeWidth' => 0,
            'barValueSpacing' => 10,
            'barDatasetSpacing' => 2,
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
        );
    }

    private function statusConverter($status){
        $constantDA = new ConstantDataAccess($this->getDbAdapter());
        $converter = new ConstantConverter($constantDA);
        return $converter($status, 'task_status');
    }

    private function getWorkloadData($status, $data){
        $colors = $this->dataTemplate[$status];
        $label = ($status == 'T') ? 'Total' : $this->statusConverter($status);
        return array(
            'label' => $label,
            'fillColor' => $colors['color'],
            'highlightFill' => $colors['highlight'],
            'data' => $data,
        );
    }

    private function getProgressData($status, $value)
    {
        $result = $this->dataTemplate[$status];
        $result['value'] = $value;
        if($status == 'T'){
            $result['label'] = 'Total';
        }else{
            $result['label'] =  $this->statusConverter($status);
        }
        return $result;
    }

    private function projectTable()
    {
        return new ProjectDataAccess($this->getDbAdapter());
    }
    private function reportTable()
    {
        return new ReportDataAccess($this->getDbAdapter());
    }
    private function reportTitle($prefix)
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        try{
            if($id > 0){
                $project = $this->projectTable()->getProject($id);
                if(!$project){
                    return 'Invalid project';
                }
                $title = 'report for ';
                $title .= $project->getName() . "({$project->getCode()})";
            }else if($id == 0){
                $title = 'report for (No Project)';
            }else{
                $title = 'report for (all project)';
            }
        }catch(Exception $ex){
            return 'Error => ' . $ex->getMessage();
        }

        return $prefix . ' ' . $title;
    }

    public function indexAction()
    {
        $this->redirect()->toRoute('pm_project', array('action' => 'report', 'id' => -1));
    }

    public function progressAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        $data = array();
        $results = $this->reportTable()->getProgress($id);

        foreach($results as $record){
            $data[] = $this->getProgressData($record->status, $record->count);
        }

        return new JsonModel(array(
            'id' => $id,
            'title' => $this->reportTitle('Progress'),
            'icon' => 'fa fa-pie-chart',
            'type' => 'Pie',
            'data' => $data,
            'options' => $this->pieChartOption()
        ));
    }
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

        return new JsonModel(array(
            'id' => $id,
            'title' => $this->reportTitle('Overdue'),
            'icon' => 'fa fa-calendar',
            'type' => 'Pie',
            'data' => $data,
            'options' => $this->pieChartOption()
        ));
    }
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
        foreach($staffList as $staff){
            $labels[] = $staff->staffName . "({$staff->staffCode})";
            $assign = 0;
            $process = 0;
            $finish = 0;
            $complete = 0;
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
                }
            }

            $assignedList[] = $assign;
            $processingList[] = $process;
            $finishedList[] = $finish;
            $completedList[] = $complete;
            $totalList[] = $assign + $process + $finish + $complete;
        }

        $data = array(
            'labels' => $labels,
            'datasets' => array(
                $this->getWorkloadData('T', $totalList),
                $this->getWorkloadData('A', $assignedList),
                $this->getWorkloadData('F', $finishedList),
                $this->getWorkloadData('P', $processingList),
                $this->getWorkloadData('C', $completedList),
            ),
        );

        return new JsonModel(array(
            'id' => $id,
            'title' => $this->reportTitle('Workload'),
            'icon' => 'fa fa-balance-scale',
            'type' => 'Bar',
            'data' => $data,
            'options' => $this->barChartOption()
        ));
    }
}