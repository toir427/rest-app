<?php

namespace app\modules\v1\controllers;

use frontend\modules\v1\models\Employee;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['bearerAuth'] = [
            'class' => JwtHttpBearerAuth::className(),
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'list' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['POST'],
                'delete' => ['DELETE'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Lists all Company models.
     * @return array
     */
    public function actionIndex()
    {
        $all = Employee::find()->all();
        return [
            'status' => 200,
            'error' => null,
            'success' => true,
            'result' => $all
        ];
    }

    public function actionView($id)
    {
        return [
            'status' => 200,
            'error' => null,
            'success' => true,
            'result' => $this->findModel($id),
        ];
    }

    public function actionCreate()
    {
        $model = new Employee();
        $model->scenario = Employee::SCENARIO_CREATE;
        $model->attributes = Yii::$app->request->post();
        if ($model->save()) {
            return [
                'status' => 200,
                'error' => null,
                'success' => true,
                'result' => $model,
            ];
        }

        return [
            'status' => 400,
            'error' => $model->getErrors(),
            'success' => false,
            'result' => null,
        ];
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Employee::SCENARIO_UPDATE;
        $model->attributes = Yii::$app->request->post();
        if ($model->save()) {
            return [
                'status' => 200,
                'error' => null,
                'success' => true,
                'result' => $model,
            ];
        }

        return [
            'status' => 400,
            'error' => $model->getErrors(),
            'success' => false,
            'result' => null,
        ];
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return [
            'status' => 200,
            'error' => null,
            'success' => true,
            'result' => null,
        ];
    }

    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
