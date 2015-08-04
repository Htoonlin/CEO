<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 8/4/2015
 * Time: 1:54 PM
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Development\Controller\Generate' => 'Development\Controller\GenerateController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);