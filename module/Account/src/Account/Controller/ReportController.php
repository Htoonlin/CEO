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
use Core\SundewController;
use Core\SundewExporting;
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

        foreach ($data as $row) {
            if (!empty($prev_label) && $prev_label != $row->date) {
                $labels[$count] = $prev_label;
                $count++;
                $payable[$count] = 0;
                $receivable[$count] = 0;
            }

            if (strtolower($row->type) == 'payable') {
                $payable[$count] = $row->amount;
            }

            if (strtolower($row->type) == 'receivable') {
                $receivable[$count] = $row->amount;
            }

            $prev_label = $row->date;
        }
        if (!empty($prev_label)) {
            $labels[$count] = $prev_label;
        }

        return new ViewModel(array(
            'labels' => $labels,
            'payable' => $payable,
            'receivable' => $receivable,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Exception
     */
    public function detailAction()
    {
        $year = $this->params()->fromRoute('year', date('Y'));
        $month = $this->params()->fromRoute('month', date('m'));
        $page = (int)$this->params()->fromQuery('page', 1);
        $filter = $this->params()->fromQuery('filter', '');
        $sort = $this->params()->fromQuery('sort', 'voucherNo');
        $sortBy = $this->params()->fromQuery('by', 'asc');
        $pageSize = $this->params()->fromQuery('size', 10);

        $fromDate = $year . '-' . $month . '-01';
        if($month == 12){
            $toDate = ($year + 1) . '-01-01';
        }else{
            $toDate = $year . '-' . ($month + 1) . '-01';
        }

        $paginator = $this->voucherTable()->getVouchersByDate($fromDate, $toDate, 0,
            $this->getSkipTypes(), true, $filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'date' => date('M, Y', strtotime($fromDate)),
            'year' => $year,
            'month' => $month,
            'paginator' => $paginator,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter' => $filter
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $year = $this->params()->fromRoute('year', date('Y'));
        $month = $this->params()->fromRoute('month', date('m'));

        $fromDate = $year . '-' . $month . '-01';
        if($month == 12){
            $toDate = ($year + 1) . '-01-01';
        }else{
            $toDate = $year . '-' . ($month + 1) . '-01';
        }

        $vouchers = $this->voucherTable()->getVouchersByDate($fromDate, $toDate, 0,
            $this->getSkipTypes(), false);

        $response = $this->getResponse();

        $filename = 'attachment; filename="Vouchers(' . $year . '-' . $month . ')-' . date('YmdHis') . '.xlsx"';
        $export = new SundewExporting($vouchers);
        $excel = $export->getExcel();

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($excel);

        return $response;
    }
}
