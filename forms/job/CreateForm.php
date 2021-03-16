<?php

namespace kaikaige\job\forms\job;
use kaikaige\job\components\JobClient;
use yii\helpers\ArrayHelper;

/**
 * Class CreateForm
 * @package kaikaige\dq\forms\topic
 * @property $id int
 * @property $name string
 */
class CreateForm extends \yii\base\Model
{
    public $id;

    public $name;

    public $run_mode;

    public $command;

    public $spec;

    public $host_id;

    public $thread_num;

    public $multi;

    public $hosts;

    public function rules()
    {
        return [
            [['id', 'name', 'command'], 'required'],
            [['run_mode', 'spec', 'host_id', 'thread_num', 'multi', 'hosts'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '名称',
            'run_mode' => '模式',
            'command' => 'Shell',
            'spec' => '表达式',
            'thread_num' => '进程数',
            'multi' => '执行方案'
        ];
    }

    /**
     * @des 写入topic
     * @param $jobClient JobClient
     * @date 2021/2/23 15:10
     * @author gaokai
     * @modified_date 2021/2/23 15:10
     * @modified_user gaokai
     */
    public function run($jobClient)
    {
        $res = $jobClient->jobCreate($this->attributes);
        $res['msg'] = $res['message'];
        return $res;
    }

    /**
     * @param $jobClient JobClient
     * @date 2021/2/23 15:10
     * @author gaokai
     * @modified_date 2021/2/23 15:10
     * @modified_user gaokai
     */
    public function flushHosts($jobClient)
    {
        $items = $jobClient->hostList();
        $hosts = $this->hosts ? ArrayHelper::getColumn($this->hosts, 'host_id') : [];
        $this->hosts = [];
        foreach ($items as $host) {
            if (!in_array($host['id'], $hosts)) {
                $this->hosts[] = ['value'=>$host['id'], 'name'=>$host['alias']];
            } else {
                $this->hosts[] = ['value'=>$host['id'], 'name'=>$host['alias'], 'selected'=>true];
            }
        }
        $this->hosts = json_encode($this->hosts);
    }
}
