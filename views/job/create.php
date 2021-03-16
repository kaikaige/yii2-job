<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\SysRole;
use yii\helpers\Url;
use common\models\user\UserBrandInfo;
use backend\models\UserAdmin;
/* @var $this yii\web\View */
/* @var $model \kaikaige\job\forms\job\CreateForm */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    html{
        background-color: #ffffff;
    }
</style>
<link rel="stylesheet" href="<?= Url::to(['/layui/assets/module/formSelects/formSelects-v4.css']) ?>"/>
<div class="user-admin-form">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'class'=>'layui-form model-form',
            'lay-filter'=>'user-admin-filter',
            'id'=>'user-admin-form'
        ],
        'fieldConfig'=>[
            'template' => '<div class="layui-form-item">{label}<div class="layui-input-block">{input}</div></div>',
            'labelOptions' => ['class' => 'layui-form-label'],
            'inputOptions' => ['class'=>'layui-input'],
            'options'=>['tag'=>false],
        ]
    ]); ?>
    <?php if($model->id) {?>
        <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'disabled' => true]) ?>
    <?php } ?>
    <?= $form->field($model, 'run_mode')->dropDownList([1=>'计划任务', 2=>'常驻任务'], ['disabled'=>true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => '任务名称，不能重复']) ?>
    <?= $form->field($model, 'command')->textInput(['maxlength' => true, 'placeholder' => 'shell命令']) ?>
    <?php if($model->run_mode == 1) { ?>
        <?= $form->field($model, 'spec')->textInput(['maxlength' => true, 'placeholder' => '秒 分 时 日 月 周']) ?>
        <?= $form->field($model, 'multi')->dropDownList([1=>'多实例同时运行', 0=>'单例运行']) ?>
    <?php } else { ?>
        <?= $form->field($model, 'thread_num')->textInput(['maxlength' => true, 'placeholder' => '如果是常驻脚本，启动的进程数量']) ?>
        <?= $form->field($model, 'multi')->dropDownList([1=>'多实例同时运行'], ['disabled'=>true]) ?>
    <?php } ?>


    <div class="layui-form-item">
        <label class="layui-form-label">
            主机</label><div class="layui-input-block">
            <div id="hosts" class="xm-select-demo"></div>
        </div>
    </div>


    <div class="layui-form-item text-right">
        <button class="layui-btn layui-btn-primary" type="button" ew-event="closeDialog">取消</button>
        <button class="layui-btn" lay-filter="btnSubmit" type="button" lay-submit>保存</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    layui.use(['layer', 'form', 'admin', 'formSelects', 'xmSelect'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var laydate = layui.laydate;
        var admin = layui.admin;
        var formSelects = layui.formSelects;
        var url = $('#user-admin-form').attr('action');
        var hosts = eval('<?= $model->hosts ?>')
        var hosts = xmSelect.render({
            el: '#hosts',
            name: 'CreateForm[host_id]',
            layVerify: 'required',
            layVerType: 'msg',
            toolbar: {show: true},
            data: hosts
        })

        // admin.iframeAuto();  // 让当前iframe弹层高度适应
        // 表单提交事件
        form.on('submit(btnSubmit)', function (data) {
            layer.load(3);
            admin.req(url,data.field,function (data) {
                layer.closeAll('loading');
                if (data.code == 0) {
                    top.layer.msg(data.msg, {icon: 1});
                    admin.putTempData('t-ok', true);  // 操作成功刷新表格
                    // 关闭当前iframe弹出层
                    admin.closeThisDialog();
                } else {
                    top.layer.msg(data.msg, {icon: 2});
                }
            },'post');
            return false;
        });

        form.on('radio(if_master)', function(data){
            if (data.value == 0){
                $('.user-brand-id').show();
            }else {
                $('.user-brand-id').hide();
            }
        });

    });
</script>
