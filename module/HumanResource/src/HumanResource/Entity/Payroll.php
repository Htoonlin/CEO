<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 8:02 PM
 */

namespace HumanResource\Entity;


class Payroll {

    protected $payrollId;
    public function getPayrollId(){return $this->payrollId;}
    public function setPayrollId($value){$this->payrollId=$value;}

    protected  $staffId;
    public function getStaffId(){return $this->staffId;}
    public function setStaffId($value){ $this->staffId=$value;}

    protected $fromDate;
    public function getFromDate(){return $this->fromDate;}
    public function setFormDate($value){$this->fromDate=$value;}

    protected $toDate;
    public function getToDate(){return $this->toDate;}
    public function setToDate($value){$this->toDate=$value;}

    protected $mWd;
    public function getMWd(){return $this->mWd;}
    public function setMWd($value){$this->mWd=$value;}

    protected $sWd;
    public function getSWd(){return $this->sWd;}
    public function setSWd($value){$this->sWd=$value;}

    protected $salary;
    public function getSalary(){return $this->salary;}
    public function setSalary($value){$this->salary=$value;}

    protected $leave;
    public function getLeave(){return $this->leave;}
    public function setLeave($value){$this->leave=$value;}

    protected $absent;
    public function getAbsent(){return $this->absent;}
    public function setAbsent($value){$this->absent=$value;}

    protected $bonus;
    public function getBonus(){return $this->bonus;}
    public function setBonus($value){$this->bonus=$value;}

    protected $deduct;
    public function getDeduct(){return $this->deduct;}
    public function setDeduct($value){$this->deduct=$value;}

}