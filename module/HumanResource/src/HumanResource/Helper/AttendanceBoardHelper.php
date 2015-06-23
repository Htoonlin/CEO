<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/8/2015
 * Time: 1:25 PM
 */

namespace HumanResource\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class AttendanceBoardHelper
{
    protected $form;
    public function getForm(array $staff)
    {
        if(!$this->form)
        {
            $attendanceId = new Element\Hidden();
            $attendanceId->setName('attendanceId');

            $staffId = new Element\Select();
            $staffId->setName('staffId')
                ->setLabel('Staff')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption('-- Choose --')
                ->setValueOptions($staff);

            $type = new Element\Select();
            $type->setName('type')
                ->setLabel('Type')
                ->setAttribute('class', 'form-control')
                ->setValueOptions(array('I' => 'In', 'O' => 'Out'));

            $date = new Element\Date();
            $date->setName('attendanceDate')
                ->setLabel('Date')
                ->setAttributes(
                    array(
                        'class' => 'form-control',
                        'allowPastDates' => true,
                        'momentConfig' => array(
                            'format' => 'YYYY-MM-DD'
                        )
                    )
                );

            $workingHours = array();
            for($i = 8; $i < 20; $i++)
            {
                $workingHours[$i] = sprintf('%02d', $i);
            }

            $hour = new Element\Select();
            $hour->setName('hour')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($workingHours);

            $workingMinutes = array();
            for($i = 0; $i < 12; $i++)
            {
                $workingMinutes[$i] = sprintf('%02d', $i * 5);
            }
            $minute = new Element\Select();
            $minute->setName('minute')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($workingMinutes);

            $form = new Form();
            $form->setAttributes(array('role' => 'form',
                'class' => 'form-horizontal'));
            $form->add($attendanceId);
            $form->add($staffId);
            $form->add($type);
            $form->add($date);
            $form->add($hour);
            $form->add($minute);

            $this->form = $form;
        }

        return $this->form;
    }
    public function setForm($form)
    {
        $this->form = $form;
    }

    protected $inputFilter;
    public function getInputFilter()
    {
        if(!$this->inputFilter)
        {
            $filter = new InputFilter();
            $filter->add(
                array(
                    'name' => 'attendanceId',
                    'required' => true,
                )
            );

            $filter->add(array(
                'name' => 'staffId',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'type',
                'required' => false,
            ));

            $filter->add(array(
                'name' => 'attendanceDate',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'hour',
                'required' => true,
            ));

            $filter->add(array(
                'name' => 'minute',
                'required' => true,
            ));

            $this->inputFilter = $filter;
        }

        return $this->inputFilter;
    }
}