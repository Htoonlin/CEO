<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/23/2015
 * Time: 11:30 AM
 */

namespace Account\Controller;

use Account\DataAccess\VoucherDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewController;
use Zend\View\Model\ViewModel;
use Zend\Form\Element;

class ReportController extends SundewController
{
    /**
     * @return mixed
     */
    private function getSkipTypes()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        $result = json_decode($dataAccess->getConstantByName('closing_type_id')->getValue());
        return array_unique(array($result->open, $result->close));
    }

    /**
     * @return VoucherDataAccess
     */
    private function voucherTable()
    {
        return new VoucherDataAccess($this->getDbAdapter());
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $labels = array();
        $payable = array();
        $receivable = array();

        $data = $this->voucherTable()->getReportData($this->getSkipTypes());
        $count = 0;
        $prev_label = '';
        $payable[$count] = 0;
        $receivable[$count] = 0;

        foreach($data as $row){
            if(!empty($prev_label) && $prev_label != $row->date){
                $labels[$count] = $prev_label;
                $count++;
                $payable[$count] = 0;
                $receivable[$count] = 0;
            }

            if(strtolower($row->type) == 'payable'){
                $payable[$count] = $row->amount;
            }

            if(strtolower($row->type) == 'receivable'){
                $receivable[$count] = $row->amount;
            }

            $prev_label = $row->date;
        }

        if(!empty($prev_label)){
            $labels[$count] = $prev_label;
        }

        return new ViewModel(array(
            'labels' => $labels,
            'payable' => $payable,
            'receivable' => $receivable,
        ));
    }
}