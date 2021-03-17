<?php
namespace kaikaige\job\controllers;

use kaikaige\job\forms\system\InitForm;

class SystemController extends Controller
{
   public function actionInit()
   {
       $model = new InitForm();
       if (\Yii::$app->request->isPost) {
           $model->load(\Yii::$app->request->post());

           return $this->asJson($model->run($this->jobClient));
       } else {
           return $this->render('init', [
               'model' => $model
           ]);
       }
   }
}