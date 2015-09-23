<?php
namespace Application\Entity;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * System Generated Code
 *
 * User : Htoonlin
 * Date : 2015-09-22 17:56:53
 *
 * @package Application\Entity
 */
class User implements ArraySerializableInterface
{

    protected $userId = null;

    protected $userName = null;

    protected $password = null;

    protected $description = null;

    protected $image = null;

    protected $status = null;

    protected $lastLogin = null;

    protected $tokenKey = null;

    protected $userRole = null;

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($value)
    {
        $this->userId = $value;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($value)
    {
        $this->userName = $value;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($value)
    {
        $this->image = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin($value)
    {
        $this->lastLogin = $value;
    }

    public function getTokenKey()
    {
        return $this->tokenKey;
    }

    public function setTokenKey($value)
    {
        $this->tokenKey = $value;
    }

    public function getUserRole()
    {
        return $this->userRole;
    }

    public function setUserRole($value)
    {
        $this->userRole = $value;
    }

    public function exchangeArray(array $data)
    {
        $this->userId = (!empty($data['userId'])) ? $data['userId'] : null;
        $this->userName = (!empty($data['userName'])) ? $data['userName'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->image = (!empty($data['image'])) ? $data['image'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->lastLogin = (!empty($data['lastLogin'])) ? $data['lastLogin'] : date('Y-m-d H:i:s', time());
        $this->tokenKey = (!empty($data['tokenKey'])) ? $data['tokenKey'] : null;
        $this->userRole = (!empty($data['userRole'])) ? $data['userRole'] : null;
    }

    public function getArrayCopy()
    {
        return array(
            'userId' => $this->userId,
            'userName' => $this->userName,
            'password' => $this->password,
            'description' => $this->description,
            'image' => $this->image,
            'status' => $this->status,
            'lastLogin' => $this->lastLogin,
            'tokenKey' => $this->tokenKey,
            'userRole' => $this->userRole,
        );
    }


}
