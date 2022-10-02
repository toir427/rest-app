<?php

namespace common\rbac;

use common\models\User;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params
 */
class UpdateCompanyRule extends Rule
{
    public $name = 'update_company';

    /**
     * @param User $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return array_key_exists('user', $params) && $params['user'] == $user;
    }
}