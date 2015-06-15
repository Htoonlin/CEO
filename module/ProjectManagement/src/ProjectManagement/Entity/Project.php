<?php
/**
 * Created by PhpStorm.
 * User: Sundew
 * Date: 5/25/2015
 * Time: 1:22 PM
 */
namespace ProjectManagement\Entity;

use Zend\Stdlib\ArraySerializableInterface;

class Project implements ArraySerializableInterface{
    protected  $projectId;
    public function getProjectId(){return $this->projectId;}
    public function setProjectId($value){$this->projectId=$value;}

    protected  $code;
    public function getCode(){return $this->code;}
    public function setCode($value){$this->code=$value;}

    protected  $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected  $description;
    public function getDescription(){return $this->description;}
    public function setDescription($value){$this->description=$value;}

    protected $managerId;
    public function getManagerId(){return $this->managerId;}
    public function setManagerId($value){$this->managerId=$value;}

    protected  $startDate;
    public function getStartDate(){return $this->startDate;}
    public function setStartDate($value){$this->startDate=$value;}

    protected  $endDate;
    public function getEndDate(){return $this->endDate;}
    public function setEndDate($value){$this->endDate=$value;}

    protected  $group_code;
    public function getGroupCode(){return $this->group_code;}
    public function setGroupCode($value){$this->group_code=$value;}

    protected  $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    protected $remark;
    public function getRemark(){return $this->remark;}
    public function setRemark($value){$this->remark=$value;}

    public function exchangeArray(array $data){
        $this->projectId=(!empty($data['projectId']))?$data['projectId'] : null;
        $this->code=(!empty($data['code']))?$data['code'] : null;
        $this->name=(!empty($data['name']))?$data['name'] : null;
        $this->description=(!empty($data['description']))?$data['description'] : null;
        $this->managerId=(!empty($data['managerId']))?$data['managerId'] : null;
        $this->startDate=(!empty($data['startDate']))?$data['startDate'] : null;
        $this->endDate=(!empty($data['endDate']))?$data['endDate'] : null;
        $this->group_code=(!empty($data['group_code']))?$data['group_code'] : null;
        $this->status=(!empty($data['status']))?$data['status'] : null;
        $this->remark=(!empty($data['remark']))?$data['remark'] : null;
    }

    public function getArrayCopy(){
        return array(
            'projectId'=>$this->projectId,
            'code'=>$this->code,
            'name'=>$this->name,
            'description'=>$this->description,
            'managerId'=>$this->managerId,
            'startDate'=>$this->startDate,
            'endDate'=>$this->endDate,
            'group_code'=>$this->group_code,
            'status'=>$this->status,
            'remark'=>$this->remark,
        );
    }
}