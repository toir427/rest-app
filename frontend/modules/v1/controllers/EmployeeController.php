<?php

namespace app\modules\v1\controllers;

use common\models\User;
use frontend\modules\v1\models\Employee;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends ActiveController
{
    public $modelClass = 'frontend\modules\v1\models\Employee';

    public $createScenario = Employee::SCENARIO_CREATE;

    public $updateScenario = Employee::SCENARIO_UPDATE;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['bearerAuth'] = [
            'class' => JwtHttpBearerAuth::className(),
        ];

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;

        return $behaviors;
    }

    /**
     * @param string $action
     * @param Employee|null $model
     * @param array $params
     * @throws \yii\web\ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        /**
         * admin
         *      can manage all companies (CRUD - all permission)
         *      can view all employees (R)
         * company
         *      can manage own information - by id (CRUD)
         *      can manage own employees' information (CRUD)
         * */

        $permissions = [
            'index' => User::PERMISSION_EMPLOYEES_LIST,
            'view' => User::PERMISSION_EMPLOYEE_DETAIL,
            'create' => User::PERMISSION_CREATE_EMPLOYEE,
            'update' => User::PERMISSION_UPDATE_EMPLOYEE,
            'delete' => User::PERMISSION_DELETE_EMPLOYEE
        ];

        foreach ($permissions as $key => $permission) {
            if ($action === $key && !Yii::$app->user->can($permission)) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to view this {action}', [
                        'action' => $action
                    ])
                );
            }
        }

        // company can manage own employees' information
        $user = $model != null && $model->company != null ? $model->company->user_id : null;
        if ($action === 'update' && !Yii::$app->user->can(User::PERMISSION_UPDATE_OWN_EMPLOYEE, ['user' => $user])) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'You are not allowed to view this {action}', [
                    'action' => $action
                ])
            );
        }
    }
}
