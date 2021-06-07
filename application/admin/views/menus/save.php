<link rel="stylesheet" href="js/admin/lib/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
<style>
    body {
        background-color: #ffffff;
    }
    .hide {display: none;}
</style>
<div class="layui-form layuimini-form" lay-filter="saveForm">
    <form method="post" enctype="multipart/form-data">
        <div class="layui-form-item type-0 type-2">
            <label class="layui-form-label required">菜单类型</label>
            <div class="layui-input-block">
                <input type="radio" name="type" value="0" title="目录" lay-filter="typeRadio">
                <input type="radio" name="type" value="1" title="菜单" checked="" lay-filter="typeRadio">
                <input type="radio" name="type" value="2" title="按钮" lay-filter="typeRadio">
            </div>
        </div>
        <div class="layui-form-item type-2">
            <label class="layui-form-label">上级菜单</label>
            <div class="layui-input-block" style="width: 20%;">
                <input disabled type="text" name="parent_menu" placeholder="请选择上级菜单" value="" class="layui-input" id="menuSelect">
                <input type="hidden" name="parent_id" id="parent_id">
            </div>
        </div>
        <div class="layui-form-item type-0">
            <label for="" class="layui-form-label">菜单图标</label>
            <div class="layui-input-block">
                <input type="text" id="iconPicker" lay-filter="iconPicker" class="hide">
                <input type="hidden" name="icon" id="icon">
            </div>
        </div>
        <div class="layui-form-item type-0 type-2">
            <label class="layui-form-label required">菜单标题</label>
            <div class="layui-input-block">
                <input autocomplete="off" type="text" name="title" lay-verify="required" lay-reqtext="标题不能为空" placeholder="请输入菜单标题" value="" class="layui-input">
            </div>
        </div>
        
        <div class="layui-form-item type-2">
            <label class="layui-form-label required">组件名称</label>
            <div class="layui-input-block">
                <input type="text" name="component" placeholder="请输入组件名称" value="" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">菜单路由</label>
            <div class="layui-input-block">
                <input type="text" name="href" placeholder="请输入菜单路由" value="" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item type-2">
            <label class="layui-form-label required">权限标识</label>
            <div class="layui-input-block">
                <input type="text" name="permissions" placeholder="列表:index,新增:create,编辑:edit,删除:delete" value="" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item type-0">
            <label class="layui-form-label">菜单可见</label>
            <div class="layui-input-block">
                <input type="checkbox" name="status" lay-skin="switch" lay-text="显示|隐藏" value="1" checked>
            </div>
        </div>
        <div class="layui-form-item type-0 type-2">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" name="sort" value="" class="layui-input" min="0" max="9999" style="width: 5%;">
            </div>
        </div>
        <div class="layui-form-item type-0 type-2">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="saveBtn">确认保存</button>
            </div>
        </div>
    </form>
</div>
<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js?v=1.0.4" charset="utf-8"></script>
<script>
    var item_info = <?=$item_info_json?>;
    var menus_list = <?=$menus_list?>;
    layui.use(['form', 'iconPickerFa', 'customSelect', 'common'], function () {
        var form = layui.form,
            layer = layui.layer,
            iconPickerFa = layui.iconPickerFa,
            customSelect = layui.customSelect,
            common = layui.common;
            $ = layui.$;

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

        iconPickerFa.render({
            // 选择器，推荐使用input
            elem: '#iconPicker',
            // fa 图标接口
            url: "js/admin/lib/font-awesome-4.7.0/less/variables.less",
            // 是否开启搜索：true/false，默认true
            search: true,
            // 是否开启分页：true/false，默认true
            page: true,
            // 每页显示数量，默认12
            limit: 20,
            // 点击回调
            click: function (data) {
                $('#icon').val(data.icon);
            },
            // 渲染成功后的回调
            success: function (d) {
                // console.log(d);
            }
        });

        /**
         * 选中图标 （常用于更新时默认选中图标）
         * @param filter lay-filter
         * @param iconName 图标名称，自动识别fontClass/unicode
         */
        iconPickerFa.checkIcon('iconPicker', '<?=$item_info&&$item_info['icon'] ? $item_info['icon'] : 'fa-address-book'?>');

        //表单赋值
        form.val("saveForm", {
            "title": item_info.title,
            "component": item_info.component,
            "href": item_info.href,
            "permissions": item_info.permissions,
            "sort": $.isEmptyObject(item_info) ? 0 : item_info.sort,
            "status": $.isEmptyObject(item_info) || item_info.status == 1 ? true : false,
            "type": item_info.type ? item_info.type : 1,
            "parent_id": item_info.parent_id,
            "parent_menu": item_info.parent_menu,
            "icon": item_info.icon,
        });
        $(function() {
            if (!$.isEmptyObject(item_info)) {
                if (item_info.type == 0) {
                    $('.layui-form-item').hide();
                    $('.type-0').show();
                } else if(item_info.type == 1) {
                    $('.layui-form-item').show();
                } else if(item_info.type == 2) {
                    $('.layui-form-item').hide();
                    $('.type-2').show();

                }
            }
        })
        
        

        //监听提交
        form.on('submit(saveBtn)', function (data) {
            let url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
            common.asyncDoRequest(url, data.field);
            return false;
        });
        
        //监听radio单选
        form.on('radio(typeRadio)', function(data){
            if (data.value == 0) {
                $('.layui-form-item').hide();
                $('.type-0').show();
            } else if(data.value == 1) {
                $('.layui-form-item').show();
            } else if(data.value == 2) {
                $('.layui-form-item').hide();
                $('.type-2').show();

            }
        });
        

    });

    
</script>