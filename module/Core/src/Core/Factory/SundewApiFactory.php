<?php
/**
 * Created by PhpStorm.
 * User: Htoonlin
 * Date: 2015-08-30
 * Time: 08:18 PM
 */

namespace Core\Factory;


use Core\Model\SundewApiRenderer;
use Core\Model\SundewApiStrategy;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\ApiProblem\View\ApiProblemRenderer;

class SundewApiFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $viewRenderer = new SundewApiRenderer(new ApiProblemRenderer());
        return new SundewApiStrategy($viewRenderer);
    }
}