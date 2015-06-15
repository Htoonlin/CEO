<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/10/2015
 * Time: 10:11 AM
 */

namespace HumanResource\Helper;


use Zend\Form\Element\DateSelect;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class HolidayHelper
{
    protected $form;
    public function getForm(array $holidayType)
    {
        if(!$this->form){
            $hidId = new Hidden();
            $hidId->setName('calendarId');

            $cboType = new Select();
            $cboType->setName('type')
                ->setLabel('Type')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption('-- Choose Types --')
                ->setValueOptions($holidayType);

            $maxYear = date('Y', time()) + 10;
            $datePicker = new DateSelect('date');
            $datePicker->setValue(new \DateTime('now'))
                ->setShouldRenderDelimiters(false)
                ->setMinYear(2011)
                ->setMaxYear($maxYear)
                ->setLabel('Date')
                ->setDayAttributes(array('class'=>'date-control', 'id' => 'dayCombo'))
                ->setMonthAttributes(array('class'=>'date-control', 'id' => 'monthCombo'))
                ->setYearAttributes(array('class'=>'date-control', 'id' => 'yearCombo'));

            $txtTitle = new Text();
            $txtTitle->setName('title')
                ->setLabel('Title')
                ->setAttribute('class', 'form-control')
                ->setAttribute('placeholder', 'Title');

            $form = new Form();
            $form->setAttributes(array('class' => 'form-horizontal','role'=>'form'));
            $form->add($hidId);
            $form->add($cboType);
            $form->add($datePicker);
            $form->add($txtTitle);
            $this->form = $form;
        }

        return $this->form;
    }
    public function setForm($value)
    {
        $this->form = $value;
    }

    protected $inputFilter;
    public function getInputFilter()
    {
        if(!$this->inputFilter){
            $filter = new InputFilter();
            $filter->add(
                array(
                    'name' => 'calendarId',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $filter->add(array(
                'name' => 'title',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'max' => 255,
                        'min' => 1,
                        'encoding' => 'UTF-8',
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'type',
                'required' => false,
            ));

            $filter->add(array(
                'name' => 'dateSelect',
                'required' => false,
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