<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/24/2015
 * Time: 1:29 PM
 */

namespace Application\Helper;

use Zend\Form\Annotation;

/**
 * Class ConstantForm
 * @package Application\Helper
 * @Annotation\Name("constant")
 */
class ConstantForm {
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "Int"})
     */
    public $constantId;
    public function getConstantId(){return $this->constantId;}
    public function setConstantId($value){$this->constantId=$value;}

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required": "true"})
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Validator({"name": "StringLength", "options" : {"max" : 200}})
     * @Annotation\Options({"label": "Name"})
     */
    public $name;
    public function getName(){return $this->name;}
    public function setName($value){$this->name=$value;}

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Attributes({"class" : "form-control"})
     * @Annotation\Validator({"name" : "StringLength", "options": {"max": 200}})
     * @Annotation\Options({"label" : "Value"})
     */
    public $value;
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value=$value;}

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class" : "form-control"})
     * @Annotation\Options({"label" : "Group Code"})
     */
    public $groupCode;
    public function getgroupCode(){return $this->groupCode;}
    public function setgroupCode($value){$this->groupCode=$value;}

}