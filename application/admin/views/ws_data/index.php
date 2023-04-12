<div class="layuimini-container">
    <div class="layuimini-main">
    <fieldset class="table-search-fieldset">
            <legend>搜索信息</legend>
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" method="post" action="admincp.php/<?=$template?>">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">关键词</label>
                            <div class="layui-input-inline">
                                <input type="text" name="keyword" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">物损大类</label>
                            <div class="layui-input-inline">
                                <select name="category" lay-search>
                                    <option value="">请选择</option>
                                    <?php if ($category_list) {
                                        foreach ($category_list as $value) { ?>
                                    <option value="<?=$value['id']?>"><?=$value['name']?></option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>

                        <div class="layui-inline">
                            <button type="submit" class="layui-btn"  lay-submit lay-filter="data-search-btn"><i class="layui-icon"></i> 搜 索</button>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>

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
        <div id="pages">
            <?php echo $pagination; ?>
        </div>
    </div>
</div>
<script type="text/html" id="statusTpl">
    {{# if(d.display != 3){ }}
        <input type="checkbox" name="status" value="{{d.id}}" lay-skin="switch" lay-text="启用|禁用" lay-filter="statusSwitch" {{ d.status == 1 ? 'checked' : '' }}>
    {{# }else{ }}
        已注销
    {{# } }}
</script>
<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js" charset="utf-8"></script>
<script>
    layui.use(['table', 'common'], function () {
        var $ = layui.jquery,
            table = layui.table,
            common = layui.common,
            form = layui.form;

        var table_data = <?=$item_list?>;
        var table_limit = <?=$table_limit?>;
        table.render({
            elem: '#currentTableId',
            data: table_data,
            toolbar: '#toolbar',
            defaultToolbar: [],
            cols: [[
                {type: "checkbox", width: 50},
                // {field: 'id', width: 80, title: 'ID', sort: true},
                {field: 'category', minWidth: 120, title: '物损大类'},
                {field: 'category_1', width: 120, title: '细分类别1'},
                {field: 'category_2', width: 120, title: '细分类别2'},
                {field: 'province', width: 80, title: '省份'},
                {field: 'city', width: 80, title: '地市'},
                {field: 'brand', width: 120, title: '品牌'},
                {field: 'project', width: 120, title: '项目名称'},
                {field: 'model', width: 120, title: '型号规格'},
                {field: 'unit', width: 80, title: '单位'},
                {field: 'num', width: 80, title: '数量'},
                {field: 'price', width: 120, title: '单价'},
                {field: 'remark', width: 120, title: '备注'},
                {field: 'img', width: 120, height: 120, title: '图片', templet: function (d) {
                    return '<div class="layui-upload-list layui-inline layer-photos" id="layer-photos">'
                    +'<img class="layui-upload-img" id="image" lay-src="'+d.path+'" src="'+d.thumb+'" style="width: 100px;">'
                    +'</div>'
                }},
                {field: 'source', width: 120, title: '案件来源'},
                // {field: 'status', width: 100, title: '状态', templet: '#statusTpl', unresize: true},
                // {field: 'create_time', width: 180, title: '添加时间'},
                {title: '操作', width: 150, toolbar: '#currentTableBar', align: "center"}
            ]],
            limit:table_limit
        });


        /**
         * toolbar监听事件
         */
        table.on('toolbar(currentTableFilter)', function (obj) {
            if (obj.event === 'add') {  // 监听添加操作
                var index = layer.open({
                    title: '添加会员',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: 'admincp.php/'+controller+'/save.html',
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
                    layer.confirm('真的删除选中会员么?', function(index){

                        layer.close(index);
                        //向服务端发送删除指令
                        var url = base_url + 'admincp.php/'+controller+'/delete';
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
                    title: '编辑数据',
                    type: 2,
                    shade: 0.2,
                    maxmin:true,
                    shadeClose: true,
                    area: ['100%', '100%'],
                    content: 'admincp.php/'+controller+'/save/'+data.id+'.html',
                });
                $(window).on("resize", function () {
                    layer.full(index);
                });
                return false;
            } else if (obj.event === 'delete') {
                layer.confirm('真的删除该条数据么', function (index) {
                    layer.close(index);
                    var url = base_url + 'admincp.php/'+controller+'/delete';
                    common.asyncDoRequest(url, {'ids': data.id}, {
                        success: function (res) {
                            obj.del();
                        }
                    });
                });
            }
        });

        //监听状态操作
        form.on('switch(statusSwitch)', function(obj){
            var status = obj.elem.checked ? 1 : 2;
            var url = base_url + 'admincp.php/'+controller+'/display'
            common.asyncDoRequest(url, {'ids': obj.value, 'display': status});
            return false;
        });

        layer.photos({
            photos: '.layer-photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型
        }); 

    });
</script>