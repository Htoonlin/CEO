<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/22/2015
 * Time: 1:02 PM
 */

namespace Application\Helper\Entity;

interface TreeViewEntityInterface
{
    public function getChildren();
    public function hasChildren();
    public function getIconClass();
    public function getLabel();
    public function getUrl();
    public function getValue();
}