<?php

namespace Api\Model;

class UserManager extends AbstractManager
{

    /**
     * connect to table
     * 
     * @var string
     */
    protected $name = 'users';

    /**
     * Find user by username and password
     * 
     * @param string $username
     * @param string $password
     * @return stdClass
     */
    public function findUser($username, $password)
    {
        $user = $this->table
                ->where("username", "=", $username)
                ->where("password", "=", md5($password))
                ->first();
        return $user;
    }

}
