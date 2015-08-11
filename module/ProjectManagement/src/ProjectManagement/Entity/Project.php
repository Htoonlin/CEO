<?php
namespace ProjectManagement\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-08-10 18:22:09
 *
 * @package ProjectManagement\Entity
 */
class Project implements ArraySerializableInterface
{

    protected $projectId = null;

    protected $code = null;

    protected $name = null;

    protected $description = null;

    protected $repository = null;

    protected $managerId = null;

    protected $startDate = null;

    protected $endDate = null;

    protected $groupCode = null;

    protected $status = null;

    protected $remark = null;

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function setProjectId($value)
    {
        $this->projectId = $value;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($value)
    {
        $this->code = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function setRepository($value)
    {
        $this->repository = $value;
    }

    public function getManagerId()
    {
        return $this->managerId;
    }

    public function setManagerId($value)
    {
        $this->managerId = $value;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($value)
    {
        $this->startDate = $value;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($value)
    {
        $this->endDate = $value;
    }

    public function getGroupCode()
    {
        return $this->groupCode;
    }

    public function setGroupCode($value)
    {
        $this->groupCode = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getRemark()
    {
        return $this->remark;
    }

    public function setRemark($value)
    {
        $this->remark = $value;
    }

    public function exchangeArray(array $data)
    {
        $this->projectId = (!empty($data['projectId'])) ? $data['projectId'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->repository = (!empty($data['repository'])) ? $data['repository'] : null;
        $this->managerId = (!empty($data['managerId'])) ? $data['managerId'] : null;
        $this->startDate = (!empty($data['startDate'])) ? $data['startDate'] : null;
        $this->endDate = (!empty($data['endDate'])) ? $data['endDate'] : null;
        $this->groupCode = (!empty($data['group_code'])) ? $data['group_code'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->remark = (!empty($data['remark'])) ? $data['remark'] : null;
    }

    public function getArrayCopy()
    {
        return array(
        	'projectId' => $this->projectId,
        	'code' => $this->code,
        	'name' => $this->name,
        	'description' => $this->description,
        	'repository' => $this->repository,
        	'managerId' => $this->managerId,
        	'startDate' => $this->startDate,
        	'endDate' => $this->endDate,
        	'group_code' => $this->groupCode,
        	'status' => $this->status,
        	'remark' => $this->remark,
        );
    }


}
