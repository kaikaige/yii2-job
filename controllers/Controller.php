<?php
namespace kaikaige\job\controllers;

use yii;
use kaikaige\job\components\JobClient;
use yii\helpers\Url;

class Controller extends \yii\web\Controller
{
    /**
     * @var JobClient
     */
    public $jobClient;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->jobClient = $this->module->client;
        $this->layout = '../layouts/main';
    }

    public function beforeAction($action)
    {
        $isInit = $this->jobClient->isInit();
        if (!$isInit && $this->id.'/'.$action->id != 'system/init') {
            $this->redirect(['system/init']);
        }

//        if ($isInit && $this->id.'/'.$action->id == 'system/init') {
//            $this->redirect(['job/index']);
//        }

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}