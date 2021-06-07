<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<input name="id" value="" type="hidden">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
 	<tr>
      <th width="25%">
      <strong>上传的图片是否使用图片水印功能</strong> <br/>
	  </th>
      <td>
      <input type="radio" value="0" name="is_open" class="radio_style" <?php if($itemInfo){if($itemInfo['is_open']=='0'){echo 'checked="checked"';}}else{echo 'checked="checked"';} ?> > 关闭
      <input type="radio" value="1" name="is_open" class="radio_style" <?php if($itemInfo){if($itemInfo['is_open']=='1'){echo 'checked="checked"';}} ?> > 开启
	</td>
	</tr>   
	<tr>
      <th width="25%">
      <strong>水印图片文件路径</strong> <br/>
	  </th>
      <td>
    <input name="model" id="model"  value="watermark" type="hidden" />
    <input name="path" id="path" size="50" class="input_blur" value="<?php if($itemInfo){echo $itemInfo['path'];} ?>" type="text" />
    <input class="button_style" name="upload_image" id="upload_image" value="上传图片" style="width: 60px;"  type="button" />  <input class="button_style" value="浏览..."
style="cursor: pointer;" name="select_image" id="select_image" type="button" /> <input class="button_style" style="cursor: pointer;"  name="cut_image" id="cut_image" value="裁剪图片" type="button"  />
    </td>
    </tr>
    <tr>
      <th width="25%"> <strong>水印位置：</strong>
	  </th>
      <td>
	     <table width="300" cellspacing="0" cellpadding="0" border="1">
	      <tbody>
	      <tr>
	        <td width="33%"><input type="radio" value="top,left" name="location" <?php if($itemInfo){if($itemInfo['location']=='top,left'){echo 'checked="checked"';}} ?> class="np"> 顶部居左</td>
	        <td width="33%"><input type="radio" value="top,center" name="location" <?php if($itemInfo){if($itemInfo['location']=='top,center'){echo 'checked="checked"';}} ?> class="np"> 顶部居中</td>
	        <td><input type="radio" value="3" name="top,right" <?php if($itemInfo){if($itemInfo['location']=='top,right'){echo 'checked="checked"';}} ?> class="np"> 顶部居右</td>
	      </tr>
	      <tr>
	        <td><input type="radio" value="middle,left" name="location" <?php if($itemInfo){if($itemInfo['location']=='middle,left'){echo 'checked="checked"';}} ?>  class="np"> 左边居中</td>
	        <td><input type="radio" value="middle,center" name="location" <?php if($itemInfo){if($itemInfo['location']=='middle,center'){echo 'checked="checked"';}} ?>  class="np"> 图片中心</td>
	        <td><input type="radio" value="middle,right" name="location" <?php if($itemInfo){if($itemInfo['location']=='middle,right'){echo 'checked="checked"';}} ?>  class="np"> 右边居中</td>
	      </tr>
	      <tr>
	        <td><input type="radio" value="bottom,left" name="location" <?php if($itemInfo){if($itemInfo['location']=='bottom,left'){echo 'checked="checked"';}} ?>  class="np"> 底部居左</td>
	        <td><input type="radio" value="bottom,center" name="location" <?php if($itemInfo){if($itemInfo['location']=='bottom,center'){echo 'checked="checked"';}} ?>  class="np"> 底部居中</td>
	        <td><input type="radio" value="bottom,right" class="np" <?php if($itemInfo){if($itemInfo['location']=='bottom,right'){echo 'checked="checked"';}} ?>  name="location"> 底部居右</td>
	      </tr>
	     </tbody>
	    </table>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input class="button_style_red" name="reset" value=" 重置 " type="reset">
	  </td>
    </tr>
</tbody>
</table>
</form>