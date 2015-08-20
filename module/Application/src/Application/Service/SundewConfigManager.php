<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-20
 * Time: 01:24 PM
 */

namespace Application\Service;


use Zend\Config\Writer\PhpArray;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SundewConfigManager implements FactoryInterface
{
    protected $public = array();
    protected $config;
    public function createService(ServiceLocatorInterface $serviceLocator){
        $this->config = $serviceLocator->get('Config');
        return $this;
    }

    public function getVersion()
    {
        $version = isset($this->config['version']) ? $this->config['version'] : array();
        $label = '0.0.0';

        if(!empty($version)){
            $major = isset($version['major']) ? $version['major'] : 0;
            $minor = isset($version['minor']) ? $version['minor'] : 0;
            $build = isset($version['build']) ? $version['build'] : 0;

            $label = "{$major}.{$minor}.{$build}";
        }

        return $label;
    }

    public function get($name = '')
    {
        if(empty($name)){
            return $this->config;
        }
        return isset($this->config[$name]) ? $this->config[$name] : array();
    }

    public function setDbConfig($dsn = '', $user = '', $password = '')
    {
        $dbConfig = $this->get('db');

        if(!empty($dsn)){
            $dbConfig['dsn'] = $dsn;
        }
        if(!empty($user)){
            $dbConfig['username'] = $user;
        }
        if(!empty($password)){
            $dbConfig['password'] = $password;
        }

        $writer = new PhpArray();
        $writer->toFile(APP_PATH . '/config/autoload/database.php', array('db' => $dbConfig));
        return $dbConfig;
    }
}