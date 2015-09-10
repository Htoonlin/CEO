<?php
namespace Core\Helper;

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-09-07
 * Time: 07:49 PM
 */
class ChartHelper
{
    public static function getRandomColor(){
        $color = rand(1, 36) * 10;

        return array(
            'color' => 'hsla(' . $color . ', 74%, 44%, 1)',
            'highlight' => 'hsla(' . $color . ', 80%, 65%, 1)',
        );
    }

    public static function getColor($color){
        $chartColor = array(
            'blue' => array(
                'color' => '#4076A0',
                'highlight' => '#5D96C1',
            ),
            'black' => array(
                'color' => '#4D5360',
                'highlight' => '#616774',
            ),
            'green' => array(
                'color' => '#4cd964',
                'highlight' => '#a4e786',
            ),
            'cyan' => array(
                'color' => '#46BFBD',
                'highlight' => '#5AD3D1',
            ),
            'yellow' => array(
                'color' => '#FDB45C',
                'highlight' => '#FFC870',
            ),
            'orange' => array(
                'color' => '#ff5e3a',
                'highlight' => '#ff9500',
            ),
            'red' => array(
                'color' => '#F7464A',
                'highlight' => '#FF5A5E',
            ),
            'pink' => array(
                'color' => '#FF4981',
                'highlight' => '#FF6992',
            ),
            'purple' => array(
                'color' => '#c86edf',
                'highlight' => '#e4b7f0',
            )
        );
        return $chartColor[$color];
    }

    /**
     * @return array
     */
    public static function pieChartOption()
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

    /**
     * @return array
     */
    public static function lineChartOption()
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

    /**
     * @return array
     */
    public static function barChartOption()
    {
        return array(
            'scaleBeginAtZero' => true,
            'scaleShowGridLines' => true,
            'scaleGridLineColor' => "rgba(0,0,0,.05)",
            'scaleGridLineWidth' => 1,
            'scaleShowHorizontalLines' => true,
            'scaleShowVerticalLines' => true,
            'barShowStroke' => true,
            'barStrokeWidth' => 1,
            'barValueSpacing' => 10,
            'barDatasetSpacing' => 2,
            'legendTemplate' => '<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
        );
    }
}