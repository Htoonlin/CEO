<?php
namespace Application\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-09-29 11:06:15
 *
 * @package Application\Entity
 */
class Route implements ArraySerializableInterface
{

    protected $routeId = null;

    protected $name = null;

    protected $route = null;

    protected $module = null;

    protected $controller = null;

    protected $constraints = null;

    protected $isApi = null;

    public function getRouteId()
    {
        return $this->routeId;
    }

    public function setRouteId($value)
    {
        $this->routeId = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($value)
    {
        $this->route = $value;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule($value)
    {
        $this->module = $value;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($value)
    {
        $this->controller = $value;
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

    public function setConstraints($value)
    {
        $this->constraints = $value;
    }

    public function getIsApi()
    {
        return $this->isApi;
    }

    public function setIsApi($value)
    {
        $this->isApi = $value;
    }

    public function exchangeArray(array $data)
    {
        $this->routeId = (!empty($data['routeId'])) ? $data['routeId'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->route = (!empty($data['route'])) ? $data['route'] : null;
        $this->module = (!empty($data['module'])) ? $data['module'] : null;
        $this->controller = (!empty($data['controller'])) ? $data['controller'] : null;
        $this->constraints = (!empty($data['constraints'])) ? $data['constraints'] : null;
        $this->isApi = (!empty($data['isApi'])) ? $data['isApi'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'routeId' => $this->routeId,
            'name' => $this->name,
            'route' => $this->route,
            'module' => $this->module,
            'controller' => $this->controller,
            'constraints' => $this->constraints,
            'isApi' => $this->isApi,
        );
    }


}
