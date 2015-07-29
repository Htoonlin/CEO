<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 3:25 PM
 */

namespace HumanResource\Controller;

use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewController;
use Application\Service\SundewExporting;
use HumanResource\DataAccess\LeaveDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Leave;
use HumanResource\Helper\LeaveHelper;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class LeaveController
 * @package HumanResource\Controller
 */
class LeaveController extends SundewController
{
    /**
     * @return LeaveDataAccess
     */
    private function leaveTable()
    {
        return new LeaveDataAccess($this->getDbAdapter());
    }

    /**
     * @return StaffDataAccess
     */
    private function staffTable()
    {
        return new StaffDataAccess($this->getDbAdapter());
    }
    private $annualLeave;
    private $staffList;
    private $statusList;
    private $leaveTypeList;

    private function initCombo()
    {
        $staffDA = new StaffDataAccess($this->getDbAdapter());
        $constantDA = new ConstantDataAccess($this->getDbAdapter());
        $this->staffList = $staffDA->getComboData('staffId', 'staffCode');
        $this->statusList = $constantDA->getComboByName('leave_status');
        unset($this->statusList['R']);

        $result = $constantDA->getConstantByName('leave_type');
        $leaveTypes = json_decode($result->getValue());

        if(!$this->leaveTypeList){
            $comboList = array();
            foreach($leaveTypes as $leave){
                $comboList[$leave->id] = $leave->title;
                $this->annualLeave[$leave->id] = $leave->value;
            }
            $this->leaveTypeList = $comboList;
        }
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'date');
        $sortBy = $this->params()->fromQuery('by', 'desc');
        $filter = $this->params()->fromQuery('filter', '');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator = $this->leaveTable()->fetchAll(true, $filter, $sort, $sortBy);
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
        $staff = (int)$this->params()->fromQuery('staff', 0);

        $helper = new LeaveHelper();
        $this->initCombo();
        $leave = $this->leaveTable()->getLeave($id);
        $isSave = false;
        if(!$leave){
            $leave = new Leave();
            $isSave = true;
            $form = $helper->getForm($this->staffList, $this->statusList, $this->leaveTypeList);
        }else{
            $formType = 'V';
            if($leave->getStatus() == 'R'){
                $isSave = true;
                $formType = 'R';
            }
            $form = $helper->getForm($this->staffList, $this->statusList, $this->leaveTypeList, $formType);
        }

        if($staff > 0){
            $leave->setStaffId($staff);
        }

        $form->bind($leave);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = array_merge($leave->getArrayCopy(), $request->getPost()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($form->getInputFilter());
            if($form->isValid()){

                $hasLeave = $this->leaveTable()->getLeaveByStaff($leave->getStaffId(), $leave->getDate());

                if($hasLeave && $hasLeave->getStatus() == 'A'){
                    $this->flashMessenger()->addWarningMessage('Sorry! you already approved leave for this date.');
                }else{
                    if($leave->getStatus() == 'A' &&
                        in_array($leave->getLeaveType(), array_keys($this->annualLeave))){
                        $db = $this->leaveTable()->getAdapter();
                        $conn = $db->getDriver()->getConnection();
                        $conn->beginTransaction();
                        try{
                            $deduct = $this->annualLeave[$leave->getLeaveType()];
                            $this->staffTable()->updateLeave($deduct, $leave->getStaffId());
                            $this->leaveTable()->saveLeave($leave);
                            $conn->commit();
                        }catch(\Exception $ex){
                            $conn->rollback();
                            $this->flashMessenger()->addErrorMessage($ex->getMessage());
                        }
                    }else{
                        $this->leaveTable()->saveLeave($leave);
                    }
                    $this->flashMessenger()->addSuccessMessage('Save successful.');
                }
                return $this->redirect()->toRoute('hr_leave');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'isSave' => $isSave,
            'backLink' => $this->getRequest()->getHeader('Referer')->getUri(),
        ));
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->leaveTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Leave-' . date('YmdHis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return JsonModel
     */
    public function jsonLeaveAction()
    {
        $staffId = $this->params()->fromQuery('staffId', 0);
        $date = $this->params()->fromQuery('date', date('Y-m-D', time()));
        $leave = $this->leaveTable()->getLeaveByStaff($staffId, $date);
        if($leave && $leave->getStatus() == 'A'){
            return new JsonModel(array(
                'status' => true,
                'result' => $leave->getArrayCopy()
            ));
        }
        return new JsonModel(array(
            'status' => false,
            'result' => array('staffId' => $staffId, 'date' => $date)
        ));
    }
}