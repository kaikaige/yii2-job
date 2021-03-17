<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\SysRole;
use yii\helpers\Url;
use common\models\user\UserBrandInfo;
use backend\models\UserAdmin;
/* @var $this yii\web\View */
/* @var $model \kaikaige\job\forms\system\InitForm */
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
    <?= $form->field($model, 'db_type')->dropDownList(['mysql'=>'Mysql']) ?>
    <?= $form->field($model, 'db_host')->textInput(['placeholder' => 'localhost']) ?>
    <?= $form->field($model, 'db_port')->textInput(['placeholder' => '3306']) ?>
    <?= $form->field($model, 'db_username')->textInput(['placeholder' => 'root']) ?>
    <?= $form->field($model, 'db_password')->textInput(['placeholder' => 'root password']) ?>
    <?= $form->field($model, 'db_name')->textInput(['placeholder' => 'db name']) ?>
    <?= $form->field($model, 'db_table_prefix')->textInput(['placeholder' => 'cron_']) ?>

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

        // admin.iframeAuto();  // 让当前iframe弹层高度适应
        // 表单提交事件
        form.on('submit(btnSubmit)', function (data) {
            admin.req(url,data.field,function (data) {
                if (data.code == 0) {
                    top.layer.msg('安装成功，等待跳转', {icon: 1, time: 2000}, function () {
                        window.location.href = '<?= Url::to(['job/index']) ?>'
                    });
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
