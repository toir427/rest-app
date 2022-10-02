<?php

namespace app\modules\v1\controllers;

use frontend\models\SignupForm;
use frontend\modules\v1\models\LoginForm;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;

/**
 * Default controller for the `api` module
 */
class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['bearerAuth'] = [
            'class' => JwtHttpBearerAuth::className(),
            'optional' => [
                'login',
                'signup',
            ],
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
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        //$behaviors['authenticator']['except'] = ['options'];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'login' => ['POST'],
                'signup' => ['POST'],
                'logout' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        if ($model->login()) {
            return [
                'error' => null,
                'success' => true,
                'status' => 200,
                'result' => [
                    'token' => (string)Yii::$app->user->identity->getJWTToken(),
                    'token_type' => 'bearer',
                ]
            ];
        } else {
            return [
                'error' => ['Incorrect email or password'],
                'success' => false,
                'status' => 401,
                'result' => null,
            ];
        }
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        $model->attributes = Yii::$app->request->post();
        if ($user = $model->signup()) {
            if (Yii::$app->getUser()->login($user)) {
                return [
                    'error' => null,
                    'success' => true,
                    'status' => 200,
                    'result' => [
                        'token' => (string)Yii::$app->user->identity->getJWTToken(),
                        'token_type' => 'bearer',
                    ]
                ];
            }
        }

        return [
            'error' => ['This user was created.'],
            'success' => false,
            'status' => 401,
            'result' => null,
        ];
    }

    public function actionLogout()
    {
        Yii::$app->user->identity->generateAuthKey();
        Yii::$app->user->logout();

        return [
            'error' => null,
            'success' => true,
            'status' => 200,
            'result' => null,
        ];
    }
}
