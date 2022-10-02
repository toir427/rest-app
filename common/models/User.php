<?php

namespace common\models;

use sizeg\jwt\Jwt;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const JWT_ISSUER = 'http://localhost:8000';
    const JWT_AUDIENCE = 'http://localhost:8000';
    const JWT_SECRET = 'secret';
    const JWT_ID = '4f1g23a12aa';
    const JWT_SIGNER = 'HS256';

    const PERMISSION_COMPANIES_LIST = 'permission_companies_list';
    const PERMISSION_COMPANY_DETAIL = 'permission_company_detail';
    const PERMISSION_CREATE_COMPANY = 'permission_create_company';
    const PERMISSION_UPDATE_COMPANY = 'permission_update_company';
    const PERMISSION_DELETE_COMPANY = 'permission_delete_company';

    const PERMISSION_EMPLOYEES_LIST = 'permission_employees_list';
    const PERMISSION_EMPLOYEE_DETAIL = 'permission_employee_detail';
    const PERMISSION_CREATE_EMPLOYEE = 'permission_create_employee';
    const PERMISSION_UPDATE_EMPLOYEE = 'permission_update_employee';
    const PERMISSION_DELETE_EMPLOYEE = 'permission_delete_employee';

    const PERMISSION_UPDATE_OWN_COMPANY = 'permission_update_own_company';
    const PERMISSION_UPDATE_OWN_EMPLOYEE = 'permission_update_own_employee';

    const ROLE_ADMIN = 'admin';
    const ROLE_COMPANY = 'company';

    public static $permissions = [
        User::PERMISSION_COMPANIES_LIST,
        User::PERMISSION_COMPANY_DETAIL,
        User::PERMISSION_CREATE_COMPANY,
        User::PERMISSION_UPDATE_COMPANY,
        User::PERMISSION_DELETE_COMPANY,

        User::PERMISSION_EMPLOYEES_LIST,
        User::PERMISSION_EMPLOYEE_DETAIL,
        User::PERMISSION_CREATE_EMPLOYEE,
        User::PERMISSION_UPDATE_EMPLOYEE,
        User::PERMISSION_DELETE_EMPLOYEE,
    ];

    public function getJWTToken()
    {
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner(self::JWT_SIGNER);
        $key = $jwt->getKey();
        $time = time();

        return $jwt->getBuilder()
            ->issuedBy(self::JWT_ISSUER)
            ->permittedFor(self::JWT_AUDIENCE)
            ->identifiedBy(self::JWT_ID, true)
            ->issuedAt($time)
            ->expiresAt($time + 3600)
            ->withClaim('uid', $this->id)
            ->getToken($signer, $key);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne($token->getClaim('uid'));
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
