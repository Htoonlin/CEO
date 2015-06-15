<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 5:11 PM
 */

namespace HumanResource\Entity;


class Attendance {

    protected $attendanceId;
    public function getAttendanceId(){return $this->attendanceId;}
    public function  setAttendanceId($value){$this->attendanceId=$value;}

    protected  $staffId;
    public function getStaffId(){return $this->staffId;}
    public function setStaffId($value){ $this->staffId=$value;}

    protected $attendance;
    public function getAttendance(){return $this->attendance;}
    public function  setAttendance($value){$this->attendance=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function  setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->attendanceId = (!empty($data['attendanceId'])) ? $data['attendanceId'] : null;
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : null;
        $this->attendance = (!empty($data['attendance'])) ? $data['attendance'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}