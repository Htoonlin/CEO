<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/16/2015
 * Time: 3:06 PM
 */

namespace Application\Helper;


use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class PasswordHelper {
    protected $form;
    public function getForm()
    {
        if(!$this->form){
            $currentPassword = new Element\Password();
            $currentPassword->setName('currentPassword')
                ->setLabel('Old password')
                ->setAttributes(array('class' => 'form-control'));

            $password = new Element\Password();
            $password->setName('password')
                ->setLabel('New password')
                ->setAttributes(array('class' => 'form-control'));

            $retypePassword = new Element\Password();
            $retypePassword->setName('retypePassword')
                ->setLabel('Retype password')
                ->setAttributes(array('class' => 'form-control'));

            $form = new Form();
            $form->setAttributes(array('class'=>'form-horizontal', 'role' => 'form'));
            $form->add($currentPassword);
            $form->add($password);
            $form->add($retypePassword);
            $this->form = $form;
        }

        return $this->form;
    }
    public function setForm($value){$this->form = $value;}

    protected $inputFilter;
    public function getInputFilter(){
        if(!$this->inputFilter){
            $filter = new InputFilter();

            $filter->add(array(
                'name' => 'currentPassword',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                        ),
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                        ),
                    ),
                ),
            ));
            $filter->add(array(
                'name' => 'retypePassword',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        )
                    ),
                ),
            ));
            $this->inputFilter = $filter;
        }

        return $this->inputFilter;
    }
    public function setInputFilter($value){$this->inputFilter = $value;}
}