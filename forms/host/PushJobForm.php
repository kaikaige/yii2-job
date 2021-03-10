<?php

namespace kaikaige\job\forms\job;

class PushJobForm extends \yii\base\Model
{
    public $topic;

    public $body;

    public function rules()
    {
        return [
            [['topic', 'body'], 'required'],
        ];
    }

    /**
     * @des push job
     * @param $dq \kaikaige\dq\components\DqClient
     * @date 2021/2/23 15:10
     * @author gaokai
     * @modified_date 2021/2/23 15:10
     * @modified_user gaokai
     */
    public function run($dq)
    {
        $res = $dq->jobPush($this->topic, $this->body);
        if ($res->isOk) {
            return ['code'=>0, 'msg'=>'添加成功'];
        }
        return ['code'=>1, 'msg'=>$res->getContent()];
    }
}
