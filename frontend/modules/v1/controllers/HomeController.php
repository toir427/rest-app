<?php

namespace app\modules\v1\controllers;

use yii\rest\Controller;

/**
 * Default controller for the `api` module
 */
class HomeController extends Controller
{
    public function actionIndex()
    {
        return $this->asJson([
            "status" => 200,
            "error" => null,
            'result' => null
        ]);
    }

    public function actionError()
    {
        return $this->asJson([
            "status" => 404,
            "error" => "Not Found (#404)",
            'result' => null
        ]);
    }
}
