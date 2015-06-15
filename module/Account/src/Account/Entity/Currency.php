<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/23/2015
 * Time: 4:26 PM
 */

namespace Account\Entity;


use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;

class Currency implements ArraySerializableInterface
{
    protected  $currencyId;
    public  function getCurrencyId(){return $this->currencyId;}
    public function setCurrencyId($value){$this->currencyId=$value;}

    protected  $code;
    public function getCode(){return $this->code;}
    public function setCode($value){$this->code=$value;}

    protected  $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    protected $rate;
    public function getRate(){return $this->rate;}
    public function setRate($value){ $this->rate=$value;}

    protected $status;
    public function getStatus(){return $this->status;}
    public function setStatus($value){$this->status=$value;}

    protected $entryDate;
    public function getEntryDate(){return $this->entryDate;}
    public function setEntryDate($value){ $this->entryDate=$value;}


    public function exchangeArray(array $data)
    {
        $this->currencyId=(!empty($data['currencyId']))?$data['currencyId']:null;
        $this->code=(!empty($data['code']))?$data['code']:null;
        $this->name=(!empty($data['name']))?$data['name']:null;
        $this->rate=(!empty($data['rate']))?$data['rate']:null;
        $this->status=(!empty($data['status']))?$data['status']:'A';
        $this->entryDate=(!empty($data['entryDate']))?$data['entryDate']:date('Y-m-d h:i:s', time());

    }

    public function getArrayCopy()
    {
       return get_object_vars($this);
    }

    public  function getLabel()
    {
        return ucfirst($this->name);
    }

    public function getUrl()
    {
        return "/account/currency/index/".$this->currencyId;
    }

    public function getValue()
    {
        return $this->currencyId;
    }
}


