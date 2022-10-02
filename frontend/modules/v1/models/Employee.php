<?php

namespace frontend\modules\v1\models;

class Employee extends \common\models\Employee
{
    public function fields()
    {
        return [
            'id',
            'first_name',
            'last_name',
            'surname',
            'passport',
            'position',
            'phone_number',
            'address',
            //'company_id',
            'status',
            'created_at',
            'updated_at',
        ];
    }
}