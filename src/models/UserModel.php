<?php

namespace Api\Model;

use Api\Lib\SingletonTrait;
use Api\Model\RoleCapabilityModel;

class UserModel extends AbstractModel
{

    use SingletonTrait;
    
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
        $user = $this->table()
                ->where("username", "=", $username)
                ->where("password", "=", md5($password))
                ->first();
        return $user;
    }

    /**
     * Get scope that user can
     * 
     * @param string $userId
     * @param array $scopes list require scope
     * @return array list scopes that user can
     */
    public function getScopes($userId, $scopes = [])
    {
        $user = $this->table()->find($userId);
        if (!$user) {
            return [];
        }

        $dbScopes = $this->model( 'RoleCapability', 
            function(RoleCapabilityModel $model) use($user) {
                return $model->getScopes($user->role_id);
            }
        );
        if (empty($scopes)) {
            return $dbScopes;
        }
        $results = [];
        foreach ($scopes as $scope) {
            if (in_array($scope, $dbScopes)) {
                $results[] = $scope;
            }
        }
        return $results;
    }

}
