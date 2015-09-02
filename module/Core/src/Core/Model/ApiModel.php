<?php

/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 07:38 PM
 */
namespace Core\Model;

use Zend\InputFilter\InputFilter;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Model\JsonModel;

/**
 * Class ApiModel
 * @package Core\Model
 */
class ApiModel extends JsonModel
{
    /**
     * @param null $data
     * @param null $values
     */
    function __construct($data = null, $values = null){
        if($data != null){
            $this->setResponseData($data);
        }

        parent::__construct(null, $values);
    }

    /**
     * @var
     */
    protected $responseData;

    /**
     * @var bool
     */
    protected $ajaxOnly = false;
    /**
     * @var int
     */
    protected $statusCode = 200;
    /**
     * @var InputFilter
     */
    protected $inputFilter;
    /**
     * @var array
     */
    protected $allowMethods = ['GET' , 'POST'];

    /**
     * @var bool
     */
    protected $allowFlashRequest = false;

    protected $allowMediaTypes = ['application/json'];#,'application/x-www-form-urlencoded'];
    public function getAllowMediaTypes(){
        return $this->allowMediaTypes;
    }

    public function setAllowMediaTypes(array $types){
        if(!empty($types)){
            $this->allowMediaTypes = $types;
        }
    }

    /**
     * @return bool
     */
    public function getAllowFlashRequest(){
        return $this->allowFlashRequest;
    }

    /**
     * @param $allow
     */
    public function setAllowFlashRequest($allow){
        $this->allowFlashRequest = $allow;
    }

    /**
     * @param $is
     */
    public function setAjaxOnly($is){
        $this->ajaxOnly = $is;
    }

    /**
     * @return bool
     */
    public function isAjaxOnly(){
        return $this->ajaxOnly;
    }

    /**
     * @param $data
     */
    public function setResponseData($data){
        $this->responseData = $data;
    }

    /**
     * @return mixed
     */
    public function getResponseData(){
        return $this->responseData;
    }

    /**
     * @param $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @var
     */
    protected $statusMessage;

    /**
     * @param $message
     */
    public function setStatusMessage($message){
        $this->statusMessage = $message;
    }

    /**
     * @return mixed
     */
    public function getStatusMessage(){
        return $this->statusMessage;
    }

    /**
     * @param InputFilter $filter
     */
    public function setInputFilter(InputFilter $filter){
        $this->inputFilter = $filter;
    }

    /**
     * @return InputFilter
     */
    public function getInputFilter(){
        return $this->inputFilter;
    }

    /**
     * @param array $allowMethods
     */
    public function setAllowMethods(array $allowMethods){
        if(!empty($allowMethods)){
            $this->allowMethods = $allowMethods;
        }
    }

    /**
     * @return array
     */
    public function getAllowMethods(){
        return $this->allowMethods;
    }
}