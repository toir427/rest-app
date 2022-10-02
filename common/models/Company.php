<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "company".
 *
 * @property intc $id
 * @property string $name
 * @property string $leader_name
 * @property string $address
 * @property string $email
 * @property string $website
 * @property string $phone_number
 * @property int $user_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 * @property Employee[] $employees
 */
class Company extends \yii\db\ActiveRecord
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DISABLE => 'Disable',
            self::STATUS_ENABLE => 'Enable',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['user_id', 'status'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['status', 'in', 'range' => array_keys(self::getStatusOptions()), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['status', 'in', 'range' => array_keys(self::getStatusOptions()), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['name', 'leader_name', 'address', 'email', 'website', 'phone_number'], 'string', 'max' => 255, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['name', 'email'], 'unique', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],

            [['name', 'leader_name', 'address', 'email', 'website', 'phone_number', 'user_id'], 'safe', 'on' => self::SCENARIO_CREATE],
            [['name', 'leader_name', 'address', 'website', 'phone_number', 'user_id'], 'safe', 'on' => self::SCENARIO_UPDATE],

            [['status'], 'default', 'value' => self::STATUS_ENABLE],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['name', 'leader_name', 'address', 'email', 'website', 'phone_number', 'user_id', 'status'],
            self::SCENARIO_UPDATE => ['name', 'leader_name', 'address', 'website', 'phone_number', 'user_id', 'status'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'leader_name' => 'Leader Name',
            'address' => 'Address',
            'email' => 'Email',
            'website' => 'Website',
            'phone_number' => 'Phone Number',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployees()
    {
        return $this->hasMany(Employee::className(), ['company_id' => 'id']);
    }
}
