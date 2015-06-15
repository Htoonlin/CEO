<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 4/24/2015
 * Time: 11:38 AM
 */


return array(
    'controllers' => array(
        'invokables' => array(
            'CustomerRelation\Controller\Proposal'=>'CustomerRelation\Controller\ProposalController',
            'CustomerRelation\Controller\Contract'=>'CustomerRelation\Controller\ContractController',
            'CustomerRelation\Controller\Company'=>'CustomerRelation\Controller\CompanyController',
            'CustomerRelation\Controller\Contact'=>'CustomerRelation\Controller\ContactController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);