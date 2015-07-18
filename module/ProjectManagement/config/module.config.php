<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 5/25/2015
 * Time: 1:02 PM
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'ProjectManagement\Controller\Project' => 'ProjectManagement\Controller\ProjectController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);