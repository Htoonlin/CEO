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
            'Account\Controller\AccountType' => 'Account\Controller\AccountTypeController',
            'Account\Controller\Receivable' => 'Account\Controller\ReceivableController',
            'Account\Controller\Payable' => 'Account\Controller\PayableController',
            'Account\Controller\Voucher' => 'Account\Controller\VoucherController',
            'Account\Controller\Currency' => 'Account\Controller\CurrencyController',
            'Account\Controller\Balance' => 'Account\Controller\BalanceController',
            'Account\Controller\Report' => 'Account\Controller\ReportController',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),

    ),
);