<?php
namespace kaikaige\job\controllers;

use kaikaige\job\forms\host\CreateForm;

class HostController extends Controller
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $data = $this->jobClient->hostList();
//            foreach ($data['data'] as $key=>$job) {
//                $data['data'][$key]['run_mode'] = $job['run_mode'] == 1 ? '计划任务' : '常驻任务';
//            }
            return $this->asJson(['data'=>$data, 'code'=>0]);
        }
        return $this->render('index');
    }

    public function actionCreate()
    {
        $model = new CreateForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            return $this->asJson($model->run($this->jobClient));
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = new CreateForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            return $this->asJson($model->run($this->jobClient));
        } else {
            $data = $this->jobClient->hostDetail($id);
            $model->setAttributes($data);
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionDelete($id)
    {
        return $this->asJson($this->dq->topicDelete($id));
    }

    public function actionPing($id)
    {
        return $this->asJson($this->jobClient->hostTest($id));
    }

    public function actionView($id)
    {
        $res = $this->dq->topicView($id);
        if ($res['code'] == 200) {
            return $this->render('view', ['data' => $res['data']]);
        }
    }
}