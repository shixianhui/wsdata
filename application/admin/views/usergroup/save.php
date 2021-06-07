<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>会员组名称</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="group_name" id="group_name" value="<?php if(! empty($itemInfo)){ echo $itemInfo['group_name'];} ?>" size="50" maxlength="50" valid="required" errmsg="管理组名称不能为空!" type="text">
	</td>
    </tr>
    
    <tr>
      <th width="20%">
      <font color="red">*</font> <strong>积分大于</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="score" id="score" value="<?php if(! empty($itemInfo)){ echo $itemInfo['score'];}else{echo '0';} ?>" size="50" maxlength="50" valid="isNumber" errmsg="积分必须是数字!" type="text">
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