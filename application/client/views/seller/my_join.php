<style>
    .b_form li {
        margin-top:15px;
    }
    .b_form .img_box .img{
        width:80px;height:60px;margin-right: 5px;
    }
    .b_form .img_box{
        padding-top:10px;display:inline-block;padding-right:10px;
    }
    .b_form .input_box{
        display:inline-block;width:65px;height:48px;position:relative;
    }
    .b_form .input_box .file_input{
        left:0px;top:0px; background:#000; width:65px;height:48px; position:absolute;filter:alpha(opacity=0);-moz-opacity:0;opacity:0;
    }
</style>
<div class="mt20" style="width: 1200px;margin:0 auto;">
    <div class="box_shadow clearfix m_border">
        <div class="b_title">
            <div class="bd">
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="jsonForm" id="jsonForm">
                <ul class="b_form">
                    <li class="clearfix">
                        <span>上传门头图：</span>
                        <div id="img_box" class="img_box">
                            <?php if ($image_list) {
                                foreach ($image_list as $key => $value) {
                                    ?>
                            <img src="<?=preg_replace('/\./', '_thumb.', $value['path'])?>" class="img">
                            <?php }} ?>
                        </div>
                        <div class="input_box">
                            <img src="images/default/upload_image.png" id="path_load">
                            <input class="file_input" type="file" accept="image/*" id="path" name="path" onchange="multiple_img_upload(this, 'path')" multiple="multiple">
                        </div>
                        <font color="red" style="font-size: 14px;">*建议现场拍摄商户正门入口，招牌，最多3张</font>
                        <input type="hidden" name="image_ids" id="image_ids" value="<?php if($item_info){ echo $item_info['image_ids'];}?>">
                        <input type="hidden" name="path_model" id="path_model" value="store">
                    </li>
                    <li class="clearfix">
                        <span>上传营业执照：</span>
                        <div id="license_img_box" class="img_box">
                            <?php if (!empty($item_info) && $item_info['license_image']) {
                                    ?>
                            <img src="<?=preg_replace('/\./', '_thumb.', $item_info['license_image'])?>" class="img">
                            <?php } ?>
                        </div>
                        <div class="input_box">
                            <img src="images/default/upload_image.png" id="path_load">
                            <input class="file_input" type="file" accept="image/*" id="license" name="license" onchange="img_upload(this, 'license')">
                        </div>
                        <input type="hidden" name="license_path" id="license_path" value="<?php if($item_info){ echo $item_info['license_image'];}?>">
                        <input type="hidden" name="license_model" id="license_model" value="store">
                    </li>
                    <Li><span>商户名：</span><input value="<?php if ($item_info) {echo $item_info['store_name'];} ?>" type="text" id="store_name" name="store_name" class="input_1" valid="required" errmsg="店铺名称不能为空" maxlength="100">
                        <font>*</font>
                    </Li>
                    <li style="font-size: 14px;line-height: 36px;">
                    <span>是否为分店：</span>
                    <label><input name="is_branch" value="0" type="radio" size="10" <?php if(empty($item_info) || $item_info['is_branch'] == 0){ echo 'checked="checked"';} ?>> 否</label>
                    <label><input name="is_branch" value="1" type="radio" size="10" <?php if(!empty($item_info) && $item_info['is_branch'] == 1){ echo 'checked="checked"';} ?>> 是</label>
                    </li>
                    <li style="font-size: 14px;line-height: 36px;">
                    <span>商户类型：</span>
                    <?php if ($store_type_list) { 
                        foreach ($store_type_list as $value) {?>
                    <label><input name="type_id" value="<?=$value['id']?>" type="radio" size="10" <?php if(!empty($item_info) && $item_info['type_id'] == $value['id']){ echo 'checked="checked"';} ?>> <?=$value['name']?></label>
                    <?php }} ?>
                    <font>*</font>
                    </li>
                    <Li><span>所在地区：</span>
                        <div class="b_select">
                                   <div class="dropdown">
                                            <select id="province_id" name="province_id" onchange="javascript:get_city('province_id','city_id',0,0,1);" valid="required" errmsg="请选择省">
                                                <option value="">-省份-</option>
							                  <?php if ($province_list) { ?>
								              <?php foreach ($province_list as $province) {
									              	$selector = '';
									              	if ($item_info) {
									              		if ($item_info['province_id'] == $province['id']) {
									              			$selector = 'selected="selected"';
									              		}
									              	}
								              	?>
								              <option <?php echo $selector; ?> value="<?php echo $province['id']; ?>"><?php echo $province['name']; ?></option>
								              <?php }} ?>
                                            </select>
                                        </div>
                        </div>
                        <div class="b_select">
                                    <div class="dropdown">
                                            <select id="city_id" name="city_id"  valid="required" errmsg="请选择市" onchange="javascript:get_city('city_id','area_id',0,0,0);">
                                                <option value="">-市/县-</option>
                                            </select>
                                        </div>
                        </div>
                        <!-- <div class="b_select">
                            <div class="dropdown">
                                            <select id="area_id" name="area_id" valid="required" errmsg="请选择区">
                                                <option value="">-区-</option>
                                            </select>
                              </div>
                        </div> -->
                        <font>*</font>
                        <div style="margin-top:20px;"><span>详细地址：</span><input value="<?php if ($item_info) {echo $item_info['address'];} ?>" type="text" class="input_1" id="address" name="address" style="width:560px;" maxlength="255"><font>*</font></div>
                    </Li>
                    <Li><span>营业时间：</span><input value="<?php if ($item_info) {echo $item_info['business_hours'];} ?>" type="text" id="business_hours" name="business_hours" class="input_1">
                    </Li>
                    <Li><span>商家电话：</span><input value="<?php if ($item_info) {echo $item_info['phone'];} ?>" type="text" id="phone" name="phone" class="input_1">
                    </Li>
                    <Li><span>银行卡号：</span><input value="<?php if ($item_info) {echo $item_info['bank_card_number'];} ?>" type="text" id="bank_card_number" name="bank_card_number" class="input_1">
                    </Li>
                    <Li><span>补充说明：</span><textarea name="remark" id="remark" cols="" rows="" class="textarea_1" placeholder="请提供更多参考性信息帮助我们核实，如周边标志性建筑，近邻商户等"><?php if ($item_info) {echo $item_info['remark'];} ?></textarea></Li>

                    <Li><span></span>
                        <a href="javascript:void(0)" onclick="$('#jsonForm').submit();" class="b_btn"><?php if ($item_info && $item_info['status'] == 2) {echo '重新提交店铺申请';}else{echo '提交店铺申请';} ?></a>
                    </Li>
                </ul>
                 </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	<?php if ($item_info) { ?>
	get_city('province_id', 'city_id', '<?php echo $item_info['city_id']; ?>', '<?php echo $item_info['province_id']; ?>', 1);
    <?php } ?>
    
    function multiple_img_upload(obj, filed) {
        var formData = new FormData();
        // formData.append(filed, $(obj)[0].files[0]);
        for (var i = 0; i < $(obj)[0].files.length; i++) {
            formData.append(filed+'[]', $(obj)[0].files[i], $(obj)[0].files[i].name);
        }
        formData.append('field', filed);
        var model = $('#'+ filed + '_model').val();
        formData.append('model', model);
        var pic_num = $('#img_box img').length;
        formData.append('pic_num', pic_num);
        formData.append('max_pic_num', 3);
        $.ajax({
            type: "post",
            url:  base_url+'index.php/upload/batch_uploadImage',
            data: formData,
            dataType: 'json',
            processData: false, // 告诉jQuery不要去处理发送的数据
            contentType: false, // 告诉jQuery不要去设置Content-Type请求头
            xhrFields:{withCredentials:true},
            async: true,    //默认是true：异步，false：同步。
            success: function (res) {
                $(obj).parent().parent().find(".load").hide();
                if (res.success) {
                    var html = '';
                    for(var i = 0; i < res.data.length; i++) {
                        html += '<img src="'+res.data[i].file_path.replace('.', '_thumb.')+'" class="img">';
                        $('#image_ids').val($('#image_ids').val()+res.data[i].id+'_');
                    }
                    $('#img_box').html(html);
                }else{
                    my_alert('',0,res.message);
                }
            },
            error: function (data) {
                my_alert('',0,'请求异常');
            }
        });

    }

    function img_upload(obj, filed) {
        var formData = new FormData();
        formData.append(filed, $(obj)[0].files[0]);
        formData.append('field', filed);
        var model = $('#'+ filed + '_model').val();
        formData.append('model', model);
        $.ajax({
            type: "post",
            url:  base_url+'index.php/upload/uploadImage',
            data: formData,
            dataType: 'json',
            processData: false, // 告诉jQuery不要去处理发送的数据
            contentType: false, // 告诉jQuery不要去设置Content-Type请求头
            xhrFields:{withCredentials:true},
            async: true,    //默认是true：异步，false：同步。
            success: function (res) {
                $(obj).parent().parent().find(".load").hide();
                if (res.success) {
                    var html = '<img src="'+res.data.file_path.replace('.', '_thumb.')+'" class="img">';
                    $('#'+filed+'_path').val(res.data.file_path);
                    $('#'+filed+'_img_box').html(html);
                }else{
                    my_alert('',0,res.message);
                }
            },
            error: function (data) {
                my_alert('',0,'请求异常');
            }
        });

    }
</script>