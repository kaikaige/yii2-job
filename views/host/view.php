<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
?>
<div class="layui-fluid user-admin-view">
    <?= DetailView::widget([
        'model' => $data,
        'options' => ['class' => 'layui-table','lay-even'=>'','lay-size'=>'sm'],
        'attributes' => [
            'des',
            'name',
            'delay',
            'ttr',
            'bucket',
            'list',
        ],
    ]) ?>
</div>
