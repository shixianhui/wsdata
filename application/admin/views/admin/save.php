<style>
    body {
        background-color: #ffffff;
    }
</style>
<div class="layui-form layuimini-form" lay-filter="saveForm">
    <div class="layui-form-item">
        <label class="layui-form-label required">用户名</label>
        <div class="layui-input-block">
            <input type="text" name="username" lay-verify="required" lay-reqtext="用户名不能为空" placeholder="请输入用户名" value="" class="layui-input" autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">密码</label>
        <div class="layui-input-block">
            <input type="password" name="password" lay-verify="password" placeholder="请输入密码" value="" class="layui-input" autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">管理组</label>
        <div class="layui-input-inline">
            <select name="admin_group_id" lay-verify="required" lay-reqtext="请选择管理组" lay-search>
                <option value="">请选择</option>
                <?php if ($admin_group_list) {
                    foreach ($admin_group_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['group_name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">真实姓名</label>
        <div class="layui-input-block">
            <input type="text" name="real_name" placeholder="请输入真实姓名" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机</label>
        <div class="layui-input-block">
            <input type="number" name="mobile" placeholder="请输入手机号" value="" class="layui-input">
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
    layui.use(['common'], function () {
        var form = layui.form,
            common = layui.common,
            $ = layui.$;

        var item_info = <?=$item_info_json?>;
        //监听提交
        form.on('submit(saveBtn)', function (data) {
            let url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
            common.asyncDoRequest(url, data.field);
            return false;
        });

        form.verify({
            password: function (value, item) {
                if (item_info.length == 0) {
                    if (typeof(value) == "undefined" || value.trim() == "") {
                        return '密码不能为空';
                    }
                }
                if (value != "" && !/^[\S]{6,12}$/.test(value)) {
                    return '密码必须6到12位，且不能出现空格';
                }
            }
        });

        //表单赋值
        form.val("saveForm", {
            'admin_group_id': item_info.admin_group_id,
            'username': item_info.username,
            'real_name': item_info.real_name,
            'mobile': item_info.mobile,
        });

    });
</script>