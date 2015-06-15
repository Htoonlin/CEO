<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/22/2015
 * Time: 1:48 PM
 */

namespace Application\Helper;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class RoleHelper
{
    private $form;
    public function getForm()
    {
        if($this->form){
            return $this->form;
        }

        $form = new Form();
        $form->setAttribute('class', 'form-horizontal');
        $form->setAttribute('role','form');
        $form->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $form->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Title',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $form->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Description',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $form->add(array(
            'name' => 'icon',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Icon Class',
            ),
        ));
        $form->add(array(
            'name' => 'parentId',
            'type' => 'Hidden',
        ));
        $form->add(array(
            'name' => 'priority',
            'type' => 'number',
            'options' => array(
                'label' => 'Priority',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Choose priority',
            ),
        ));

        $form->setInputFilter($this->getInputFilter());

        $this->form = $form;

        return $this->form;
    }
    public function setForm($form)
    {
        $this->form = $form;
    }

    private $inputFilter;
    public function getInputFilter()
    {
        if($this->inputFilter){
            return $this->inputFilter;
        }

        $filter = new InputFilter();

        $filter->add(array(
            'name' => 'id',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $filter->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 255
                    ),
                ),
            ),
        ));

        $this->inputFilter = $filter;

        return $this->inputFilter;
    }
    public function setInputFilter($filter)
    {
        $this->inputFilter = $filter;
    }
}