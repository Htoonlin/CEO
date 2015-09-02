<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:36 PM
 */

namespace CustomerRelation\Entity;

use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;
class Proposal implements ArraySerializableInterface
{
    protected $proposalId;
    public function getProposalId(){return $this->proposalId;}
    public function setProposalId($value){$this->proposalId=$value;}

    protected $companyId;
    public function getCompanyId(){return $this->companyId;}
    public function setCompanyId($value){$this->companyId=$value;}

    protected $contactId;
    public function getContactId(){return $this->contactId;}
    public function setContactId($value){ $this->contactId=$value;}

    protected $code;
    public function getCode(){return $this->code;}
    public function setCode($value){$this->code=$value;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $amount;
    public function getAmount(){return $this->amount;}
    public function setAmount($value){$this->amount=$value;}

    protected $currencyId;
    public function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId=$value;}

    protected $proposalDate;
    public function getProposalDate(){return $this->proposalDate;}
    public function setProposalDate($value){$this->proposalDate=$value;}

    protected $proposalFile;
    public function getProposalFile(){return $this->proposalFile;}
    public function setProposalFile($value){$this->proposalFile=$value;}

    protected $notes;
    public function getNotes(){return $this->notes;}
    public function setNotes($value){$this->notes=$value;}

    protected $proposalBy;
    public function getProposalBy(){return $this->propasalBy;}
    public function setProposalBy($value){$this->proposalBy=$value;}

    protected $group_code;
    public function getGroupCode(){return $this->group_code;}
    public function setGroupCode($value){$this->group_code=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->proposalId=(!empty($data['proposalId']))?$data['proposalId']:null;
        $this->companyId=(!empty($data['companyId']))?$data['companyId']:null;
        $this->contactId=(!empty($data['contactId']))?$data['contactId']:null;
        $this->code=(!empty($data['code']))?$data['code']:null;
        $this->name=(!empty($data['name']))?$data['name']:null;
        $this->amount=(!empty($data['amount']))?$data['amount']:null;
        $this->currencyId=(!empty($data['currencyId']))?$data['currencyId']:null;
        $this->proposalDate=(!empty($data['proposalDate']))?$data['proposalDate']:null;
        $this->proposalFile  = (isset($data['proposalFile']))  ? $data['proposalFile']     : null;
        $this->notes=(!empty($data['notes']))?$data['notes']:null;
        $this->proposalBy=(!empty($data['proposalBy']))?$data['proposalBy']:null;
        $this->group_code=(!empty($data['group_code']))?$data['group_code']:null;
        $this->status=(!empty($data['status']))?$data['status']:null;
    }

    public function getArrayCopy()
    {
        return array(
            'proposalId'=>$this->proposalId,
            'companyId'=>$this->companyId,
            'contactId'=>$this->contactId,
            'code'=>$this->code,
            'name'=>$this->name,
            'amount'=>$this->amount,
            'currencyId'=>$this->currencyId,
            'proposalDate'=>$this->proposalDate,
            'proposalFile'=>$this->proposalFile,
            'notes'=>$this->notes,
            'proposalBy'=>$this->proposalBy,
            'group_code'=>$this->group_code,
            'status'=>$this->status,
        );
    }

}