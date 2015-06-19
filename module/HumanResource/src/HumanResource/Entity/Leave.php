<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 1:21 PM
 */

namespace HumanResource\Entity;


class Leave {
    protected $leaveId;
    public function getLeaveId(){return $this->leaveId;}
    public function setLeaveId($value){$this->leaveId = $value;}

    protected $staffId;
    public function getStaffId(){return $this->staffId;}
    public function setStaffId($value){$this->staffId = $value;}

    protected $leaveType;
    public function getLeaveType(){return $this->leaveType;}
    public function setLeaveType($value){$this->leaveType = $value;}

    protected $date;
    public function getDate(){return $this->date;}
    public function setDate($value){$this->date = $value;}

    protected $description;
    public function getDescription(){return $this->description;}
    public function setDescription($value){$this->description = $value;}

    public function exchangeArray(array $data)
    {
        $this->leaveId = (!empty($data['leaveId'])) ? $data['leaveId'] : null;
        $this->staffId = (!empty($data['staffId'])) ? $data['staffId'] : 0;
        $this->leaveType = (!empty($data['leaveType'])) ? $data['leaveType'] : 'E';
        $this->leaveType = (!empty($data['date'])) ? $data['date'] : date('Y-m-d', time());
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'leaveId' => $this->leaveId,
            'staffId' => $this->staffId,
            'leaveType' => $this->leaveType,
            'date' => $this->date,
            'description' => $this->description
        );
    }
}