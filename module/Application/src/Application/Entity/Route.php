<?php
/**
 * Created by PhpStorm.
 * User: linn
 * Date: 3/18/2015
 * Time: 4:43 PM
 */

namespace Application\Entity;


use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;


class Route implements ArraySerializableInterface
{
    protected  $routeId;
    public function getRouteId() {return $this->routeId;}
    public function setRouteId($value) {$this->routeId=$value;}

    protected  $name;
    public function getName() {return $this->name;}
    public function setName($value) {$this->name=$value;}

    protected  $route;
    public function getRoute() {return $this->route;}
    public function setRoute($value) {$this->route=$value;}

    protected  $module;
    public function getModule() {return $this->module;}
    public function setModule($value) {$this->module=$value;}

    protected  $controller;
    public function getController() {return $this->controller;}
    public function setController($value) {$this->controller=$value;}

    protected  $constraints;
    public function getConstraints() {return $this->constraints;}
    public function setConstraints($value) { $this->constraints=$value;}



    public function exchangeArray(array $data)
    {
        $this->routeId = (!empty($data['routeId'])) ? $data['routeId'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->route = (!empty($data['route'])) ? $data['route'] : null;
        $this->module = (!empty($data['module'])) ? $data['module'] : null;
        $this->controller = (!empty($data['controller'])) ? $data['controller'] : null;
        $this->constraints = (!empty($data['constraints'])) ? $data['constraints'] : null;
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}