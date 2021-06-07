<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
    <table class="table_form" cellpadding="0" cellspacing="1">
        <caption>基本信息</caption>
        <tbody>
            <tr style="display: none;">
                <th width="20%"><strong>上级分类</strong> <br/>
                </th>
                <td>
                    <select class="input_blur" name="parent_id" id="parent_id">
                        <option value="0" >请选择上级分类</option>
                        <?php if (!empty($item_list)) { ?>
                            <?php
                            foreach ($item_list as $key => $value) {
                                $selector = '';
                                if ($item_info) {
                                    if ($item_info['parent_id'] == $value['id']) {
                                        $selector = 'selected="selected"';
                                    }
                                } else {
                                    if ($value['id'] == $tmp_parent_id) {
                                        $selector = 'selected="selected"';
                                    }
                                }
                                ?>
                                <option <?php echo $selector; ?> value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                            <?php }} ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th width="20%">
                    <font color="red">*</font> <strong>分类名称</strong> <br/>
                </th>
                <td>
                   <input valid="required" errmsg="分类名称不能为空!" name="name" id="product_category_name" value="<?php if (!empty($item_info)) { echo $item_info['name'];} ?>" size="80" type="text">
                   <?php if (empty($item_info) || $tmp_parent_id){ ?>
                    <br/><font color="red">注：中间加“|”分隔符可以实现批量添加，格式如：“简餐便当|小吃炸串|地方菜系”</font>
                    <?php } ?>
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
                <th width="20%">
                    <strong>图片</strong>
                </th>
                <td>
                <a id="path_src_a" title="点击查看大图" href="<?php if ($item_info && $item_info['path']){echo $item_info['path'];}else{echo 'images/admin/no_pic.png';} ?>" target="_blank" style="float:left;"><img id="path_src" width="60px" src="<?php if ($item_info && $item_info['path']){echo preg_replace('/\./', '_thumb.', $item_info['path']);}else{echo 'images/admin/no_pic.png';} ?>" onerror="javascript:this.src='images/admin/no_pic.png';" /></a>

                <div style="float:left; margin-top:22px;">
                <a style=" position:relative; width:auto; " >
		        <span style="cursor:pointer;" class="but_4">上传照片<input style="left:0px;top:0px; background:#000; width:100%;height:36px;line-height:36px; position:absolute;filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" id="path_file" name="path_file" onchange="upload_img(this)"></span>
		        <i class="load" id="path_load" style="cursor:pointer;display:none;width:auto;padding-left:0px; left:50%; margin-left:-16px;"><img src="images/admin/loading_2.gif" width="32" height="32"></i>
		       </a>

		       <input value="<?php if ($item_info){echo $item_info['path'];} ?>" type="hidden" id="path" name="path">
		       <input name="model" id="model"  value="<?php echo $table; ?>" type="hidden" />
               <span id="cut_image" style="cursor:pointer;" class="but_4" onclick="cut_image('path')">裁剪图片</span>
               <?php $image_size_arr = get_image_size($table);
                     if ($image_size_arr) {
               ?>
               <span style="color:#9c9c9c;margin-left:30px;">注：缩略图大小＝<?php echo $image_size_arr['width']; ?>x<?php echo $image_size_arr['height']; ?></span>
               <?php } ?>
               </div>

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