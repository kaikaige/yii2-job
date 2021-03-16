<?php
namespace kaikaige\job\controllers;

use yii;
use kaikaige\job\forms\job\CreateForm;
use kaikaige\job\forms\job\PushJobForm;

class JobController extends Controller
{
    public function actionIndex()
    {
        $runMode = \Yii::$app->request->get('run_mode', 1);
        if (\Yii::$app->request->isAjax) {
            $data = $this->jobClient->jobList($runMode);
            $data['code'] = 0;
            foreach ($data['data'] as $key=>$job) {
                $data['data'][$key]['next_run_time'] = $job['run_mode'] == 1 ? date('Y-m-d H:i:s', strtotime($job['next_run_time'])) : '手动触发';
                $data['data'][$key]['run_mode'] = $job['run_mode'] == 1 ? '计划任务' : '常驻任务';
            }
            return $this->asJson($data);
        }
        $this->view->title = $runMode == 1 ? '计划任务' : '常驻任务';
        return $this->render('index');
    }

    public function actionCreate()
    {
        $model = new CreateForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            return $this->asJson($model->run($this->jobClient));
        } else {
            $model->run_mode = Yii::$app->request->get('run_mode', 1);
            $model->flushHosts($this->jobClient);
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = new CreateForm();
        $res = $this->jobClient->jobDetail($id);
        if ($res['code'] != 0) {
            throw new \Exception($res['message']);
        }
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->post());
            return $this->asJson($model->run($this->jobClient));
        } else {
            $model->setAttributes($res['data']);
            $model->flushHosts($this->jobClient);
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * @des 删除任务
     * @param $id
     * @date 2021/3/15 14:42
     * @author gaokai
     * @return \yii\web\Response
     * @modified_date 2021/3/15 14:42
     * @modified_user gaokai
     * @throws \yii\httpclient\Exception
     */
    public function actionDelete($id)
    {
        return $this->asJson($this->jobClient->jobCreate($id));
    }

    /**
     * @des 手动执行任务
     * @param $id
     * @date 2021/3/15 14:42
     * @author gaokai
     * @modified_date 2021/3/15 14:42
     * @modified_user gaokai
     */
    public function actionRun($id)
    {
        return $this->asJson($this->jobClient->jobRun($id));
    }

    /**
     * @des 手动停止任务，此处ID为执行log id
     * @param $id
     * @date 2021/3/16 14:29
     * @author gaokai
     * @return \yii\web\Response
     * @modified_date 2021/3/16 14:29
     * @modified_user gaokai
     * @throws \yii\httpclient\Exception
     */
    public function actionStop($id, $taskId)
    {
        return $this->asJson($this->jobClient->jobLogStop($id, $taskId));
    }

    public function actionSwitchStatus($id)
    {
        if (\Yii::$app->request->post('value') == 0) {
            $data = $this->jobClient->jobEnable($id);
        } else {
            $data = $this->jobClient->jobDisable($id);
        }
        return $this->asJson($data);
    }

    /**
     * @des 执行日志
     * @param $id
     * @date 2021/3/15 14:55
     * @author gaokai
     * @return \yii\web\Response
     * @modified_date 2021/3/15 14:55
     * @modified_user gaokai
     * @throws \yii\httpclient\Exception
     */
    public function actionLog($id)
    {
        if (\Yii::$app->request->isAjax) {
            $data = $this->jobClient->jobLogList($id, \Yii::$app->request->get('page'), \Yii::$app->request->get('limit'));
            $data['code'] = 0;
            $data['count'] = $data['data']['total'];
            $data['data'] = $data['data']['data'];
            foreach ($data['data'] as $key=>$job) {
                $data['data'][$key]['total_time'] = sprintf("<div>总耗时: %s</div><div>开始时间: %s</div><div>结束时间: %s</div>", $job['total_time'], date('Y-m-d H:i:s', strtotime($job['start_time'])), date('Y-m-d H:i:s', strtotime($job['end_time'])));
                //状态替换
                switch ($job['status']) {
                    //0:执行失败 1:执行中  2:执行完毕 3:任务取消(上次任务未执行完成) 4:异步执行
                    case 0:
                        $data['data'][$key]['status_text'] = '<span style="color:red;">执行失败</span>';
                        break;
                    case 1:
                        $data['data'][$key]['status_text'] = '<span style="color:#c69027;">执行中</span>';
                        break;
                    case 2:
                        $data['data'][$key]['status_text'] = '<span style="color:#37B71B;">执行成功</span>';
                        break;
                    case 3:
                        $data['data'][$key]['status_text'] = '<span style="color:cornflowerblue;">任务取消</span>';
                        break;
                    case 4:
                        $data['data'][$key]['status_text'] = '异步执行';
                        break;
                }
            }
            return $this->asJson($data);
        }
        return $this->render('log');
    }

    /**
     * @des 清除任务的日志
     * @param $id
     * @date 2021/3/16 15:20
     * @author gaokai
     * @return \yii\web\Response
     * @modified_date 2021/3/16 15:20
     * @modified_user gaokai
     * @throws \yii\httpclient\Exception
     */
    public function actionClearLog($id)
    {
        return $this->asJson($this->jobClient->jobLogClear($id));
    }
}