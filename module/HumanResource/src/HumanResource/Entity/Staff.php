<?php
namespace HumanResource\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-09-10 14:07:59
 *
 * @package HumanResource\Entity
 */
class Staff implements ArraySerializableInterface
{

    protected $staffId = null;

    protected $userId = null;

    protected $staffCode = null;

    protected $staffName = null;

    protected $birthday = null;

    protected $positionId = null;

    protected $departmentId = null;

    protected $workHours = null;

    protected $salary = null;

    protected $currencyId = null;

    protected $annualLeave = null;

    protected $permanentDate = null;

    protected $bankCode = null;

    protected $status = null;

    public function getStaffId()
    {
        return $this->staffId;
    }

    public function setStaffId($value)
    {
        $this->staffId = $value;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($value)
    {
        $this->userId = $value;
    }

    public function getStaffCode()
    {
        return $this->staffCode;
    }

    public function setStaffCode($value)
    {
        $this->staffCode = $value;
    }

    public function getStaffName()
    {
        return $this->staffName;
    }

    public function setStaffName($value)
    {
        $this->staffName = $value;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($value)
    {
        $this->birthday = $value;
    }

    public function getPositionId()
    {
        return $this->positionId;
    }

    public function setPositionId($value)
    {
        $this->positionId = $value;
    }

    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    public function setDepartmentId($value)
    {
        $this->departmentId = $value;
    }

    public function getWorkHours()
    {
        return $this->workHours;
    }

    public function setWorkHours($value)
    {
        $this->workHours = $value;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    public function setSalary($value)
    {
        $this->salary = $value;
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setCurrencyId($value)
    {
        $this->currencyId = $value;
    }

    public function getAnnualLeave()
    {
        return $this->annualLeave;
    }

    public function setAnnualLeave($value)
    {
        $this->annualLeave = $value;
    }

    public function getPermanentDate()
    {
        return $this->permanentDate;
    }

    public function setPermanentDate($value)
    {
        $this->permanentDate = $value;
    }

    public function getBankCode()
    {
        return $this->bankCode;
    }

    public function setBankCode($value)
    {
        $this->bankCode = $value;
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
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : null;
        $this->userId = (!empty($data['userId'])) ? $data['userId'] : null;
        $this->staffCode = (!empty($data['staffCode'])) ? $data['staffCode'] : null;
        $this->staffName = (!empty($data['staffName'])) ? $data['staffName'] : null;
        $this->birthday = (!empty($data['birthday'])) ? $data['birthday'] : null;
        $this->positionId = (!empty($data['positionId'])) ? $data['positionId'] : null;
        $this->departmentId = (!empty($data['departmentId'])) ? $data['departmentId'] : null;
        $this->workHours = (!empty($data['workHours'])) ? $data['workHours'] : null;
        $this->salary = (!empty($data['salary'])) ? $data['salary'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->annualLeave = (!empty($data['annual_leave'])) ? $data['annual_leave'] : null;
        $this->permanentDate = (!empty($data['permanentDate'])) ? $data['permanentDate'] : null;
        $this->bankCode = (!empty($data['bankCode'])) ? $data['bankCode'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'staffId' => $this->staffId,
            'userId' => $this->userId,
            'staffCode' => $this->staffCode,
            'staffName' => $this->staffName,
            'birthday' => $this->birthday,
            'positionId' => $this->positionId,
            'departmentId' => $this->departmentId,
            'workHours' => $this->workHours,
            'salary' => $this->salary,
            'currencyId' => $this->currencyId,
            'annual_leave' => $this->annualLeave,
            'permanentDate' => $this->permanentDate,
            'bankCode' => $this->bankCode,
            'status' => $this->status,
        );
    }


}
