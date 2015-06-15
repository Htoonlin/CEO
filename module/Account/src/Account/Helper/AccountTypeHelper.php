<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/15/2015
 * Time: 9:54 PM
 */

namespace Account\Helper;

use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class AccountTypeHelper {

    private $form;
    public function getForm(array $types, array $defaultStatus)
    {
        if($this->form){
            return $this->form;
        }

        $form = new Form();
        $form->setAttribute('class', 'form-horizontal');
        $form->setAttribute('accountType','form');
        $form->add(array(
            'name' => 'accountTypeId',
            'type' => 'Hidden',
        ));
        $form->add(array(
            'name' => 'code',
            'type' => 'text',
            'options' => array(
                'label' => 'Code',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $form->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));

        $form->add(array(
            'name' => 'parentTypeId',
            'type' => 'Hidden',
        ));

        $baseType = new Select();
        $baseType->setName('baseType')
            ->setLabel('Base Type')
            ->setAttribute('class', 'form-control')
            ->setEmptyOption('-- Choose Base Type --')
            ->setValueOptions($types);
        $form->add($baseType);

        $status=new Select();
        $status->setName('status')
            ->setLabel('Status')
            ->setAttribute('class', 'form-control')
            ->setValueOptions($defaultStatus);
        $form->add($status);

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
            'name' => 'accountTypeId',
            'required' => true,
            'filters' => array(
                array('name' => 'Int'),
            ),
        ));

        $filter->add(array(
            'name' => 'code',
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
                        'max' => 50
                    ),
                ),
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
        $filter->add(array(
            'name' => 'baseType',
            'required' => true,
        ));

        $filter->add(array(
            'name' => 'status',
            'required' => true,
        ));

        $this->inputFilter = $filter;

        return $this->inputFilter;
    }
    public function setInputFilter($filter)
    {
        $this->inputFilter = $filter;
    }


}