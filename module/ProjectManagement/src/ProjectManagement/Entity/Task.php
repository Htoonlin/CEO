<?php
namespace ProjectManagement\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-12 19:45:21
 *
 * @package ProjectManagement\Entity
 */
class Task implements ArraySerializableInterface
{

    protected $taskId = null;

    protected $name = null;

    protected $current = null;

    protected $staffId = null;

    protected $fromTime = null;

    protected $toTime = null;

    protected $projectId = null;

    protected $predecessorId = null;

    protected $level = null;

    protected $maxBudget = null;

    protected $currencyId = null;

    protected $priority = null;

    protected $description = null;

    protected $tag = null;

    protected $managerId = null;

    protected $finished = null;

    protected $status = null;

    public function getTaskId()
    {
        return $this->taskId;
    }

    public function setTaskId($value)
    {
        $this->taskId = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($value)
    {
        $this->current = $value;
    }

    public function getStaffId()
    {
        return $this->staffId;
    }

    public function setStaffId($value)
    {
        $this->staffId = $value;
    }

    public function getFromTime()
    {
        return $this->fromTime;
    }

    public function setFromTime($value)
    {
        $this->fromTime = $value;
    }

    public function getToTime()
    {
        return $this->toTime;
    }

    public function setToTime($value)
    {
        $this->toTime = $value;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function setProjectId($value)
    {
        $this->projectId = $value;
    }

    public function getPredecessorId()
    {
        return $this->predecessorId;
    }

    public function setPredecessorId($value)
    {
        $this->predecessorId = $value;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($value)
    {
        $this->level = $value;
    }

    public function getMaxBudget()
    {
        return $this->maxBudget;
    }

    public function setMaxBudget($value)
    {
        $this->maxBudget = $value;
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setCurrencyId($value)
    {
        $this->currencyId = $value;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($value)
    {
        $this->priority = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($value)
    {
        $this->tag = $value;
    }

    public function getManagerId()
    {
        return $this->managerId;
    }

    public function setManagerId($value)
    {
        $this->managerId = $value;
    }

    public function getFinished()
    {
        return $this->finished;
    }

    public function setFinished($value)
    {
        $this->finished = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function exchangeArray(array $data)
    {
        $this->taskId = (!empty($data['taskId'])) ? $data['taskId'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->current = (!empty($data['current'])) ? $data['current'] : 0;
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : null;
        $this->fromTime = (!empty($data['fromTime'])) ? $data['fromTime'] : null;
        $this->toTime = (!empty($data['toTime'])) ? $data['toTime'] : null;
        $this->projectId = (!empty($data['projectId'])) ? $data['projectId'] : null;
        $this->predecessorId = (!empty($data['predecessorId'])) ? $data['predecessorId'] : null;
        $this->level = (!empty($data['level'])) ? $data['level'] : 0;
        $this->maxBudget = (!empty($data['maxBudget'])) ? $data['maxBudget'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->priority = (!empty($data['priority'])) ? $data['priority'] : 0;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->tag = (!empty($data['tag'])) ? $data['tag'] : null;
        $this->managerId = (!empty($data['managerId'])) ? $data['managerId'] : 0;
        $this->finished = (!empty($data['finished'])) ? $data['finished'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return array(
        	'taskId' => $this->taskId,
        	'name' => $this->name,
        	'current' => $this->current,
        	'staffId' => $this->staffId,
        	'fromTime' => $this->fromTime,
        	'toTime' => $this->toTime,
        	'projectId' => $this->projectId,
        	'predecessorId' => $this->predecessorId,
        	'level' => $this->level,
        	'maxBudget' => $this->maxBudget,
        	'currencyId' => $this->currencyId,
        	'priority' => $this->priority,
        	'description' => $this->description,
        	'tag' => $this->tag,
        	'managerId' => $this->managerId,
        	'finished' => $this->finished,
        	'status' => $this->status,
        );
    }


}
