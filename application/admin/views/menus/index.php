<link rel="stylesheet" href="js/admin/lib/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
<style>
    .layui-btn:not(.layui-btn-lg ):not(.layui-btn-sm):not(.layui-btn-xs) {
        height: 34px;
        line-height: 34px;
        padding: 0 8px;
    }
</style>
<div class="layuimini-container">
    <div class="layuimini-main">
        <script type="text/html" id="toolbarDemo">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-normal layui-btn-sm data-add-btn" lay-event="add"><i class="layui-icon layui-icon-add-1"></i> 添加 </button>
                <button class="layui-btn layui-btn-sm layui-btn-danger data-delete-btn" lay-event="delete"><i class="layui-icon layui-icon-delete"></i> 批量删除 </button>
            </div>
        </script>
        <div>
            <div class="layui-btn-group">
                <button class="layui-btn" id="btn-expand">全部展开</button>
                <button class="layui-btn layui-btn-normal" id="btn-fold">全部折叠</button>
            </div>
            <table id="munu-table" class="layui-table" lay-filter="munu-table"></table>
        </div>
    </div>
</div>
<!-- 操作列 -->
<script type="text/html" id="actionBar">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>

<script type="text/html" id="statusTpl">
  <input type="checkbox" name="status" value="{{d.id}}" lay-skin="switch" lay-text="显示|隐藏" lay-filter="statusSwitch" {{ d.status == 1 ? 'checked' : '' }}>
</script>

<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js?v=1.0.4" charset="utf-8"></script>
<script>
    var json = <?=$item_list?>;
    layui.use(['table', 'treeTable', 'common'], function () {
        var $ = layui.jquery;
        var table = layui.table;
        var treeTable = layui.treeTable;
        var form = layui.form;
        var common = layui.common;

        // 渲染表格
        // layer.load(2);
        var insTb = treeTable.render({
            elem: '#munu-table',
            // url: 'api/menus.json',
            data: json,
            tree: {
                iconIndex: 1,           // 折叠图标显示在第几列
                isPidData: true,        // 是否是id、pid形式数据
                idName: 'id',  // id字段名称
                pidName: 'parent_id',     // pid字段名称
                getIcon: '',
            },
            toolbar: '#toolbarDemo',
            defaultToolbar: [],
            cols: [[
                {type: 'checkbox'},
                {field: 'title', minWidth: 200, title: '菜单名称', templet: function (d) {
                        // d是当前行的数据
                        if (d.icon) {
                            return '<i class="fa '+ d.icon +'"></i>  '+ d.title;
                        } else {
                            // d是当前行的数据
                            if (d.is_ancestry) {  // 判断是否有子集
                                return '<i class="layui-icon layui-icon-layer"></i>  '+ d.title;
                            } else {
                                return '<i class="layui-icon layui-icon-file"></i>  '+ d.title;
                            }
                        }
                    }
                },
                {field: 'component', title: '组件名称'},
                {field: 'permissions', title: '权限标识'},
                {field: 'href', title: '菜单链接'},
                {field: 'sort', width: 80, align: 'center', title: '排序号', edit: 'text'},
                {field: 'status', width: 100, title: '状态', templet: '#statusTpl', unresize: true},
                {
                    field: 'type', width: 80, align: 'center', templet: function (d) {
                        if (d.type == 0) {
                            return '<span class="layui-badge layui-bg-blue">目录</span>';
                        } else if(d.type == 1) {
                            return '<span class="layui-badge-rim">菜单</span>';
                        } else if(d.type == 2) {
                            return '<span class="layui-badge layui-bg-gray">按钮</span>';
                        }
                    }, title: '类型'
                },
                {templet: '#actionBar', width: 120, align: 'center', title: '操作'}
            ]],
            done: function () {
                // layer.closeAll('loading');
            }
        });

        $('#btn-expand').click(function () {
            insTb.expandAll();
        });

        $('#btn-fold').click(function () {
            insTb.foldAll();
        });


        /**
         * toolbar监听事件
         */
        treeTable.on('toolbar(munu-table)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                var index = layer.open({
                    title: '添加菜单',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: 'admincp.php/menus/save.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
            } else if (obj.event === 'delete') {  // 监听删除操作
                var data = insTb.checkStatus(false);;
                if (data.length > 0) {
                    let ids = ''; 
                    data.forEach(function(item){
                        ids += item.id+',';
                    });
                    ids = ids.substr(0, ids.length - 1);
                    layer.confirm('真的删除行么?', function(index){
                        layer.close(index);
                        //向服务端发送删除指令
                        var url = base_url + 'admincp.php/menus/delete'
                        common.asyncDoRequest(url, {'ids': ids}, {
                            success: function (res) {
                                data.forEach(function(item){
                                    $("tr[data-index='" + item.LAY_INDEX + "']").remove();
                                });
                            }
                        });
                        
                    });
                } else {
                    layer.msg('请选择删除项');
                }
            }
        });

        //监听表格复选框选择
        treeTable.on('checkbox(munu-table)', function (obj) {
            console.log(obj)
        });

        //监听工具条
        treeTable.on('tool(munu-table)', function (obj) {
            var data = obj.data;
            var layEvent = obj.event;

            if (layEvent === 'del') {
                layer.confirm('真的删除行么?', function(index){
                    layer.close(index);
                    //向服务端发送删除指令
                    var url = base_url + 'admincp.php/menus/delete'
                    common.asyncDoRequest(url, {'ids': data.id}, {
                        success: function (res) {
                            obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                        }
                    });
                });
                
            } else if (layEvent === 'edit') {
                var index = layer.open({
                    title: '添加菜单',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: 'admincp.php/menus/save/'+data.id+'.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
            }
            
        });

        //监听单元格编辑
        treeTable.on('edit(munu-table)', function(obj){
            var value = obj.value //得到修改后的值
            ,data = obj.data //得到所在行所有键值
            ,field = obj.field; //得到字段
            // layer.msg('[ID: '+ data.id +'] ' + field + ' 字段更改为：'+ value);
            var url = base_url + 'admincp.php/menus/sort'
            common.asyncDoRequest(url, {'ids': data.id, 'sorts': value});
        });

        //监听状态操作
        form.on('switch(statusSwitch)', function(obj){
            var status = obj.elem.checked ? 1 : 0;
            var url = base_url + 'admincp.php/menus/display'
            common.asyncDoRequest(url, {'ids': obj.value, 'display': status});
            return false;
        });
    });
</script>