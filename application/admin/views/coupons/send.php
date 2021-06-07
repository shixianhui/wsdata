<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
    <tr>
        <th width="20%">
            <strong>类型</strong> <br/>
        </th>
        <td>
            <label><input type="radio" class="radio_style" name="type" value="0" checked> 全体发放</label>
            <label><input type="radio" class="radio_style" name="type" value="1"> 单独发放</label>
        </td>
    </tr>
    <tr>
      <th width="20%">
          <strong>单独发放用户ID</strong> <br/>
	  </th>
      <td>
      <input name="user_ids" id="user_ids" size="80" type="text">
      <font color="red">*多个用户id用英文逗号‘,’相隔，如'1,2,3'</font>
	</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 发放 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
</tbody>
</table>
</form>