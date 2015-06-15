<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 3/9/2015
 * Time: 10:13 PM
 */

namespace Application\Helper;


use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\View;

class MenuHelper {
    private $form;
    public function getForm(array $urlType)
    {
        if($this->form){
            return $this->form;
        }
        $form=new Form();
        $form->setAttribute('class','form-horizontal');
        $form->setAttribute('role','form');
        $form->add(array(
            'name'=>'menuId',
            'type'=>'Hidden',
        ));
        $form->add(array(
            'name'=>'title',
            'type'=>'text',
            'options'=>array(
                'label'=>'Title',
            ),
            'attributes'=>array(
                'class'=>'form-control',
            ),
        )) ;
        $form->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'options' => array(
                'label' => 'Description',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder'=>'description',
            ),
        ));
        $form->add(array(
            'name'=>'icon',
            'type'=>'text',
            'options'=>array(
                'label'=>'Icon',
            ),
            'attributes'=>array(
                'class'=>'form-control',
                'placeholder'=>'Icon Class',
            ),
        ));
        $form->add(array(
            'name'=>'url',
            'type'=>'text',
            'options'=>array(
                'label'=>'Url',
            ),
            'attributes'=>array(
                'class'=>'form-control',
                'placeholder'=>'Url',
            ),
        ));


        $url_type = new Select('url_type');
        $url_type->setLabel('Type')
            ->setAttribute('class', 'form-control')
            ->setValueOptions($urlType)->setEmptyOption('-- Choose URL Type --');
        $form->add($url_type);

        $hasDivider = new Checkbox('hasDivider');
        $hasDivider->setLabel('Has divider?');
        $form->add($hasDivider);

        $form->add(array(
            'name'=>'parentId',
            'type'=>'hidden',
        ));
        $form->add(array(
            'name' => 'priority',
            'type' => 'number',
            'options' => array(
                'label' => 'Priority',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $form->setInputFilter($this->getInputFilter());
        $this->form=$form;
        return $this->form;
    }
    public function setForm($form)
    {
        $this->form=$form;
    }
    private $inputFilter;
    public function getInputFilter()
    {
    if($this->inputFilter){
        return $this->inputFilter;
    }
        $filter=new InputFilter();
        $filter->add(array(
            'name'=>'menuId',
            'required'=>true,
            'filters'=>array(
                array('name'=>'Int',)
            )
        ));
        $filter->add(array(
            'name'=>'parentId',
            'required'=>false,
            'filters'=>array(
                array('name'=>'Int',)
            ),
        ));
        $filter->add(array(
            'name'=>'title',
            'required'=>true,
            'filters'=>array(
                array('name'=>'StripTags'),
                array('name'=>'StringTrim'),
            ),
            'validators'=>array(
                array(
                    'name'=>'StringLength',
                    'options'=>array(
                        'encoding'=>'UTF-8',
                        'min'=>1,
                        'max'=>55
                    ),
                ),
            ),
        ));

        $filter->add(array(
            'name' => 'url',
            'required' => true,
            'filters'=>array(
                array('name'=>'StripTags'),
                array('name'=>'StringTrim'),
            ),
            'validators'=>array(
                array(
                    'name'=>'StringLength',
                    'options'=>array(
                        'encoding'=>'UTF-8',
                        'min'=>1,
                        'max'=>500
                    ),
                ),
            ),
        ));
        /*
        $filter->add(array(
            'name' => 'icon',
            'required' => true,
        ));*/

        $filter->add(array(
            'name' => 'url_type',
            'required' => true,
        ));
        $this->inputFilter=$filter;
        return $this->inputFilter;

    }
    public function setInputFilter($filter)
    {
        $this->inputFilter=$filter;
    }

}


