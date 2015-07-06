<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/6/2015
 * Time: 2:46 PM
 */

namespace Application\Service;


class SundewExporting {
    protected $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getExcel()
    {
        $excelObj = new \PHPExcel();
        $excelObj->setActiveSheetIndex(0);
        $sheet = $excelObj->getActiveSheet();

        $columns = array();

        $excelColumn = "A";
        $start = 2;

        foreach($this->data as $row)
        {
            if(!is_array($row)){
                $exportData = $row->getArrayCopy();
            }else{
                $exportData = $row;
            }

            if(count($columns) == 0){
                $columns = array_keys($exportData);
            }

            foreach($exportData as $key=>$value){
                $cellId = $excelColumn . $start;
                $sheet->setCellValue($cellId, $value);
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

        return $excelOutput;
    }
}