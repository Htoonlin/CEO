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
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = "tbl_pm_task";
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Task());
        $this->initialize();
    }

    public function fetchAll($projectId, $paginated = false, $filter = '', $orderBy = '', $order = '')
    {
        $view = 'vw_pm_task';
        $select = new Select($view);
        $select->order($orderBy . ' ' . $order);
        $where = new Where();
        $where->equalTo('projectId', $projectId);
        $where->literal("CONCAT_WS(' ', tag, name, fromTime, toTime) LIKE ?", '%' . $filter . '%');
        $select->where($where);
        if($paginated){
        	return $this->paginateWith($select);
        }
        $gateway = new TableGateway($view, $this->adapter);
        return $gateway->select(array('projectId' => $projectId));
    }

    public function getTask($id)
    {
        $id = (int)$id;
        $rowSet = $this->select(array("taskId" => $id));
        if($rowSet == null){
        	 throw new \Exception('Invalid data');
        }
        return $rowSet->current();
    }

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

    public function deleteTask($id)
    {
        $this->delete(array("taskId" => (int)$id));
    }


}
