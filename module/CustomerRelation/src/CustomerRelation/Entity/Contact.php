<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/27/2015
 * Time: 4:57 PM
 */

namespace CustomerRelation\Entity;

use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;

class Contact implements ArraySerializableInterface
{
    protected $contactId;
    public function getContactId(){return $this->contactId;}
    public function setContactId($value){$this->contactId=$value;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $phone;
    public function getPhone(){return $this->phone;}
    public function setPhone($value){$this->phone=$value;}

    protected $email;
    public function getEmail(){return $this->email;}
    public function setEmail($value){$this->email=$value;}

    protected $address;
    public function getAddress(){return $this->address;}
    public function setAddress($value){$this->address=$value;}

    protected $website;
    public function getWebsite(){return $this->website;}
    public function setWebsite($value){$this->website=$value;}

    public $companyId;
    public function getCompanyId(){return $this->companyId;}
    public function setCompanyId($value){$this->companyId=$value;}

    public $notes;
    public function getNotes(){return $this->notes;}
    public function setNotes($value){$this->notes=$value;}

    public $tag;
    public function getTag(){return $this->tag;}
    public function setTag($value){$this->tag=$value;}

    public $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->contactId=(!empty($data['contactId']))?$data['contactId']:null;
        $this->name=(!empty($data['name']))?$data['name']:null;
        $this->phone=(!empty($data['phone']))?$data['phone']:null;
        $this->email=(!empty($data['email']))?$data['email']:null;
        $this->address=(!empty($data['address']))?$data['address']:null;
        $this->website=(!empty($data['website']))?$data['website']:null;
        $this->companyId=(!empty($data['companyId']))?$data['companyId']:null;
        $this->notes=(!empty($data['notes']))?$data['notes']:null;
        $this->tag=(!empty($data['tag']))?$data['tag']:null;
        $this->status=(!empty($data['status']))?$data['status']:null;

    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getLabel()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return "/customerrelation/contact/index/".$this->contactId;
    }

    public function getValue()
    {
        return $this->contactId;
    }
}


