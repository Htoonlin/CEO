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
            $month = new Element\MonthSelect('month');
            $month->setLabel('Month :')
                ->setMinYear(2011)
                ->setMaxYear(date('Y', time()))
                ->setMonthAttributes(array('class' => 'form-control'))
                ->setYearAttributes(array('class' => 'form-control'));

            $form = new Form();
            $form->add($month);
        }

        return $this->form;
    }
}