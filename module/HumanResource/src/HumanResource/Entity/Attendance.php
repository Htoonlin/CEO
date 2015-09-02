<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/5/2015
 * Time: 5:11 PM
 */

namespace HumanResource\Entity;


use Zend\Stdlib\ArraySerializableInterface;

class Attendance implements ArraySerializableInterface{

    protected $attendanceId;
    public function getAttendanceId(){return $this->attendanceId;}
    public function  setAttendanceId($value){$this->attendanceId=$value;}

    protected  $staffId;
    public function getStaffId(){return $this->staffId;}
    public function setStaffId($value){ $this->staffId=$value;}

    protected $attendanceDate;
    public function getAttendanceDate(){return $this->attendanceDate;}
    public function  setAttendanceDate($value){$this->attendanceDate=$value;}

    protected $inTime;
    public function getInTime(){return $this->inTime;}
    public function setInTime($value){$this->inTime = $value;}

    protected $outTime;
    public function getOutTime(){return $this->outTime;}
    public function setOutTime($value){$this->outTime = $value;}

    public function exchangeArray(array $data)
    {
        $this->attendanceId = (!empty($data['attendanceId'])) ? $data['attendanceId'] : null;
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : null;
        $this->attendanceDate = (!empty($data['attendanceDate'])) ? $data['attendanceDate'] : date('Y-m-d', time());
        $this->inTime = (!empty($data['inTime'])) ? $data['inTime'] : null;
        $this->outTime = (!empty($data['outTime'])) ? $data['outTime'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'attendanceId' => $this->attendanceId,
            'staffId' => $this->staffId,
            'attendanceDate' => $this->attendanceDate,
            'inTime' => $this->inTime,
            'outTime' => $this->outTime,
        );
    }
}