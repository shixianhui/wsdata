<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>广告位置</strong> <br/>
	  </th>
      <td>
      <input name="select_category_id" id="select_category_id" type="hidden" value="<?php if(! empty($itemInfo)){ echo $itemInfo['category_id'];} ?>" >
      <select class="input_blur" name="category_id" id="category" valid="required" errmsg="请选择广告位置!">
       <option value="" >请选择广告位置</option>
       <?php if (! empty($adgroupList)): ?>
       <?php foreach ($adgroupList as $adgroup): ?>
       <option value="<?php echo $adgroup['id'] ?>" ><?php echo $adgroup['group_name'] ?></option>
       <?php endforeach; ?>
       <?php endif; ?>
      </select>
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>广告类型</strong> <br/>
	  </th>
      <td>
      <label>
      <input type="radio" value="image" name="ad_type" class="radio_style" <?php if (! empty($itemInfo)){if($itemInfo['ad_type']=='image'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?>> 图片广告
      </label>
      <label>
      <input type="radio" value="flash" name="ad_type" class="radio_style" <?php if (! empty($itemInfo)){if($itemInfo['ad_type']=='flash'){echo 'checked="checked"';}} ?>> Flash广告
	  </label>
	  <label>
	  <input type="radio" value="html" name="ad_type" class="radio_style" <?php if (! empty($itemInfo)){if($itemInfo['ad_type']=='html'){echo 'checked="checked"';}} ?>> Html广告
	  </label>
	  <label>
	  <input type="radio" value="text" name="ad_type" class="radio_style" <?php if (! empty($itemInfo)){if($itemInfo['ad_type']=='text'){echo 'checked="checked"';}} ?>> 文字广告
	  </label>
	</td>
    </tr>
    <tr id="tr_image_path">
      <th width="20%">
      <strong>广告图片</strong> <br/>
	  </th>
      <td>
    <input name="model" id="model"  value="<?php echo $template; ?>" type="hidden" />
    <input name="path" id="path" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['path'];} ?>" type="text" />
    <input class="button_style" name="upload_image" id="upload_image" value="上传图片" style="width: 60px;"  type="button" />  <input class="button_style" value="浏览..."
style="cursor: pointer;" name="select_image" id="select_image" type="button" /> <input class="button_style" style="cursor: pointer;"  name="cut_image" id="cut_image" value="裁剪图片" type="button"  />
    </td>
    </tr>
    <tr id="tr_content" style="display:none;">
      <th width="20%"> <strong>广告内容</strong>
      </th>
      <td>
<?php echo $this->load->view('element/ckeditor_tool', NULL, TRUE); ?>
<script id="content" name="content" type="text/plain" style="width:800px;height:300px;"><?php if(! empty($itemInfo)){ echo html($itemInfo["content"]);}else{echo "";} ?></script>
<script type="text/javascript">
    var ue = UE.getEditor('content');
</script>
      </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>广告大小</strong> <br/>
	  </th>
      <td>
     宽：<input class="input_blur" name="width" id="width" value="<?php if(! empty($itemInfo)){ echo $itemInfo['width'];}else{echo '0';} ?>" size="10" valid="isNumber" errmsg="宽只能是数字!" type="text"> <font color="red">px</font>
    高：<input class="input_blur" name="height" id="height" value="<?php if(! empty($itemInfo)){ echo $itemInfo['height'];}else{echo '0';} ?>" size="10" valid="isNumber" errmsg="高只能是数字!" type="text"> <font color="red">px</font>
	</td>
    </tr>
    <tr>
      <th width="20%">
      <strong>广告状态</strong> <br/>
	  </th>
      <td>
     <input type="radio" value="1" name="enable" class="radio_style" <?php if (! empty($itemInfo)){if($itemInfo['enable']=='1'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?> >开启
	 <input type="radio" value="0" name="enable" class="radio_style" <?php if (! empty($itemInfo)){if($itemInfo['enable']=='0'){echo 'checked="checked"';}} ?> >关闭
	  </td>
    </tr>
    <tr>
      <th width="20%">
      <strong>广告词</strong> <br/>
	  </th>
      <td>
      <input name="ad_text" id="ad_text" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['ad_text'];} ?>" type="text" />
    </td>
    </tr>
    <tr id="tr_image_path">
      <th width="20%">
      <strong>广告链接</strong> <br/>
	  </th>
      <td>
      <input name="url" id="url" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['url'];} ?>" type="text" />
    </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
</tbody></table>
</div>
</form>