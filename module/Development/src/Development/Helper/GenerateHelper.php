<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 2:07 PM
 */

namespace Development\Helper;


use Zend\Form\Element;
use Zend\Form\Form;

class GenerateHelper
{
    protected $form;
    public function getForm($tableList){

        if(!$this->form){
            $form = new Form();

            $tableList = new Element\Select('tbl_name');
            $tableList->setLabel('Table')
                ->setAttributes(array('class' => 'form-control'))
                ->setEmptyOption("--Choose Table--");

            $form->add($tableList);

            $this->form = $form;
        }

        return $this->form;
    }
}