<style>
    body {
        background-color: #ffffff;
    }
</style>
<div class="layui-form layuimini-form" lay-filter="saveForm">
    <div class="layui-form-item">
        <label class="layui-form-label required">管理组名称</label>
        <div class="layui-input-block">
            <input type="text" name="group_name" lay-verify="required" lay-reqtext="管理组名称不能为空" placeholder="请输入管理组名称" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">权限列表</label>
        <div class="layui-input-block">
            <div id="menuTree"></div>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
        </div>
    </div>
</div>
<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js" charset="utf-8"></script>
<script>
    layui.use(['form', 'common'], function () {
        var form = layui.form,
            layer = layui.layer,
            common = layui.common,
            $ = layui.$;

        var item_info = <?=$item_info_json?>;
        var menus_list = <?=$menus_list?>;
        var tree = layui.tree;
   
        //渲染
        tree.render({
            elem: '#menuTree',  //绑定元素
            data: menus_list,
            showCheckbox: true,
            id: 'id',
            oncheck: function(obj){
                var data = obj.data;  //获取当前点击的节点数据
            }
        });
        //监听提交
        form.on('submit(saveBtn)', function (data) {
            var checkedData = tree.getChecked('id'); //获取选中节点的数据
            var fields = {}
            fields.group_name = data.field.group_name
            fields.permissions_arr = JSON.stringify(checkedData)
            let url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
            common.asyncDoRequest(url, fields);
            return false;
        });

        //表单赋值
        form.val("saveForm", {
            'group_name': item_info.group_name
        });

        if (item_info.permission_ids){
            console.log(item_info.permission_ids)
            var dataStr = item_info.permission_ids;
            var dataStrArr=dataStr.split(",");
            var dataIntArr=[];
            dataStrArr.forEach(function(data,index,arr){
                dataIntArr.push(data);
            });
            tree.setChecked('id', dataIntArr);
        }

    });
</script>