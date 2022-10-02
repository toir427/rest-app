<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "employee".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $surname
 * @property string $passport
 * @property int $position
 * @property string $phone_number
 * @property string $address
 * @property int $company_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Company $company
 */
class Employee extends \yii\db\ActiveRecord
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const POSITION_TEAM_LEADER = 1;
    const POSITION_MANAGER = 2;
    const POSITION_ASSISTANT_MANAGER = 3;
    const POSITION_EXECUTIVE = 4;
    const POSITION_DIRECTOR = 5;
    const POSITION_COORDINATOR = 6;
    const POSITION_ADMINISTRATOR = 7;
    const POSITION_CONTROLLER = 8;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DISABLE => 'Disable',
            self::STATUS_ENABLE => 'Enable',
        ];
    }

    public static function getPositionOptions()
    {
        return [
            self::POSITION_TEAM_LEADER => 'Team Leader',
            self::POSITION_MANAGER => 'Manager',
            self::POSITION_ASSISTANT_MANAGER => 'Assistant Manager',
            self::POSITION_EXECUTIVE => 'Executive',
            self::POSITION_DIRECTOR => 'Director',
            self::POSITION_COORDINATOR => 'Coordinator',
            self::POSITION_ADMINISTRATOR => 'Administrator',
            self::POSITION_CONTROLLER => 'Controller',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position', 'company_id', 'status'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['position', 'company_id', 'passport'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['status', 'in', 'range' => array_keys(self::getStatusOptions()), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['position', 'in', 'range' => array_keys(self::getPositionOptions()), 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['first_name', 'last_name', 'surname', 'passport', 'phone_number', 'address'], 'string', 'max' => 255, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['email', 'passport'], 'unique', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],

            [['first_name', 'last_name', 'surname', 'passport', 'phone_number', 'address'], 'safe', 'on' => self::SCENARIO_CREATE],
            [['first_name', 'last_name', 'surname', 'phone_number', 'address'], 'safe', 'on' => self::SCENARIO_UPDATE],

            [['status'], 'default', 'value' => self::STATUS_ENABLE],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => ['first_name', 'last_name', 'surname', 'passport', 'phone_number', 'address', 'position', 'company_id', 'status'],
            self::SCENARIO_UPDATE => ['first_name', 'last_name', 'surname', 'phone_number', 'address', 'position', 'company_id', 'status'],
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
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'surname' => 'Surname',
            'passport' => 'Passport',
            'position' => 'Position',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'company_id' => 'Company ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }
}
