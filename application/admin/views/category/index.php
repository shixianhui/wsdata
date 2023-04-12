<div class="layuimini-container">
    <div class="layuimini-main">
        <script type="text/html" id="toolbar">
            <div class="layui-btn-container">
            <button class="layui-btn layui-btn-normal layui-btn-sm data-add-btn" lay-event="add"><i class="layui-icon layui-icon-add-1"></i> 添加 </button>
                <button class="layui-btn layui-btn-sm layui-btn-danger data-delete-btn" lay-event="delete"><i class="layui-icon layui-icon-delete"></i> 批量删除 </button>
            </div>
        </script>

        <table class="layui-hide" id="currentTableId" lay-filter="currentTableFilter"></table>

        <script type="text/html" id="currentTableBar">
            <a class="layui-btn layui-btn-normal layui-btn-xs data-count-edit" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete" lay-event="delete">删除</a>
        </script>

    </div>
</div>
<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js" charset="utf-8"></script>
<script>
    layui.use(['form', 'table', 'common'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table;
            common = layui.common;

        var table_data = <?=$item_list?>;
        table.render({
            elem: '#currentTableId',
            data: table_data,
            toolbar: '#toolbar',
            defaultToolbar: [],
            cols: [[
                {type: "checkbox", width: 50},
                {field: 'id', width: 80, title: 'ID', sort: true},
                {field: 'group_name', minWidth: 120, title: '管理组名'},
                {title: '操作', width: 150, toolbar: '#currentTableBar', align: "center"}
            ]],
            limit: table_data.length
        });

        /**
         * toolbar监听事件
         */
        table.on('toolbar(currentTableFilter)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                var index = layer.open({
                    title: '添加管理组',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: 'admincp.php/admin_group/save.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
            } else if (obj.event === 'delete') {  // 监听删除操作
                var checkStatus = table.checkStatus('currentTableId')
                    , data = checkStatus.data;

                if (data.length > 0) {
                    let ids = ''; 
                    data.forEach(function(item){
                        ids += item.id+',';
                    });
                    ids = ids.substr(0, ids.length - 1);
                    layer.confirm('真的删除行么?', function(index){
                        layer.close(index);
                        //向服务端发送删除指令
                        var url = base_url + 'admincp.php/admin_group/delete';
                        common.asyncDoRequest(url, {'ids': ids}, {
                            success: function(res) {
                                //layui中找到CheckBox所在的行，并遍历找到行的顺序
                                $('div.layui-table-body table tbody input[name="layTableCheckbox"]:checked').each(function() { // 遍历选中的checkbox
                                    n = $(this).parents('tbody tr').index()  // 获取checkbox所在行的顺序
                                    //移除行
                                    $('div.layui-table-body table tbody ').find('tr:eq(' + n + ')').remove()
                                    
                                })
                                //如果是全选移除，就将全选CheckBox还原为未选中状态
                                $('div.layui-table-header table thead div.layui-unselect.layui-form-checkbox').removeClass('layui-form-checked')
                            }
                        });
                    });
                } else {
                    layer.msg('请选择删除项');
                }
            }
        });

        //监听表格复选框选择
        table.on('checkbox(currentTableFilter)', function (obj) {
            console.log(obj)
        });

        table.on('tool(currentTableFilter)', function (obj) {
            var data = obj.data;
            if (obj.event === 'edit') {
                var index = layer.open({
                    title: '编辑管理组',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: 'admincp.php/admin_group/save/'+data.id+'.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
                return false;
            } else if (obj.event === 'delete') {
                layer.confirm('真的删除行么', function (index) {
                    layer.close(index);
                    var url = base_url + 'admincp.php/admin_group/delete';

                    common.asyncDoRequest(url, {'ids': data.id}, {
                        success: function(res){
                            obj.del();
                        },
                        fail: function(res){
                            console.log(res)
                        }
                    });
                });
            }
        });

    });
</script>