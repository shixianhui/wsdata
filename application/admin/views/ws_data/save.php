<style>
    body {
        background-color: #ffffff;
    }
</style>
<div class="layui-form layuimini-form" lay-filter="saveForm">
    <div class="layui-form-item">
        <label class="layui-form-label required">物损大类</label>
        <div class="layui-input-inline">
            <select name="admin_group_id" lay-verify="required" lay-reqtext="请选择物损分类" lay-search>
                <option value="">请选择</option>
                <?php if ($category_list) {
                    foreach ($category_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">细分类别1</label>
        <div class="layui-input-inline">
            <select name="admin_group_id" lay-verify="required" lay-reqtext="请选择细分类别1" lay-search>
                <option value="">请选择</option>
                <?php if ($admin_group_list) {
                    foreach ($admin_group_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['group_name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">细分类别2</label>
        <div class="layui-input-inline">
            <select name="admin_group_id" lay-verify="required" lay-reqtext="请选择细分类别2" lay-search>
                <option value="">请选择</option>
                <?php if ($admin_group_list) {
                    foreach ($admin_group_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['group_name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label required">省份</label>
        <div class="layui-input-inline">
            <select name="admin_group_id" lay-verify="required" lay-reqtext="请选择省份" lay-search>
                <option value="">请选择</option>
                <?php if ($province_list) {
                    foreach ($province_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
        <div class="layui-input-inline">
            <select name="admin_group_id" lay-verify="required" lay-reqtext="请选择地市" lay-search>
                <option value="">请选择</option>
                <?php if ($province_list) {
                    foreach ($province_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">品牌</label>
        <div class="layui-input-block">
            <input type="number" name="brand" placeholder="请输入品牌" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">项目名称</label>
        <div class="layui-input-block">
            <input type="number" name="project" placeholder="请输入项目名称" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">型号规格</label>
        <div class="layui-input-block">
            <input type="number" name="model" placeholder="请输入型号规格" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单位</label>
        <div class="layui-input-block">
            <input type="number" name="unit" placeholder="请输入单位" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">数量</label>
        <div class="layui-input-block">
            <input type="number" name="num" placeholder="请输入数量" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单价</label>
        <div class="layui-input-block">
            <input type="number" name="price" placeholder="请输入单价" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <input type="number" name="remark" placeholder="请输入备注" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">案件来源</label>
        <div class="layui-input-block">
            <input type="number" name="source" placeholder="请输入案件来源（可以输入易赔订单号）" value="" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">图片</label>
        <div class="layui-input-block">
            <input type="hidden" name="path" id="path">
            <div class="layui-upload">
                <div class="layui-upload-list layui-inline" id="layer-photos">
                    <img class="layui-upload-img" id="image" lay-src="images/admin/no_pic.png" src="images/admin/no_pic.png" style="width: 100px;">
                    <p id="errorText"></p>
                </div>
                <button type="button" class="layui-btn layui-inline" id="upload_image">上传图片</button>
                <div style="width: 95px;display:none;" class="progress layui-inline">
                    <div class="layui-progress layui-progress-big" lay-showpercent="yes" lay-filter="progress">
                        <div class="layui-progress-bar" lay-percent=""></div>
                    </div>
                </div>
            </div>  
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
            upload = layui.upload,
            element = layui.element,
            $ = layui.$;

        var item_info = <?=$item_info_json?>;

        if (!$.isEmptyObject(item_info)) {
            $('#image').attr('src', item_info.path.replace('.', '_thumb.'));
            $('#image').attr('layer-src', item_info.path);
        }

        layer.photos({
            photos: '#layer-photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型
        }); 
        //常规使用 - 普通图片上传
        var uploadInst = upload.render({
            elem: '#upload_image'
            ,url: base_url+'admincp.php/upload/uploadImage2'
            ,accept: 'images'
            ,data: {
                model: 'user',
                field: 'file'
            }
            ,field: 'file'
            ,before: function(obj){
                $('#upload_image').siblings('.progress').show();
            //预读本地文件示例，不支持ie8
            // obj.preview(function(index, file, result){
            //     $('#demo1').attr('src', result); //图片链接（base64）
            // });
            
                element.progress('progress', '0%'); //进度条复位
                layer.msg('上传中', {icon: 16, time: 0});
            }
            ,done: function(res){
                if(res.success){
                    $('#image').attr('src', res.data.file_path.replace('.', '_thumb.')+"?"+res.data.field);
                    $('#path').val(res.data.file_path);
                    $('#upload_image').siblings('.progress').hide();
                    return layer.msg('上传成功');
                }
                $('#errorText').html(''); //置空上传失败的状态
            }
            ,error: function(){
            //演示失败状态，并实现重传
            var errorText = $('#errorText');
            errorText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
            errorText.find('.demo-reload').on('click', function(){
                uploadInst.upload();
            });
            }
            //进度条
            ,progress: function(n, elem, e){
            element.progress('progress', n + '%'); //可配合 layui 进度条元素使用
            if(n == 100){
                layer.msg('上传完毕', {icon: 1});
            }
            }
        });

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
            },
            confirmPass:function(value){
                if($('input[name=password]').val() !== value)
                    return '两次密码输入不一致！';
            }

        });

        //表单赋值
        form.val("saveForm", {
            'username': item_info.username,
            'real_name': item_info.real_name,
            'mobile': item_info.mobile,
            'nickname': item_info.nickname,
            'path': item_info.path,
            'sex': item_info.sex ? item_info.sex : 1,
            'status': item_info.status ? item_info.status : 1,
        });

    });
</script>