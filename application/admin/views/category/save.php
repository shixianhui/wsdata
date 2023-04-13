<style>
    body {
        background-color: #ffffff;
    }
</style>
<div class="layui-form layuimini-form" lay-filter="saveForm">
    <div class="layui-form-item">
        <label class="layui-form-label">上级分类</label>
        <div class="layui-input-block" style="width: 20%;">
            <input disabled type="text" name="parent_menu" placeholder="请选择上级分类" value="" class="layui-input" id="menuSelect">
            <input type="hidden" name="parent_id" id="parent_id">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">分类名称</label>
        <div class="layui-input-block">
            <input type="text" name="name" lay-verify="required" lay-reqtext="分类名称不能为空" placeholder="请输入分类名称" value="" class="layui-input">
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
    layui.use(['form', 'common', 'customSelect'], function () {
        var form = layui.form,
            layer = layui.layer,
            common = layui.common,
            customSelect = layui.customSelect,
            $ = layui.$;

        var item_info = <?=$item_info_json?>;
        var menus_list = <?=$menus_list?>;
   
        customSelect.render({
            el: 'menuSelect',
            data: menus_list,
            type: 'radio',
            line: false,
            checked: function (obj) {
                console.log(obj.obj.data)
                $('#parent_id').val(obj.obj.data.id);
            }
        })
        //监听提交
        form.on('submit(saveBtn)', function (data) {
            let url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
            common.asyncDoRequest(url, data.field);
            return false;
        });

        //表单赋值
        form.val("saveForm", {
            'name': item_info.name,
            "parent_id": item_info.parent_id,
            "parent_menu": item_info.parent_menu,
        });

    });
</script>