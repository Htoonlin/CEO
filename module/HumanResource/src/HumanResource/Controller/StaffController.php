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
use Application\Service\SundewController;
use Application\Service\SundewExporting;
use HumanResource\DataAccess\DepartmentDataAccess;
use HumanResource\DataAccess\PositionDataAccess;
use Application\DataAccess\UserDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use HumanResource\Entity\Staff;
use HumanResource\Helper\StaffHelper;
use Zend\View\Model\JsonModel;
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
        return $dataAccess->getComboData('positionId','name');
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
        $pageSize = (int)$this->params()->fromQuery('size', 10);

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
        $helper=new StaffHelper($this->getDbAdapter());
        $form = $helper->getForm($this->userCombos(), $this->positionCombos(), $this->currencyCombo(), $this->statusCombo());
        $staff = $this->staffTable()->getStaff($id);
        $isEdit = true;

        if(!$staff)
        {
            $isEdit=false;
            $staff=new Staff();
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
                var_dump($form->getMessages());
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
        $filename='attachment; filename="Staff-'.date('Ymdhis').'.xlsx"';

        $headers=$response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    /**
     * @return JsonModel
     */
    public function jsonDeleteAction()
    {
        $data=$this->params()->fromPost('chkId', array());
        $db=$this->staffTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();

        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->staffTable()->deleteStaff($id);
            }
            $conn->commit();
            $message='success';
            $this->flashMessenger()->addInfoMessage('Delete Successful!');

        }
        catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }
        return new JsonModel(array("message"=>$message));
    }
}
