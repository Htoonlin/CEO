<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/24/2015
 * Time: 6:18 PM
 */

namespace Application\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class DashboardHelper {
    public function getLeaveForm(array $leaveList){
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

        $form = new Form();
        $form->setAttribute('class', 'form');
        $form->add($leaveType);
        $form->add($date);
        $form->add($description);

        return $form;
    }

    public function getLeaveFilter(){
        $filter = new InputFilter();

        $filter->add(array(
            'name' => 'leaveType',
            'required' => true,
        ));

        $filter->add(array(
            'name' => 'description',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrims'),
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

        return $filter;
    }
}