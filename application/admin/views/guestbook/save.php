<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<input name="id" value="" type="hidden">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody> 	
	<tr>
      <th width="20%"><strong>客户</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo $itemInfo['contact_name'];} ?>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>电话</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo $itemInfo['phone'];} ?>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>手机</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo $itemInfo['mobile'];} ?>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>QQ号</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo $itemInfo['qq'];} ?>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>邮箱</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo $itemInfo['email'];} ?>
	</td>
    </tr>
     <tr>
      <th width="20%"><strong>客户留言</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo str_replace(array("\r\n", "\n", "\r"), '<br/>', $itemInfo['content']);} ?>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>留言时间</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($itemInfo)){ echo date('Y-m-d H:i', $itemInfo['add_time']);} ?>
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>状态</strong> <br/>
	  </th>
      <td>
      <?php echo $status_arr[$itemInfo['status']]; ?>
      <select class="input_blur" name="status" id="status" valid="required" errmsg="请选择处理状态!">
       <option value="">请选择处理状态</option>
       <?php if($status_arr) { ?>
       <?php foreach ($status_arr as $key=>$value) {
                 $selector = '';
                 if ($key > 0) {
                 if ($itemInfo) {
                     if ($itemInfo['status'] == $key) {
                         $selector = 'selected="selected"';
                     }
                 }
                 
           ?>
       <option <?php echo $selector; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
       <?php }}} ?>
      </select>
	</td>
    </tr>
	<tr>
      <th width="20%"> <strong>备注</strong> <br/>
	  </th>
      <td>
      <textarea name="remark" maxlength="400" id="remark" rows="4" cols="60"  class="textarea_style"><?php if(! empty($itemInfo)){ echo $itemInfo['remark'];} ?></textarea>
    </td>
    </tr>
	<tr>
      <th width="20%"> <strong>回复时间</strong> <br/>
	  </th>
      <td>
	<input class="input_blur" name="reply_time" id="reply_time"  size="21" readonly="readonly" type="text"/>&nbsp;
	<script language="javascript" type="text/javascript">
	    datetime = "<?php if(! empty($itemInfo) && $itemInfo['reply_time']){ echo date('Y-m-d H:i:s', $itemInfo['reply_time']);}else{echo date('Y-m-d H:i:s', time());} ?>";
		date = new Date();
		if (datetime == "" || datetime == null) {
			datetime = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
		}
		document.getElementById ("reply_time").value =datetime;
		Calendar.setup({
			inputField     :    "reply_time",
			ifFormat       :    "%Y-%m-%d %H:%M:%S",
			showsTime      :    true,
			timeFormat     :    "24",
			align          :    "Tr"
		});
	</script>
	 </td>
    </tr>
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