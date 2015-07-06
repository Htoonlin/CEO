<?php
namespace HumanResource\Controller;

use Application\DataAccess\ConstantDataAccess;
use Application\Service\SundewExporting;
use HumanResource\DataAccess\PositionDataAccess;
use HumanResource\Entity\Position;
use HumanResource\Helper\PositionHelper;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class PositionController extends AbstractActionController
{
    private function positionTable()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        return new PositionDataAccess($adapter);
    }

    private function statusCombo()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess = new ConstantDataAccess($adapter);
        return $dataAccess->getComboByName('default_status');
    }

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
    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $helper = new PositionHelper($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form = $helper->getForm($this->statusCombo());
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

