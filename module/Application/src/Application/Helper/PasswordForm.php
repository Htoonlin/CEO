<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/24/2015
 * Time: 11:10 AM
 */

namespace Application\Helper;

use Zend\Form\Annotation;

/**
 * Class PasswordHelper
 * @package Application\Helper
 * @Annotation\Name("password")
 */
class PasswordForm
{
    /**
     * @Annotation\Attributes({"type": "password", "class": "form-control"})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name": "StringLength", "options" : {"min" : 4, "max" : 50}})
     * @Annotation\Options({"label":"Old password"})
     */
    public $currentPassword;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name": "StringLength", "options" : {"min" : 4, "max" : 50}})
     * @Annotation\Options({"label":"New password"})
     */
    public $password;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Attributes({"class": "form-control"})
     * @Annotation\Required({"required" : "true"})
     * @Annotation\Filter({"name" : "StripTags"})
     * @Annotation\Filter({"name" : "StringTrim"})
     * @Annotation\Validator({"name": "StringLength", "options" : {"min" : 4, "max" : 50}})
     * @Annotation\Validator({"name": "Identical", "options" : {"token": "password"}})
     * @Annotation\Options({"label":"Retype password"})
     */
    public $retypePassword;
}