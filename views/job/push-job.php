<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\SysRole;
use yii\helpers\Url;
use common\models\user\UserBrandInfo;
use backend\models\UserAdmin;
/* @var $this yii\web\View */
/* @var $model backend\models\UserAdmin */
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
    <?= $form->field($model, 'topic')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'body')->textarea(['rows'=>'5']) ?>

    <div class="layui-form-item text-right">
        <button class="layui-btn layui-btn-primary" type="button" ew-event="closeDialog">取消</button>
        <button class="layui-btn" lay-filter="btnSubmit" type="button" lay-submit>保存</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    layui.use(['layer', 'form', 'admin', 'formSelects'], function () {
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

        // 添加表单验证方法
        form.verify({
            loginRequired:[/^[a-zA-z]\w{3,15}$/,'账号由字母、数字、下划线组成，字母开头，4-16位'],
            psw: [/^[A-Za-z0-9]{6,20}$/, '密码必须6到20位数字或者字母'],
            email:function (value,item) {
                var reg = new RegExp("^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$");
                if (value.length > 0 && !reg.test(value)) {
                    return '邮箱格式不正确'
                }
            }
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
