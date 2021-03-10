<?php

namespace kaikaige\job\forms\host;
use kaikaige\job\components\JobClient;

/**
 * Class CreateForm
 * @package kaikaige\dq\forms\topic
 */
class CreateForm extends \yii\base\Model
{
    public $id;

    public $alias;

    public $name;

    public $port;

    public function rules()
    {
        return [
            [['name', 'alias', 'port'], 'required'],
            ['id', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '名称',
        ];
    }

    /**
     * @param $jobClient JobClient
     * @date 2021/2/23 15:10
     * @author gaokai
     * @modified_date 2021/2/23 15:10
     * @modified_user gaokai
     */
    public function run($jobClient)
    {
        $res = $jobClient->hostCreate($this->attributes);
        $res['msg'] = $res['message'];
        return $res;
    }
}
