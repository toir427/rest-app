<?php

namespace frontend\modules\v1\models;

class Company extends \common\models\Company
{
    public function fields()
    {
        return [
            'id',
            'name',
            'leader_name',
            'address',
            'email',
            'website',
            'phone_number',
            //'user_id',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}