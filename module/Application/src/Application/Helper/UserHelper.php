<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/3/2015
 * Time: 5:40 PM
 */

namespace Application\Helper;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\IsImage;

class UserHelper
{
    protected $dbAdapter;
    public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    protected $form;
    public function getForm(array $roles, array $default_status)
    {
        if(!$this->form) {
            $hidId = new Element\Hidden();
            $hidId->setName('userId');

            $txtName = new Element\Text();
            $txtName->setLabel('User Name')
                ->setName("userName")
                ->setAttribute('class', 'form-control');

            $password = new Element\Password();
            $password->setLabel('Password')
                ->setName('password')
                ->setAttribute('class', 'form-control');

            $confirmPassword = new Element\Password();
            $confirmPassword->setName('confirmPassword')
                ->setLabel('Retype Password')
                ->setAttribute('class', 'form-control');

            $selectRole = new Element\Select();
            $selectRole->setName('userRole')
                ->setLabel('Role')
                ->setAttribute('class', 'form-control')
                ->setEmptyOption("-- Choose Role --")
                ->setValueOptions($roles);

            $description = new Element\Textarea();
            $description->setName('description')
                ->setLabel('Description')
                ->setAttribute('class', 'form-control');

            $status = new Element\Select();
            $status->setName('status')
                ->setLabel('Status')
                ->setAttribute('class', 'form-control')
                ->setValueOptions($default_status);

            $image = new Element\File();
            $image->setName('image')
                ->setLabel('Profile image');

            $form = new Form();
            $form->setAttribute('class', 'form-horizontal');
            $form->setAttribute('enctype', 'multipart/form-data');
            $form->add($hidId);
            $form->add($txtName);
            $form->add($password);
            $form->add($confirmPassword);
            $form->add($selectRole);
            $form->add($description);
            $form->add($status);
            $form->add($image);
            $this->form = $form;
        }

        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    protected $inputFilter;
    public function getInputFilter($userId = 0, $userName = "")
    {
        if(!$this->inputFilter) {
            $filter = new InputFilter();
            $filter->add(array(
                'name' => 'userId',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                )
            ));

            $filter->add(array(
                'name' => 'userName',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 200,
                            'min' => 1,
                            'encoding' => 'UTF-8',
                        ),
                    ),
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'tbl_user',
                            'field' => 'userName',
                            'adapter' => $this->dbAdapter,
                            'exclude' => array(
                                'field' => 'userId',
                                'value' => $userId
                            ),
                            'message' => 'This user name is already exist.',
                        )
                    ),
                ),
            ));

            $filter->add(array(
                'name' => 'password',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'max' => 50,
                        ),
                    ),
                ),
            ));
            $filter->add(array(
                'name' => 'confirmPassword',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        )
                    ),
                ),
            ));
            $filter->add(array(
               'name' => 'userRole',
                'required' => true,
            ));

            $fileInput = new FileInput('image');
            $fileInput->setRequired(false);

            $fileInput->getValidatorChain()
                ->attachByName('filesize', array('max' => 204800))
                ->attachByName('fileimagesize', array('maxWidth' => 256, 'maxHeight' => 256))
                ->attach(new IsImage());
            //->attachByName('filemimetype', array('mimetype' => ''))

            $fileInput->getFilterChain()->attachByName(
                'filerenameupload',
                array(
                    'target' => sprintf('./data/uploads/avatar/%s', $userName),
                    'use_upload_extension' => true,
                    'overwrite' => true,
                )
            );

            $filter->add($fileInput);

            $this->inputFilter = $filter;
        }
        return $this->inputFilter;
    }

    public function setInputFilter($filter)
    {
        $this->inputFilter = $filter;
    }
}