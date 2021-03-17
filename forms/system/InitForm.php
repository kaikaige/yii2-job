<?php

namespace kaikaige\job\forms\system;
use kaikaige\job\components\JobClient;
use yii\helpers\ArrayHelper;

/**
 * Class CreateForm
 * @package kaikaige\dq\forms\topic
 */
class InitForm extends \yii\base\Model
{
    public $db_type;

    public $db_host;

    public $db_port;

    public $db_username;

    public $db_password;

    public $db_name;

    public $db_table_prefix;

    public function rules()
    {
        return [
            [['db_name', 'db_password', 'db_username', 'db_port', 'db_host', 'db_type'], 'required'],
            [['db_table_prefix'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'db_type' => '数据库类型',
            'db_host' => '链接地址',
            'db_port' => '端口',
            'db_username' => '用户名',
            'db_password' => '密码',
            'db_name' => '数据库名',
            'db_table_prefix' => '数据表前缀'
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
        $res = $jobClient->initSystem($this->attributes);
        $res['msg'] = $res['message'];
        return $res;
    }
}
