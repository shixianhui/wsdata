<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>用户名</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="username" id="username" value="<?php if(! empty($itemInfo)){ echo $itemInfo['username'];} ?>" <?php if(! empty($itemInfo)){ echo 'readonly="true"';} ?> size="50" maxlength="50" valid="required" errmsg="用户名不能为空!" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <font color="red">*</font> <strong>密&nbsp;&nbsp;&nbsp;码</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="password" id="password" value="" size="50" maxlength="50" <?php if(empty($itemInfo)){ echo 'valid="required" errmsg="密码不能为空!"';} ?> type="password">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <font color="red">*</font> <strong>确认密码</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="ref_password" id="ref_password" value="" size="50" maxlength="50" valid="eqaul" eqaulName="password" errmsg="前后密码不一致!" type="password">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>会员类型</strong> <br/>
	  </th>
      <td>
          <?php if ($typeArr){
              foreach ($typeArr as $key=>$value){ ?>
      <input type="checkbox" value="<?php echo $key; ?>" name="type[]" class="radio_style" <?php if($itemInfo){if(strpos($itemInfo['type'],"$key") !== false){echo 'checked="checked"';}}else{echo $key == 0 ? 'checked="checked"' : '';} ?> > <?php echo $value; ?>
          <?php }} ?>
	</td>
    </tr>
    <tr>
    <th width="20%"> <strong>头像</strong> <br/>
	</th>
    <td>
    <input name="model" id="model"  value="<?php echo $template; ?>" type="hidden" />
    <input name="path" id="path" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['path'];} ?>" type="text" />
    <input class="button_style" name="upload_image" id="upload_image" value="上传图片" style="width: 60px;"  type="button" />  <input class="button_style" value="浏览..."
style="cursor: pointer;" name="select_image" id="select_image" type="button" /> <input class="button_style" style="cursor: pointer;"  name="cut_image" id="cut_image" value="裁剪图片" type="button"  />
    </td>
    </tr>
    <tr>
        <th width="20%"> <strong>昵称</strong> <br/>
        </th>
        <td>
            <input class="input_blur" name="nickname" id="nickname" value="<?php if(! empty($itemInfo)){ echo $itemInfo['nickname'];} ?>" size="50" type="text">
        </td>
    </tr>
    <tr>
      <th width="20%"> <strong>姓名</strong> <br/>
	  </th>
      <td>
     <input class="input_blur" name="real_name" id="real_name" value="<?php if(! empty($itemInfo)){ echo $itemInfo['real_name'];} ?>" size="50" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>性别</strong> <br/>
	  </th>
      <td>
      <input type="radio" value="0" name="sex" class="radio_style" <?php if($itemInfo){if($itemInfo['sex']=='0'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?> > 未知
      <input type="radio" value="1" name="sex" class="radio_style" <?php if($itemInfo){if($itemInfo['sex']=='1'){echo 'checked="checked"';}} ?> > 男
      <input type="radio" value="2" name="sex" class="radio_style" <?php if($itemInfo){if($itemInfo['sex']=='2'){echo 'checked="checked"';}} ?> > 女
	</td>
	</tr>
<!--    <tr>-->
<!--      <th width="20%"><font color="red">*</font> <strong>QQ号</strong> <br/>-->
<!--	  </th>-->
<!--      <td>-->
<!--     <input class="input_blur" name="qq" id="qq" value="--><?php //if(! empty($itemInfo)){ echo $itemInfo['qq'];} ?><!--" size="50" valid="required|isQQ" errmsg="QQ号不能为空!|QQ号格式错误!" type="text">-->
<!--	</td>-->
<!--    </tr>-->
     <tr>
      <th width="20%"><font color="red">*</font> <strong>手机</strong> <br/>
	  </th>
      <td>
     <input valid="required" errmsg="手机号码不能为空!" class="input_blur" name="mobile" id="mobile" value="<?php if(! empty($itemInfo)){ echo $itemInfo['mobile'];} ?>" size="50" type="text">
	</td>
    </tr>

    <tr>
        <th width="20%"> <strong>所在地</strong> <br/>
        </th>
        <td>
            <select class="input_blur" id="province_id" name="province_id" onchange="javascript:get_city('province_id','city_id',0,0,1);">
                <option value="">选择省</option>
                <?php if ($area_list) { ?>
                    <?php foreach ($area_list as $key=>$value) {
                        $selector = '';
                        if ($itemInfo) {
                            if ($itemInfo['province_id'] == $value['id']) {
                                $selector = 'selected="selected"';
                            }
                        }
                        ?>
                        <option <?php echo $selector; ?> value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                    <?php }} ?>
            </select>
            <select class="input_blur" id="city_id" name="city_id" onchange="javascript:get_city('city_id','area_id',0,0,2);">
                <option>选择市</option>
            </select>
            <select onchange="javascript:change_area();" class="input_blur" id="area_id" name="area_id">
                <option>选择区/县</option>
            </select>
        </td>
    </tr>

    <tr>
      <th width="20%"><strong>积分</strong> <br/>
	  </th>
      <td>
     <input class="input_blur" name="score" id="score" value="<?php if(! empty($itemInfo)){ echo $itemInfo['score'];}else{echo '0';} ?>" size="50" type="text">
	</td>
    </tr>
    <?php if (! empty($itemInfo)){ ?>
        <tr>
            <th width="20%"><font color="red">*</font> <strong>身份证号</strong> <br/>
            </th>
            <td>
                <input class="input_blur" name="id_card" id="id_card" value="<?php if(! empty($itemInfo)){ echo $itemInfo['id_card'];} ?>" size="50" type="text">
            </td>
        </tr>
        <?php if ($attachment_list){?>
            <tr>
                <th width="20%"><strong>身份证照片</strong> <br/>
                </th>
                <td>
                    <?php foreach ($attachment_list as $item){ ?>
                    <a href="<?php echo $item['path']; ?>"><img src="<?php echo preg_replace('/\./', '_thumb.', $item['path']); ?>"></a>
                <?php } ?>
                </td>
            </tr>
            <?php } ?>
    <tr>
        <th width="20%"><strong>账户余额</strong> <br/>
        </th>
        <td>
            <?php echo $itemInfo['total']; ?>
        </td>
    </tr>

        <tr>
            <th width="20%"><strong>添加时间</strong> <br/>
            </th>
            <td>
                <?php echo date('Y-m-d H:i:s',$itemInfo['add_time']); ?>
            </td>
        </tr>
    <?php } ?>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
</tbody>
</table>
</form>
<script>
    function change_area() {
        //国 省 市 县
        var province_id_txt = $("#province_id").find("option:selected").text();
        var city_id_txt = $("#city_id").find("option:selected").text();
        var area_id_txt = $("#area_id").find("option:selected").text();
        $("#txt_address").val(province_id_txt+' '+city_id_txt+' '+area_id_txt);
    }

    function get_city(cur_id, next_id, next_select_val, prev_select_val, is_city) {
        var parent_id = $("#"+cur_id).val();
        if (prev_select_val) {
            parent_id = prev_select_val;
        }
        $.post(base_url+"admincp.php/user/get_city",
            {	"parent_id": parent_id
            },
            function(res){
                if(res.success){
                    var html = '';
                    if (is_city == 1) {
                        html = '<option value="">--选择市--</option>';
                    } else if (is_city == 2) {
                        html = '<option value="">--选择区/县--</option>';
                    }
                    for (var i = 0, data = res.data, len = data.length; i < len; i++){
                        if (data[i]['id'] == next_select_val) {
                            html += '<option selected="selected" value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
                        } else {
                            html += '<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
                        }
                    }
                    $("#"+next_id).html(html);
                    return false;
                }else{
                    alert(res.message);
                    return false;
                }
            },
            "json"
        );
    }
    <?php if ($itemInfo) { ?>
    get_city('province_id','city_id',<?php echo $itemInfo['city_id']; ?>,<?php echo $itemInfo['province_id']; ?>,1);
    get_city('city_id','area_id',<?php echo $itemInfo['area_id']; ?>,<?php echo $itemInfo['city_id']; ?>,2);
    <?php } ?>
</script>