<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/24/2015
 * Time: 5:09 PM
 */

namespace Application\Helper;

use Zend\Form\Annotation;

/**
 * Class RouteForm
 * @package Application\Helper
 * @Annotation\Name("route")
 */
class RouteForm
{
    /**
     * @Annotation\Type("Zend\Form\Element\Hidden")
     * @Annotation\Filter({"name": "Int"})
     */
    public $routeId;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":255}})
     * @Annotation\Options({"label":"Name"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":255}})
     * @Annotation\Options({"label":"Route"})
     */
    public $route;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":255}})
     * @Annotation\Options({"label":"Module"})
     */
    public $module;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":255}})
     * @Annotation\Options({"label":"Controller"})
     */
    public $controller;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":1, "max":255}})
     * @Annotation\Options({"label":"Constraints"})
     */
    public $constraints;
    /**
     * @Annotation\Type("Zend
     */
}