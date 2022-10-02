<?php

namespace frontend\components;

use common\models\User;

class JwtValidationData extends \sizeg\jwt\JwtValidationData
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->validationData->setIssuer(User::JWT_ISSUER);
        $this->validationData->setAudience(User::JWT_AUDIENCE);
        $this->validationData->setId(User::JWT_ID);

        parent::init();
    }
}