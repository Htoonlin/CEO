<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 11:51 AM
 */

namespace CustomerRelation\Entity;

use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;

class Company implements ArraySerializableInterface
{
    protected $companyId;
    public function getCompanyId(){return $this->companyId;}
    public function setCompanyId($value){$this->companyId=$value;}

    protected $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $phone;
    public function getPhone(){return $this->phone;}
    public function setPhone($value){$this->phone=$value;}

    protected $address;
    public function getAddress(){return $this->address;}
    public function setAddress($value){$this->address=$value;}

    protected $website;
    public function getWebsite(){return $this->website;}
    public function setWebsite($value){$this->website=$value;}

    protected $type;
    public function getType(){return $this->type;}
    public function setType($value){$this->type=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    public function exchangeArray(array $data)
    {
        $this->companyId=(!empty($data['companyId']))?$data['companyId']:null;
        $this->name=(!empty($data['name']))?$data['name']:null;
        $this->phone=(!empty($data['phone']))?$data['phone']:null;
        $this->address=(!empty($data['address']))?$data['address']:null;
        $this->website=(!empty($data['website']))?$data['website']:null;
        $this->type=(!empty($data['type']))?$data['type']:null;
        $this->status=(!empty($data['status']))?$data['status']:null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}