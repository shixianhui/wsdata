<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<input name="id" value="" type="hidden">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>会员充值</caption>
 	<tbody>
 	<tr>
      <th width="20%"><strong>充值用户名</strong> <br/>
	  </th>
      <td>
      <input value="<?php if ($userInfo){ echo $userInfo['username'];} ?>" name="username" id="username" size="30" valid="required" errmsg="充值用户名不能为空!" class="input_blur" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>充值金额</strong> <br/>
	  </th>
      <td>
      <input name="price" id="price" size="10" valid="required" errmsg="充值金额不能为空!" class="input_blur" type="text">
      <font color="red">注:必须大于零</font>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>充值备注</strong> <br/>
	  </th>
      <td>
      <textarea valid="required" errmsg="充值备注不能为空!" name="remark" maxlength="400" id="remark" rows="4" cols="40"  class="textarea_style" style="width: 50%;" ></textarea>
    </td>
    </tr>
 	<tr>
      <td>
      &nbsp;
      </td>
      <td>
      <input class="button_style" name="dosubmit" value=" 会员充值 " type="submit" onclick="javascript:if(confirm('你确定要对用户进行充值吗,请认真核对资料？')){return true}else{return false};" >
	</td>
	</tr>
</tbody>
</table>
</form>