<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/17/2015
 * Time: 3:12 PM
 */

namespace Application\Service;

use Zend\Authentication\Storage\Session;

class SundewAuthStorage extends Session
{
    public function setRememberMe($rememberMe = 0, $time = 1209600)
    {
        if($rememberMe == 1)
        {
            $this->session->getManager()->rememberMe($time);
        }
    }

    public function forgetMe()
    {
        $this->session->getManager()->forgetMe();
    }
}