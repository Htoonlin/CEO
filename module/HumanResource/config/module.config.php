<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'HumanResource\Controller\Position'     => 'HumanResource\Controller\PositionController',
            'HumanResource\Controller\Staff'        => 'HumanResource\Controller\StaffController',
            'HumanResource\Controller\Department'   => 'HumanResource\Controller\DepartmentController',
            'HumanResource\Controller\Attendance'   => 'HumanResource\Controller\AttendanceController',
            'HumanResource\Controller\Holiday'      => 'HumanResource\Controller\HolidayController',
            'HumanResource\Controller\Payroll'      => 'HumanResource\Controller\PayrollController',
            'HumanResource\Controller\Leave'        => 'HumanResource\Controller\LeaveController',
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
