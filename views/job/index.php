<?php
use yii\helpers\Url;
?>

<table class="layui-table" id="sys-log-table" lay-filter="sys-log-table"></table>

<!-- 表格状态列 -->
<script type="text/html" id="table-status">
    <input type="checkbox" lay-filter="ckStatus" value="{{d.id}}" lay-skin="switch" lay-text="可用|禁用" {{d.is_deleted==0?'checked':''}}/>
</script>
<!-- 表格操作列 -->
<script type="text/html" id="sys-log-table-bar">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="job">job</a>
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<!-- 表头操作 -->
<script type="text/html" id="sys-log-table-tool-bar">
    <a class="layui-btn layui-btn-sm" lay-event="refresh"><i class="layui-icon layui-icon-refresh"></i>刷新</a>
    <a class="layui-btn layui-btn-sm" lay-event="create">添加任务</a></i></a>
</script>
<script type="text/html" id="tbaleStatus">
    <input type="checkbox" lay-filter="ckStatus" value="{{d.id}}" lay-skin="switch" lay-text="可用|禁用"
           {{d.status==1?'checked':''}}/>
</script>
<script>
    layui.use(['layer', 'form', 'table','tableX', 'laydate'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var tableX = layui.tableX;
        var admin = layui.admin;
        var laydate = layui.laydate;
        var _csrf = "<?= Yii::$app->request->csrfToken ?>";
        var tableId = "";

        // 渲染表格
        tableX.render({
            elem: '#sys-log-table',
            url: '<?=Url::to(["index"])?>',
            toolbar: '#sys-log-table-tool-bar',
            method:'get',
            limit:10000,
            defaultToolbar: ['filter'],
            where:{'init_data':'get-request'}, //用于判断是否为get的请求
            page: false,
            cellMinWidth: 100,
            cols: [[
                {type: 'checkbox', fixed: 'left'},
                {field:'id', align: 'center', title:'编号', sort: true},
                {field:'name', align: 'center', title:'Name', sort: true},
                {field:'run_mode', align: 'center', title:'运行模式', sort: true},
                {field:'status', align: 'center', title:'状态', sort: true, templet: '#tbaleStatus'},
                {fixed: 'right',align: 'center', toolbar: '#sys-log-table-bar', title: '操作', minWidth: 270}
            ]]
        });

        //监听工具条
        table.on('tool(sys-log-table)', function (obj) {
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值

            if (layEvent === 'edit') { //修改
                showEditModel(data)
            } else if (layEvent === 'del') { //删除
                delModel(data,obj);
            } else if (layEvent === 'job'){ //添加job
                pushJobModel(data);
            }
        });

        //监听搜索、添加、刷新
        table.on('toolbar(sys-log-table)', function(obj) {
            var layEvent = obj.event
            if (layEvent === 'clear') { //添加
                layer.confirm('请确认清空日志', function(index){
                    layer.close(index);
                    //向服务端发送删除指令
                    var url = "<?= Url::to(['clear', 'sys'=>f_get('sys')])?>";
                    $.post(url,{'_csrf-backend':_csrf},function (data) {
                        layer.msg("清除成功！");})
                    table.reload('sys-log-table');
                });
            } else if (layEvent === 'search') { //搜索
                //监听搜索
                var searchForm = form.val("searchForm")
                table.reload('sys-log-table', {
                    page: {curr: 1},
                    where: searchForm
                })
            } else if (layEvent === 'refresh'){ //刷新
                table.reload('sys-log-table')
            } else if (layEvent === 'create') {
                showEditModel()
            }
        })

        //编辑单元格
        table.on('edit(sys-log-table)', function(obj) {
            let params = {
                value:obj.value,
                attribute:obj.field,
                //'".\Yii::$app->request->csrfParam."':'".\Yii::$app->request->getCsrfToken()."'
            }
            $.post('<?= Url::to(['update-attribute']) ?>?id='+obj.data.id, params,function(data) {
                layer.msg('修改成功', {icon:1})
            })
        })

        form.on('switch(ckStatus)', function (obj) {
            let params = {
                value:obj.elem.checked ? 0 : 1,
                attribute: 'is_deleted',
            }
            $.post('<?= Url::to(['update-attribute']) ?>?id='+obj.value, params,function(data) {
                layer.msg('修改成功', {icon:1})
            })
        })

        function delModel(table_data,obj) {
            layer.confirm('真的要删除该记录吗？', function(index){
                obj.del(); //删除对应行（tr）的DOM结构
                layer.close(index);
                //向服务端发送删除指令
                var url = "<?= Url::to(['delete'])?>?id="+table_data.name;
                $.post(url,{'_csrf-backend':_csrf},function (data) {
                    layer.msg("删除成功！");})
                table.reload('sys-log-table');
            });

        }

        //修改and添加
        function showEditModel(data) {
            admin.putTempData('t-ok', false);
            top.layui.admin.open({
                type: 2,
                title: data ? '修改Topic' : '添加topic',
                maxmin: true,
                resize: true,
                area: ['50%', '70%'],
                content: data ? '<?=Url::to(['update'])?>?id='+data.name : '<?= Url::to(['create'])?>',
                end: function () {
                    admin.getTempData('t-ok') && table.reload('sys-log-table');  // 成功刷新表格
                }
            });
        }

        //修改and添加
        function pushJobModel(data) {
            admin.putTempData('t-ok', false);
            top.layui.admin.open({
                type: 2,
                title: '添加job',
                maxmin: true,
                resize: true,
                area: ['50%', '70%'],
                content: '<?=Url::to(['push-job'])?>?id='+data.name,
                end: function () {
                    admin.getTempData('t-ok') && table.reload('sys-log-table');  // 成功刷新表格
                }
            });
        }

        function viewModel(data) {
            top.layui.admin.open({
                type:2,
                title:'查看',
                content:'<?= Url::to(['view'])?>?id='+data.name,
                maxmin: true,
                resize: true,
                area: ['50%', '70%'],
                btn: ['关闭']

            });
        }
        // 时间范围
        laydate.render({
            elem: '#sys-log-log_time',
            type: 'date',
            range: true,
            theme: 'molv'
        });
    });
</script>