<style>
    body {
        background-color: #ffffff;
    }
</style>
<div class="layui-form layuimini-form" lay-filter="saveForm">
    <div class="layui-form-item">
        <label class="layui-form-label required">物损大类</label>
        <div class="layui-input-inline">
            <select name="category" lay-verify="required" lay-reqtext="请选择物损分类" lay-search lay-filter="category">
                <option value="">请选择</option>
                <?php if ($category_list) {
                    foreach ($category_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item" id="category_1_div">
        <label class="layui-form-label">细分类别1</label>
        <div class="layui-input-inline">
            <select name="category_1" lay-reqtext="请选择细分类别1" lay-search lay-filter="category_1">
                <option value="">请选择</option>
                <?php if ($category_1_list) {
                    foreach ($category_1_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item" id="category_2_div">
        <label class="layui-form-label">细分类别2</label>
        <div class="layui-input-inline">
            <select name="category_2" lay-reqtext="请选择细分类别2" lay-search>
                <option value="">请选择</option>
                <?php if ($category_2_list) {
                    foreach ($category_2_list as $value) { ?>
                <option value="<?=$value['id']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">省份</label>
        <div class="layui-input-inline">
            <select name="province" lay-search lay-filter="province">
                <option value="">请选择</option>
                <?php if ($province_list) {
                    foreach ($province_list as $value) { ?>
                <option value="<?=$value['name']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
        <div class="layui-input-inline" id="city_div">
            <select name="city" lay-search>
                <option value="">请选择</option>
                <?php if ($city_list) {
                    foreach ($city_list as $value) { ?>
                <option value="<?=$value['name']?>"><?=$value['name']?></option>
                <?php }} ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">编号</label>
        <div class="layui-input-block">
            <input type="text" name="no" placeholder="请输入编号" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">品牌</label>
        <div class="layui-input-block">
            <input type="text" name="brand" placeholder="请输入品牌" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">项目名称</label>
        <div class="layui-input-block">
            <input type="text" name="project" placeholder="请输入项目名称" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">型号规格</label>
        <div class="layui-input-block">
            <input type="text" name="model" placeholder="请输入型号规格" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单位</label>
        <div class="layui-input-block">
            <input type="text" name="unit" placeholder="请输入单位" value="" class="layui-input">
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
            <input type="text" name="price" placeholder="请输入单价" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <input type="text" name="remark" placeholder="请输入备注" value="" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">案件来源</label>
        <div class="layui-input-block">
            <input type="text" name="source" placeholder="请输入案件来源（可以输入易赔订单号）" value="" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">图片</label>
        <div class="layui-input-block">
            <input type="hidden" name="path" id="path">
            <div class="layui-upload">
                <div class="layui-upload-list layui-inline" id="layer-photos">
                    <img class="layui-upload-img" id="image" layer-src="images/admin/no_pic.png" src="images/admin/no_pic.png" style="width: 100px;">
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
<script id="category" type="text/html">
    <option value="">请选择</option>
    {{#  layui.each(d.list, function(index, item){ }}
    <option value="{{ item.id }}">{{ item.name }}</option>
    {{#  }); }}
</script>
<script id="area" type="text/html">
    <option value="">请选择</option>
    {{#  layui.each(d.list, function(index, item){ }}
    <option value="{{ item.name }}">{{ item.name }}</option>
    {{#  }); }}
</script>
<script id="photos" type="text/html">
    {{#  layui.each(d.list, function(index, item){ }}
        <img class="layui-upload-img" id="image" layer-src="{{ item.path }}" src="{{ item.thumb }}" style="width: 100px;height:100px">
    {{#  }); }}
</script>
<script>
    layui.use(['common'], function () {
        var form = layui.form,
            common = layui.common,
            upload = layui.upload,
            element = layui.element,
            laytpl = layui.laytpl,
            $ = layui.$;

        var item_info = <?=$item_info_json?>;
        var photos_list = [];

        if (!$.isEmptyObject(item_info)) {
            // $('#image').attr('src', item_info.path);
            // $('#image').attr('layer-src', item_info.path);
            var data = { //数据
                "list": item_info.photos_list
            }
            var getTpl = photos.innerHTML;
            laytpl(getTpl).render(data, function(html){
                $('#layer-photos').html(html)
            });
        }

        layer.photos({
            photos: '#layer-photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型
        }); 
        //常规使用 - 普通图片上传
        var uploadInst = upload.render({
            elem: '#upload_image'
            ,url: base_url+'admincp.php/upload/uploadImageOss'
            ,accept: 'images'
            ,multiple: true
            ,data: {
                model: 'user',
                field: 'file',
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
            ,choose: function(obj) {
                // obj.preview(function(index, file, result){
                //     img_base64 = result; //图片链接（base64）
                //     console.log(img_base64)
                // });
            }
            ,done: function(res){
                if(res.success){
                    // $('#image').attr('src', res.data.file_path_thumb);
                    // $('#path').val(res.data.id);
                    let path = '';
                    photos_list.push(res.data)
                    photos_list.forEach(item => {
                        path += item.id+','
                    })
                    path = path.slice(0, -1)
                    $('#path').val(path);
                    var data = { //数据
                        "list": photos_list
                    }
                    var getTpl = photos.innerHTML;
                    laytpl(getTpl).render(data, function(html){
                        $('#layer-photos').html(html)
                    });
                    $('#upload_image').siblings('.progress').hide();
                    return layer.msg('上传成功');
                }
                $('#errorText').html(''); //置空上传失败的状态
            }
            ,error: function(){
                $('#upload_image').siblings('.progress').hide();
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

        //表单赋值
        form.val("saveForm", {
            'category': item_info.category,
            'category_1': item_info.category_1,
            'category_2': item_info.category_2,
            'province': item_info.province,
            'city': item_info.city,
            'brand': item_info.brand,
            'project': item_info.project,
            'model': item_info.model,
            'unit': item_info.unit,
            'num': item_info.num,
            'price': item_info.price,
            'remark': item_info.remark,
            'source': item_info.source,
            'path': item_info.img,
            'no': item_info.no,
        });

        form.on('select(category)', function(data){
            let id = data.value
            $.get('admincp.php/ws_data/getCategory/'+id,
                function(res){
                    if(res.success){
                        var data = { //数据
                            "list": res.data.item_list
                        }
                        var getTpl = category.innerHTML;
                        laytpl(getTpl).render(data, function(html){
                            $('#category_1_div select').html(html)
                            form.render('select');
                        });
						return false;
					}else{
						layer.msg(res.message);
						return false;
					}
                },
                "json"
            )
        }); 
        form.on('select(category_1)', function(data){
            let id = data.value
            $.get('admincp.php/ws_data/getCategory/'+id,
                function(res){
                    if(res.success){
                        var data = { //数据
                            "list": res.data.item_list
                        }
                        var getTpl = category.innerHTML;
                        laytpl(getTpl).render(data, function(html){
                            $('#category_2_div select').html(html)
                            form.render('select');
                        });
						return false;
					}else{
						layer.msg(res.message);
						return false;
					}
                },
                "json"
            )
        }); 

        form.on('select(province)', function(data){
            let val = data.value
            $.get('admincp.php/ws_data/getCity/'+val,
                function(res){
                    if(res.success){
                        var data = { //数据
                            "list": res.data.item_list
                        }
                        var getTpl = area.innerHTML;
                        laytpl(getTpl).render(data, function(html){
                            $('#city_div select').html(html)
                            form.render('select');
                        });
						return false;
					}else{
						layer.msg(res.message);
						return false;
					}
                },
                "json"
            )
        }); 

    });
</script>