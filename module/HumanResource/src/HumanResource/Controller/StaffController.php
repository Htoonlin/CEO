<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/8/2015
 * Time: 1:50 PM
 */

namespace HumanResource\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Core\Helper\ChartHelper;
use Core\Model\ApiModel;
use Core\SundewController;
use Core\SundewExporting;
use HumanResource\DataAccess\AttendanceDataAccess;
use HumanResource\DataAccess\DepartmentDataAccess;
use HumanResource\DataAccess\PositionDataAccess;
use Application\DataAccess\UserDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Staff;
use HumanResource\Helper\StaffHelper;
use ProjectManagement\DataAccess\TaskDataAccess;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Where;
use Zend\View\Model\ViewModel;

/**
 * Class StaffController
 * @package HumanResource\Controller
 */
class StaffController extends SundewController
{
    /**
     * @return StaffDataAccess
     */
    private function staffTable()
    {
        return new StaffDataAccess($this->getDbAdapter());
    }


    private function taskTable(){
        return new TaskDataAccess($this->getDbAdapter());
    }

    private function attendanceTable(){
        return new AttendanceDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function userCombos()
    {
        $dataAccess=new UserDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('userId', 'userName');
    }

    /**
     * @return array
     */
    private function statusCombo()
    {
        $dataAccess = new ConstantDataAccess($this->getDbAdapter());
        return $dataAccess->getComboByName('default_status');
    }

    /**
     * @return array
     */
    private function positionCombos()
    {
        $dataAccess=new PositionDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('positionId', 'name');
    }

    /**
     * @return array
     */
    private function departments()
    {
        $dataAccess=new DepartmentDataAccess($this->getDbAdapter());
        return $dataAccess->getChildren();
    }

    /**
     * @return array
     */
    private function currencyCombo(){
        $dataAccess = new CurrencyDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('currencyId', 'code');
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort', 'staffName');
        $sortBy = $this->params()->fromQuery('by', 'asc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', $this->getPageSize());
        $this->setPageSize($pageSize);

        $paginator=$this->staffTable()->fetchAll(true, $filter, $sort, $sortBy);
        $paginator->setCurrentPageNumber($page);

        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter'=>$filter,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public  function detailAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $action = $this->params()->fromQuery('action', '');

        $helper=new StaffHelper($this->getDbAdapter());
        $form = $helper->getForm($this->userCombos(), $this->positionCombos(), $this->currencyCombo(), $this->statusCombo());
        $staff = $this->staffTable()->getStaff($id);
        $isEdit = true;

        if(!$staff)
        {
            $isEdit=false;
            $staff=new Staff();
        }

        if($action == 'clone'){
            $isEdit = false;
            $id = 0;
            $staff->setStaffId(0);
        }
        $form->bind($staff);
        $request = $this->getRequest();

        if($request->isPost())
        {
            $post_data=$request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter($id));
            if($form->isValid()){
                $this->staffTable()->saveStaff($staff);
                $this->flashMessenger()->addSuccessMessage('Save Successful');
                return $this->redirect()->toRoute('hr_staff');
            }else{
                $this->flashMessenger()->addErrorMessage($form->getMessages());
            }
        }
        return new ViewModel(array('form'=>$form,
            'id'=>$id,
            'isEdit'=>$isEdit,
            'departments' => $this->departments()));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function  deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $staff = $this->staffTable()->getStaff($id);
        if ($staff) {
            $this->staffTable()->deleteStaff($id);
            $this->flashMessenger()->addInfoMessage('Delete Successful');
        }
        return $this->redirect()->toRoute("hr_staff");
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->staffTable()->fetchAll(false));

        $response=$this->getResponse();
        $filename='attachment; filename="Staff-'.date('YmdHis').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    public function reportAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);

        return new ViewModel([
            'staffId' => $id,
        ]);
    }

    public function apiWorkHoursAction()
    {
        $id=(int)$this->params()->fromRoute('id',0);
        $year = (int)$this->params()->fromQuery('year', date('Y'));

        $where = new Where();
        $where->equalTo('staffId', $id)
            ->and->literal('YEAR(attendanceDate) = ' . $year);
        $workHours = $this->attendanceTable()->getWorkHours($where);
        $labels = array();
        $data = array();
        foreach($workHours as $monthlyHours){
            $labels[] = $monthlyHours->year . '-' . $monthlyHours->month;
            $data[] = $monthlyHours->hours;
        }

        $colors = ChartHelper::getColor('blue');
        $label = 'Hours/month';
        $chartData = array(
            'labels' => $labels,
            'datasets' => array(
                'label' => $label,
                'fillColor' => 'rgba(220, 220, 220,0.2)',
                'strokeColor' => $colors['color'],
                'pointColor' => $colors['color'],
                'pointStrokeColor' => "#999",
                'pointHighlightFill' => $colors['highlight'],
                'pointHighlightStroke' => "rgba(220,220,220,1)",
                'data' => $data,
            ),
        );

        return new ApiModel(array(
            'type' => 'Line',
            'data' => $chartData,
            'options' => ChartHelper::lineChartOption(),
        ));
    }
    public function apiTaskAction()
    {
        $start = $this->params()->fromPost('start', '');
        $end = $this->params()->fromPost('end', '');
        $staffId = (int)$this->params()->fromPost('staffId', 0);

        $api = new ApiModel();
        $request = $this->getRequest();
        if($request->isPost()){
            if(empty($start) || empty($end) || empty($staffId)){
                $api->setStatusCode(400);
            }else{
                $result = $this->taskTable()->getTaskListByDate($staffId, $start, $end);
                $api->setResponseData($result);
            }
        }else{
            $api->setStatusCode(405);
        }
        return $api;
    }

    /**
     * @return ApiModel
     */
    public function apiDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $db=$this->staffTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        $api = new ApiModel();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->staffTable()->deleteStaff($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete Successful!');
        }
        catch (\Exception $ex){
            $conn->rollback();
            $api->setStatusCode(500);
            $api->setStatusMessage($ex->getMessage());
        }
        return $api;
    }

    public function apiBirthdayAction()
    {
        $year = $this->params()->fromPost('year', date('Y', time()));
        $staffs = $this->staffTable()->getActiveStaffs();
        $result = array();

        /**
         * @var staff Staff
         */
        foreach($staffs as $staff)
        {
            $age = $year - date('Y', strtotime($staff->Birthday));
            $result[] = array(
                'staff' => $staff->staffName,
                'birthday' => date('m-d', strtotime($staff->Birthday)),
                'age' => $age,
            );
        }

        return new ApiModel($result);
    }
}
