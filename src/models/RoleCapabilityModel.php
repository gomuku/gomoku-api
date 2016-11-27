<?php

namespace Api\Model;

use Api\Lib\SingletonTrait;
use Api\Model\CapabilityModel;

class RoleCapabilityModel extends AbstractModel
{

    use SingletonTrait;

    /**
     * connect to table
     * 
     * @var string
     */
    protected $name = 'roles_capabilities';

    /**
     * 
     * @param type $roleId
     * @param type $allowed
     * @return type
     */
    public function getScopes($roleId, $allowed = true)
    {
        $scopes = [];
        $data   = $this->table()->where('role_id', '=', $roleId)
                ->where('allowed', '=', $allowed)
                ->get();
        foreach ($data as $cap) {
            // get capability name
            $capName = $this->model('Capability', 
                function(CapabilityModel $model) use($cap) {
                    return $model->getCapabilityName($cap->capability_id);
                }
            );
            if ($capName) {
                $scopes[] = $capName;
            }
        }
        return $scopes;
    }

}
