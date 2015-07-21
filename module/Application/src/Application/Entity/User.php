<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 2/16/2015
 * Time: 5:11 PM
 */

namespace Application\Entity;

use Zend\Stdlib\ArraySerializableInterface;
use Zend\Form\Annotation as Form;

/**
 * Class User
 * @package Application\Entity
 * @Annotation\Name("user")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class User implements ArraySerializableInterface
{
    protected $userId;
    public function getUserId(){ return $this->userId; }
    public function setUserId($value){ $this->userId = $value; }

    protected $userName;
    public function getUserName(){ return $this->userName; }
    public function setUserName($value){ $this->userName = $value; }

    protected $password;
    public function getPassword(){ return $this->password; }
    public function setPassword($value){ $this->password = $value; }

    protected $description;
    public function getDescription(){ return $this->description; }
    public function setDescription($value){ $this->description = $value; }

    protected $image;
    public function getImage(){ return $this->image;}
    public function setImage($value){ $this->image = $value; }

    protected $status;
    public function getStatus(){ return $this->status; }
    public function setStatus($value){ $this->status = $value; }

    protected $lastLogin;
    public function getLastLogin(){ return $this->lastLogin; }
    public function setLastLogin($value){ $this->lastLogin = $value; }

    public function exchangeArray(array $data)
    {
        $this->userId = (!empty($data['userId'])) ? $data['userId'] : null;
        $this->userName = (!empty($data['userName'])) ? $data['userName'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->image = (!empty($data['image'])) ? $data['image'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : 'A';
        $this->lastLogin = (!empty($data['lastLogin'])) ? $data['lastLogin'] : date('Y-m-d H:i:s', time());
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}