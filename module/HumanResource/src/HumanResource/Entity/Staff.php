<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 4:40 PM
 */

namespace HumanResource\Entity;

use Zend\Stdlib\ArraySerializableInterface;

class Staff implements  ArraySerializableInterface
{
    protected $staffId;
    public function getStaffId(){return $this->staffId;}
    public function setStaffId($value){$this->staffId=$value;}

    protected $userId;
    public function getUserId() {return $this->userId;}
    public function setUserId($value){$this->userId=$value;}

    protected $staffCode;
    public function getStaffCode(){return $this->staffCode;}
    public function setStaffCode($value){$this->staffCode=$value;}

    protected $staffName;
    public function getStaffName(){return $this->staffName;}
    public function setStaffName($value){$this->staffName=$value;}

    protected $positionId;
    public function getPositionId(){return $this->positionId;}
    public function setPositionId($value){$this->positionId=$value;}

    protected $departmentId;
    public function getDepartmentId(){return $this->departmentId;}
    public function setDepartmentId($value){$this->departmentId=$value;}

    protected $salary;
    public function getSalary(){return $this->salary;}
    public function setSalary($value){$this->salary=$value;}

    protected $annualLeave;
    public function getAnnualLeave(){return $this->annualLeave;}
    public function setAnnualLeave($value){$this->annualLeave=$value;}

    protected $permanentDate;
    public function getPermanentDate(){return $this->permanentDate;}
    public function setPermanentDate($value){$this->permanentDate=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    protected $birthday;
    public function getBirthday(){return $this->birthday;}
    public function setBirthday($value){$this->birthday = $value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId = $value;}

    protected $bankCode;
    public function getBankCode(){return $this->bankCode;}
    public function setBankCode($value){$this->bankCode = $value;}

    public function exchangeArray(array $data)
    {
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : null;
        $this->staffName =(!empty($data['staffName']))?$data['staffName'] : null;
        $this->userId = (!empty($data['userId']))? $data['userId'] : null;
        $this->staffCode=(!empty($data['staffCode']))? $data['staffCode']: null;
        $this->positionId=(!empty($data['positionId']))? $data['positionId']: null;
        $this->departmentId=(!empty($data['departmentId']))? $data['departmentId']: null;
        $this->salary=(!empty($data['salary']))? $data['salary']: null;
        $this->annualLeave=(!empty($data['annual_leave']))? $data['annual_leave']: null;
        $this->permanentDate = (!empty($data['permanentDate']))? $data['permanentDate']: null;
        $this->status = (!empty($data['status']))? $data['status']: null;
        $this->birthday = (!empty($data['birthday']))? $data['birthday'] : null;
        $this->currencyId = (!empty($data['currencyId'])) ? $data['currencyId'] : null;
        $this->bankCode = (!empty($data['bankCode'])) ? $data['bankCode'] : null;
    }
    public function getArrayCopy()
    {
        return array(
            'staffId' => $this->staffId,
            'staffName' => $this->staffName,
            'userId' => $this->userId,
            'staffCode' => $this->staffCode,
            'positionId' => $this->positionId,
            'departmentId' => $this->departmentId,
            'salary' => $this->salary,
            'annual_leave' => $this->annualLeave,
            'permanentDate' => $this->permanentDate,
            'status' => $this->status,
            'birthday' => $this->birthday,
            'currencyId' => $this->currencyId,
            'bankCode' => $this->bankCode,
        );
    }

}