<?php
namespace ProjectManagement\DataAccess;

use Application\Service\SundewTableGateway;
use ProjectManagement\Entity\Task;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Predicate;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-11 12:41:35
 *
 * @package ProjectManagement\DataAccess
 */
class TaskDataAccess extends SundewTableGateway
{
    protected $view = 'vw_pm_task';
    /**
     *
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_pm_task";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Task());
        $this->initialize();
    }

    /**
     * @param $managerId
     * @param null $projectId
     * @param bool|false $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|\Zend\Paginator\Paginator
     */
    public function fetchAll($managerId,$projectId = null, $paginated = false, $filter = '', $orderBy = '', $order = '')
    {
        $select = new Select($this->view);
        $select->order($orderBy . ' ' . $order);
        $where = new Where();
        $where->equalTo('managerId', $managerId);
        if($projectId > 0){
            $where->equalTo('projectId', $projectId);
        }else if($projectId == 0){
            $where->isNull('projectId')
            ->OR->equalTo('projectId', 0);
        }
        $where->literal("CONCAT_WS(' ', tag, name, projectName, projectCode, staffCode, staffName, fromTime, toTime) LIKE ?", '%' . $filter . '%');
        $select->where($where);
        if($paginated){
        	return $this->paginateWith($select);
        }

        $gateway = new TableGateway($this->view, $this->adapter);
        return $gateway->select(array('projectId' => $projectId));
    }

    public function getToDoList($staffId)
    {
        $result = $this->select(function(Select $select) use ($staffId){
            $where = new Where();
            $where->equalTo('staffId', $staffId)
            ->AND->in('status', array('A', 'P'));
            $select->where($where)->order('toTime ASC, priority ASC');
        });
        return $result;
    }

    /**
     * @param $id
     * @param $staffId
     * @return array|\ArrayObject|null
     */
    public function getTaskByStaff($id, $staffId)
    {
        $gateway = new TableGateway($this->view, $this->adapter);
        $results = $gateway->select(array('taskId' => $id, 'staffId' => $staffId));
        if(!$results){
            return null;
        }
        return $results->current();
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     * @throws \Exception
     */
    public function getTask($id)
    {
        $id = (int)$id;
        $rowSet = $this->select(array("taskId" => $id));
        if($rowSet == null){
        	 throw new \Exception('Invalid data');
        }
        return $rowSet->current();
    }

    /**
     *
     * @param Task $task
     * @return \ProjectManagement\Entity\Task
     */
    public function saveTask(Task $task)
    {
        $id = $task->getTaskId();
        $data = $task->getArrayCopy();
        if($id > 0){
        	$this->update($data, array("taskId" => $id));
        } else {
        	unset($data["taskId"]);
        	$this->insert($data);
        	$task->setTaskId($this->getLastInsertValue());
        }
        return $task;
    }

    /**
     * @param $id
     */
    public function deleteTask($id)
    {
        $this->delete(array("taskId" => (int)$id));
    }
}
