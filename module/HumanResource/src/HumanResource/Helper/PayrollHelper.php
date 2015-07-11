<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 6/29/2015
 * Time: 2:04 PM
 */

namespace HumanResource\Helper;

use Zend\Form\Element;
use Zend\Form\Form;

class PayrollHelper
{
    protected $form;
    public function getForm()
    {
        if(!$this->form){
            $fromDate = new Element\Date('fromDate');
            $fromDate->setAttributes(array(
                'allowPastDates' => true,
                'style' => 'width:120px;',
                'momentConfig' => array('format' => 'YYYY-MM-DD'),
            ));
            #$fromDate->setValue(date('Y-m-d', strtotime('-1 day')));
            $fromDate->setValue(date('Y-m-26', strtotime('-1 month')));

            $toDate = new Element\Date('toDate');
            $toDate->setAttributes(array(
                'allowPastDates' => true,
                'style' => 'width:120px;margin-left:5px;',
                'momentConfig' => array('format' => 'YYYY-MM-DD'),
            ));
            #$toDate->setValue(date('Y-m-d', strtotime('-1 day')));
            $toDate->setValue(date('Y-m-25', time('')));

            $formula = new Element\Text('formula');
            $formula->setAttributes(array(
                'class' => 'form-control',
                'style' => 'width:450px;margin-left:5px;',
                'placeholder' => 'Payroll Formula',
            ));

            $form = new Form();
            $form->setAttributes(array(
                'class' => 'form-inline',
                'role' => 'form',
            ));
            $form->add($fromDate);
            $form->add($toDate);
            $form->add($formula);

            $this->form = $form;
        }

        return $this->form;
    }
}