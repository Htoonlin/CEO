<?php
namespace HumanResource\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewController;
use Application\Service\SundewExporting;
use HumanResource\DataAccess\PositionDataAccess;
use HumanResource\Entity\Position;
use HumanResource\Helper\PositionHelper;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class PositionController
 * @package HumanResource\Controller
 */
class PositionController extends SundewController
{
    /**
     * @return PositionDataAccess
     */
    private function positionTable()
    {
        return new PositionDataAccess($this->getDbAdapter());
    }

    /**
     * @return array
     */
    private function currencyCombo(){
        $dataAccess = new CurrencyDataAccess($this->getDbAdapter());
        return $dataAccess->getComboData('currencyId', 'code');
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
     * @return ViewModel
     */
    public function indexAction()
    {
        $page = (int) $this->params()->fromQuery('page', 1);
        $sort = $this->params()->fromQuery('sort', 'name');
        $sortBy = $this->params()->fromQuery('by', 'asc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator = $this->positionTable()->fetchAll(true,$filter, $sort, $sortBy);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);

        return new ViewModel(array(
            'paginator' => $paginator,
            'sort' => $sort,
            'sortBy' => $sortBy,
            'filter'=>$filter,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $helper = new PositionHelper($this->getDbAdapter());
        $form = $helper->getForm($this->statusCombo(), $this->currencyCombo());
        $position = $this->positionTable()->getPosition($id);
        $isEdit = true;
        if(!$position){
            $isEdit = false;
            $position = new Position();
        }

        $form->bind($position);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = $request->getPost()->toArray();
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter(($isEdit ? $post_data['positionId'] : 0), $post_data['name']));
            if($form->isValid()){

                $this->positionTable()->savePosition($position);

                $this->flashMessenger()->addSuccessMessage('Save successful');
                return $this->redirect()->toRoute('hr_position');
            }
        }

        return new ViewModel(array('form' => $form,
            'id' => $id, 'isEdit' => $isEdit));
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $position = $this->positionTable()->getPosition($id);
        if($position){
            $this->positionTable()->deletePosition($id);
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }

        return $this->redirect()->toRoute("hr_position");
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function exportAction()
    {
        $export = new SundewExporting($this->positionTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Position-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
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
        $data = $this->params()->fromPost('chkId', array());
        $db = $this->positionTable()->getAdapter();
        $conn = $db->getDriver()->getConnection();
        $conn->beginTransaction();
        try{
            foreach($data as $id){
                $this->positionTable()->deletePosition($id);
            }
            $conn->commit();
            $this->flashMessenger()->addInfoMessage('Delete successful!');
        }catch(\Exception $ex){
            $conn->rollback();
            $message = $ex->getMessage();
        }
        return new JsonModel(array("message" => $message));
    }
}

