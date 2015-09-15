<?php
namespace ProjectManagement\DataAccess;

use Core\SundewTableGateway;
use ProjectManagement\Entity\Task;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;

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
     * @param Int $userId
     */
    public function __construct(Adapter $dbAdapter, $userId)
    {
        $this->table = "tbl_pm_task";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Task());
        $this->initialize();

        $this->useSoftDelete = true;
        parent::__construct($userId);
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

        $select = new Select($this->view);
        $select->where(array('projectId' => $projectId));
        return $this->selectOther($select);
    }

    public function getTaskListByDate($staffId, $start, $end){
        $select = new Select($this->view);
        $where = new Where();
        $where->equalTo('staffId', $staffId)
            ->AND->greaterThanOrEqualTo('fromTime', $start)
            ->AND->lessThan('toTime', $end);
        $select->where($where)->order('priority ASC');

        return $this->selectOther($select);
    }

    public function getToDoList($staffId)
    {
        $select = new Select($this->view);
        $where = new Where();
        $where->equalTo('staffId', $staffId)
            ->AND->lessThanOrEqualTo('fromTime', date('Y-m-d H:m:s'))
            ->AND->in('status', array('A', 'P'));
        $select->where($where)->order('toTime ASC, priority ASC');
        return $this->selectOther($select);
    }

    /**
     * @param $id
     * @param $staffId
     * @return array|\ArrayObject|null
     */
    public function getTaskByStaff($id, $staffId)
    {
        $select = new Select($this->view);
        $select->where(array('taskId' => $id, 'staffId' => $staffId));
        $results = $this->selectOther($select);
        if(!$results){
            return null;
        }
        return $results->current();
    }

    public function getTaskValue($staffId)
    {
        $query = "SELECT SUM((level + 1)) as exp from " . $this->table . " WHERE status = 'C' AND staffId = " . $staffId;
        $statement = $this->adapter->query($query);
        $result = $statement->execute();
        return $result->current();
    }
    public function getTaskView($id){
        $select = new Select($this->view);
        $select->where(array('taskId' => $id));
        $results = $this->selectOther($select);
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
