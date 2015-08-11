<?php
namespace ProjectManagement\DataAccess;

use Application\Service\SundewTableGateway;
use ProjectManagement\Entity\Task;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

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

    public function fetchAll($paginated = false, $filter = '', $orderBy = '', $order = '')
    {
        if($paginated){
        	return $this->paginate($filter, $orderBy, $order);
        }
        return $this->select();
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
