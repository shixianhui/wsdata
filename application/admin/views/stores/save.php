<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
    <table class="table_form" cellpadding="0" cellspacing="1">
        <caption>基本信息</caption>
        <tbody>
            <tr>
                <th width="20%">
                    <font color="red">*</font> <strong>选择会员</strong> <br/>
                </th>
                <td>
                    <button style="margin-left: 10px;" class="button_style" onclick="select_user();" type="button">点我选择
                    </button>
                    <input valid="required" errmsg="请选择会员!" type="hidden" id="user_id" name="user_id" value="<?php if(! empty($item_info)){ echo $item_info['user_id'];} ?>"/>
                    <span id="span_nickname"><?php if(! empty($item_info)){ echo $item_info['nickname'];} ?></span>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <font color="red">*</font> <strong>商户名称</strong> <br/>
                </th>
                <td>
                   <input valid="required" errmsg="商户名称不能为空!" name="store_name" id="store_name" value="<?php if (!empty($item_info)) { echo $item_info['store_name'];} ?>" size="80" type="text">
                </td>
            </tr>
            <tr>
                <th width="20%"><strong>商户分类</strong> <br/>
                </th>
                <td>
                <?php if (!empty($type_list)) { ?>
                            <?php
                            foreach ($type_list as $key => $value) {
                                $selector = '';
                                if ($item_info) {
                                    if ($item_info['type_id'] == $value['id']) {
                                        $selector = 'checked="checked"';
                                    }
                                }
                                ?>
                                <label><input type="radio" name="type_id" <?=$selector?> value="<?=$value['id']?>"> <?=$value['name']?></label>
                            <?php }} ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>城市</strong> <br/>
                </th>
                <td>
                   <?php if (!empty($item_info)) { echo $item_info['city'];} ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>地址</strong> <br/>
                </th>
                <td>
                   <?php if (!empty($item_info)) { echo $item_info['address'];} ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>图片</strong> <br/>
                </th>
                <td>
                   <?php if($image_list){ 
                       foreach($image_list as $value){?>
                       <a href="<?=base_url().$value['path']?>" target="_blank"><image src="<?=preg_replace('/\./', '_thumb.', $value['path'])?>" style="width: 60px;height: 60px;display:inline-block;"></image></a>
                       <?php }} ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>营业执照</strong> <br/>
                </th>
                <td>
                <?php if ($item_info && $item_info['license_image']) {?>
                <a href="<?=base_url().$item_info['license_image']?>" target="_blank"><image src="<?=preg_replace('/\./', '_thumb.', $item_info['license_image'])?>" style="width: 60px;height: 60px;display:inline-block;"></image></a>
                <?php } ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>许可证照</strong> <br/>
                </th>
                <td>
                <?php if ($item_info && $item_info['biz_lic_image']) {?>
                <a href="<?=base_url().$item_info['biz_lic_image']?>" target="_blank"><image src="<?=preg_replace('/\./', '_thumb.', $item_info['biz_lic_image'])?>" style="width: 60px;height: 60px;display:inline-block;"></image></a>
                <?php } ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>身份证照</strong> <br/>
                </th>
                <td>
                <?php if ($item_info && $item_info['id_card_image']) {?>
                <a href="<?=base_url().$item_info['id_card_image']?>" target="_blank"><image src="<?=preg_replace('/\./', '_thumb.', $item_info['id_card_image'])?>" style="width: 60px;height: 60px;display:inline-block;"></image></a>
                <?php } ?>
                </td>
            </tr>
            
            <tr>
                <th width="20%"><strong>身份证号</strong> <br/>
                </th>
                <td>
                    <?php if (!empty($item_info)) {echo $item_info['id_card_number'];} ?>
                </td>
            </tr>
            <tr>
                <th width="20%"><strong>银行卡号</strong> <br/>
                </th>
                <td>
                    <input name="bank_card_number" id="bank_card_number" value="<?php if (!empty($item_info)) {echo $item_info['bank_card_number'];} ?>" size="30" type="text">
                </td>
            </tr>
            <tr>
                <th width="20%"><strong>商家电话</strong> <br/>
                </th>
                <td>
                    <input name="phone" id="phone" value="<?php if (!empty($item_info)) {echo $item_info['phone'];} ?>" size="30" type="text">
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>营业时间</strong> <br/>
                </th>
                <td>
                   <?php if (!empty($item_info)) { echo $item_info['business_hours'];} ?>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <strong>补充说明</strong> <br/>
                </th>
                <td>
                   <?php if (!empty($item_info)) { echo $item_info['remark'];} ?>
                </td>
            </tr>
            <tr>
                <th width="20%"><strong>排序</strong> <br/>
                </th>
                <td>
                    <input name="sort" id="sort" value="<?php if (!empty($item_info)) {echo $item_info['sort'];} else {echo '0';} ?>" size="30" type="text">
                </td>
            </tr>
            <tr>
                <th width="20%"><strong>商户类型</strong> <br/>
                </th>
                <td>
                <?php if (!empty($store_type_arr)) { ?>
                            <?php
                            foreach ($store_type_arr as $key => $value) {
                                $selector = '';
                                if ($item_info) {
                                    if ($item_info['store_type'] == $key) {
                                        $selector = 'checked="checked"';
                                    }
                                }else{
                                    if($key == 0){
                                        $selector = 'checked="checked"';
                                    }
                                }
                                ?>
                                <label><input type="radio" name="store_type" <?=$selector?> value="<?=$key?>"> <?=$value?></label>
                            <?php }} ?>
                </td>
            </tr>
            <tr>
                <th width="20%"><strong>审核状态</strong> <br/>
                </th>
                <td>
                <?php if (!empty($status_arr)) { ?>
                            <?php
                            foreach ($status_arr as $key => $value) {
                                $selector = '';
                                if ($item_info) {
                                    if ($item_info['status'] == $key) {
                                        $selector = 'checked="checked"';
                                    }
                                }else{
                                    if($key == 0){
                                        $selector = 'checked="checked"';
                                    }
                                }
                                ?>
                                <label><input type="radio" name="status" <?=$selector?> value="<?=$key?>"> <?=$value?></label>
                            <?php }} ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
                    &nbsp;&nbsp; <input onclick="javascript:window.location.href = '<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
                </td>
            </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    function select_user() {
        window.open(base_url+"admincp.php/user/selector", "upload", "top=100, left=200, width=1000, height=400, scrollbars=1, resizable=yes");
    }

function upload_img(obj) {
     var formData = new FormData();
     formData.append('path_file', $(obj)[0].files[0]);
     formData.append('field', 'path_file');
     formData.append('model', 'store_type');
     $.ajax({
         type: "post",
         url:  base_url+'admincp.php/upload/uploadImage2',
         data: formData,
         dataType: 'json',
         processData: false, // 告诉jQuery不要去处理发送的数据
         contentType: false, // 告诉jQuery不要去设置Content-Type请求头
         xhrFields:{withCredentials:true},
         async: true,    //默认是true：异步，false：同步。
         success: function (res) {
             $(obj).parent().parent().find(".load").hide();
             if (res.success) {
                 $(obj).parent().parent().parent().parent().find("#path_src_a").attr("href", res.data.file_path);
                 $(obj).parent().parent().parent().parent().find("#path_src").attr("src", res.data.file_path.replace('.', '_thumb.')+"?"+res.data.field);
                 $(obj).parent().parent().next("input").attr("value", res.data.file_path);
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