<?php
namespace kaikaige\job\controllers;

use kaikaige\job\forms\job\CreateForm;
use kaikaige\job\forms\job\PushJobForm;

class JobController extends Controller
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isAjax) {
            $data = $this->jobClient->jobList();
            $data['code'] = 0;
            foreach ($data['data'] as $key=>$job) {
                $data['data'][$key]['run_mode'] = $job['run_mode'] == 1 ? '计划任务' : '常驻任务';
            }
            return $this->asJson($data);
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
        $res = $this->dq->topicView($id);
        if (!$res->isOk) {
            throw new \Exception($res->getContent());
        }
        $model = new CreateForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            return $this->asJson($model->run($this->dq));
        } else {
            $model->setAttributes($res->getData());
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionDelete($id)
    {
        return $this->asJson($this->dq->topicDelete($id));
    }

    public function actionView($id)
    {
        $res = $this->dq->topicView($id);
        if ($res['code'] == 200) {
            return $this->render('view', ['data' => $res['data']]);
        }
    }

    public function actionPushJob($id)
    {
        $model = new PushJobForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            return $this->asJson($model->run($this->dq));
        } else {
            $model->topic = $id;
            return $this->render('push-job', [
                'model' => $model
            ]);
        }
    }
}