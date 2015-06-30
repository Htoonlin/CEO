<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/18/2015
 * Time: 4:10 PM
 */

namespace HumanResource\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LeaveHelper
{
    protected $form;
    public function getForm(array $staffList, array $statusList, array $leaveList, $formType = 'W'){
        if(!$this->form){
            $leaveId = new Element\Hidden('leaveId');

            $staff = new Element\Select();
            $staff->setName('staffId')
                ->setEmptyOption('-- Choose Staff --')
                ->setLabel('Staff')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($staffList);

            $leaveType = new Element\Select();
            $leaveType->setName('leaveType')
                ->setLabel('Type')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($leaveList);

            $date = new Element\Date();
            $date->setName('date')
                ->setLabel('Date')
                ->setAttributes(array(
                    'allowPastDates' => true,
                    'momentConfig' => array(
                        'format' => 'YYYY-MM-DD',
                    ),
                ));

            $description = new Element\Textarea('description');
            $description->setLabel('Description')
                ->setAttribute('class', 'form-control');

            $status=new Element\Select();
            $status->setName('status')
                ->setLabel('Status')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($statusList);

            if($formType == 'R' || $formType == 'V'){
                $staff->setAttribute('disabled', 'disabled');
                $leaveType->setAttribute('disabled', 'disabled');
                $description->setAttribute('readonly', 'readonly');
            }

            if($formType == 'V'){
                $status->setAttribute('disabled', 'disabled');
                $date = new Element\Text();
                $date->setName('date')
                    ->setLabel('Date')
                    ->setattributes(array(
                        'class' => 'form-control',
                        'disabled' => 'disabled'
                    ));
            }

            $form = new Form();
            $form->setAttribute('class', 'form-horizontal');
            $form->add($leaveId);
            $form->add($staff);
            $form->add($leaveType);
            $form->add($date);
            $form->add($description);
            $form->add($status);

            $this->form = $form;
        }

        return $this->form;
    }

    public function setForm($value){
        $this->form = $value;
    }

    protected  $inputFilter;
    public function getInputFilter()
    {
        if(!$this->inputFilter){
            $filter = new InputFilter();

            $filter->add(array(
                'name' => 'leaveId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                )
            ));


            $filter->add(array(
                'name' => 'staffId',
                'required' => true,
                'filters' => array('name'=>'Int')
            ));

            $filter->add(array(
                'name' => 'leaveType',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'description',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'max' => 500,
                        'min' => 1,
                        'encoding' => 'UTF-8',
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'status',
                'required' => true,
            ));

            $this->inputFilter = $filter;
        }

        return $this->inputFilter;
    }
    public function setInputFilter($value)
    {
        $this->inputFilter = $value;
    }
}