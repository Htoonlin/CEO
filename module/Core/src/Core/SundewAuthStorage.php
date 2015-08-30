<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 3/17/2015
 * Time: 3:12 PM
 */

namespace Core;

use Zend\Authentication\Storage\Session;
use Zend\Cache\StorageFactory;

class SundewAuthStorage extends Session
{
    /**
     * @param int $rememberMe
     * @param int $time
     */
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

    protected $remove_caches = array('menu_cache_', 'route_cache_');
    /**
     * Defined by Zend\Authentication\Storage\StorageInterface
     *
     * @return void
     */
    public function cacheClear()
    {
        $userId = $this->session->{$this->member}->userId;
        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'cache_dir' => './data/cache',
                    'ttl' => 3600,
                )
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => false),
                'serializer',
            ),
        ));

        foreach($this->remove_caches as $cache_ns){
            $cache->removeItem($cache_ns . $userId);
        }
    }

}