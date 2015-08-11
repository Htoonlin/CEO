<?php
namespace ProjectManagement\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-11 10:45:17
 *
 * @package ProjectManagement\Entity
 */
class Task implements ArraySerializableInterface
{

    protected $taskId = null;

    protected $projectId = null;

    protected $name = null;

    protected $tag = null;

    protected $level = null;

    protected $managerId = null;

    protected $fromTime = null;

    protected $toTime = null;

    protected $parentId = null;

    protected $predecessorId = null;

    protected $priority = null;

    protected $remark = null;

    protected $current = null;

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

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function setProjectId($value)
    {
        $this->projectId = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($value)
    {
        $this->tag = $value;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($value)
    {
        $this->level = $value;
    }

    public function getManagerId()
    {
        return $this->managerId;
    }

    public function setManagerId($value)
    {
        $this->managerId = $value;
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

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($value)
    {
        $this->parentId = $value;
    }

    public function getPredecessorId()
    {
        return $this->predecessorId;
    }

    public function setPredecessorId($value)
    {
        $this->predecessorId = $value;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($value)
    {
        $this->priority = $value;
    }

    public function getRemark()
    {
        return $this->remark;
    }

    public function setRemark($value)
    {
        $this->remark = $value;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($value)
    {
        $this->current = $value;
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
        $this->projectId = (!empty($data['projectId'])) ? $data['projectId'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->tag = (!empty($data['tag'])) ? $data['tag'] : null;
        $this->level = (!empty($data['level'])) ? $data['level'] : null;
        $this->managerId = (!empty($data['managerId'])) ? $data['managerId'] : null;
        $this->fromTime = (!empty($data['fromTime'])) ? $data['fromTime'] : null;
        $this->toTime = (!empty($data['toTime'])) ? $data['toTime'] : null;
        $this->parentId = (!empty($data['parentId'])) ? $data['parentId'] : null;
        $this->predecessorId = (!empty($data['predecessorId'])) ? $data['predecessorId'] : null;
        $this->priority = (!empty($data['priority'])) ? $data['priority'] : null;
        $this->remark = (!empty($data['remark'])) ? $data['remark'] : null;
        $this->current = (!empty($data['current'])) ? $data['current'] : null;
        $this->finished = (!empty($data['finished'])) ? $data['finished'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'taskId' => $this->taskId,
            'projectId' => $this->projectId,
            'name' => $this->name,
            'tag' => $this->tag,
            'level' => $this->level,
            'managerId' => $this->managerId,
            'fromTime' => $this->fromTime,
            'toTime' => $this->toTime,
            'parentId' => $this->parentId,
            'predecessorId' => $this->predecessorId,
            'priority' => $this->priority,
            'remark' => $this->remark,
            'current' => $this->current,
            'finished' => $this->finished,
            'status' => $this->status,
        );
    }


}
