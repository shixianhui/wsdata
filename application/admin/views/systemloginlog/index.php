<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">
用户名 <input class="input_blur" name="username" id="username" size="20" type="text">&nbsp;
IP <input class="input_blur" name="ip" id="ip" size="20" type="text">&nbsp;
IP地址 <input class="input_blur" name="address" id="address" size="20" type="text">&nbsp;
登录时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;<script language="javascript" type="text/javascript">
					date = new Date();
					Calendar.setup({
						inputField     :    "inputdate_start",
						ifFormat       :    "%Y-%m-%d",
						showsTime      :    false,
						timeFormat     :    "24"
					});
				 </script> - <input class="input_blur" name="inputdate_end"
id="inputdate_end" size="10"  readonly="readonly" type="text">&nbsp;<script language="javascript" type="text/javascript">
					date = new Date();
					Calendar.setup({
						inputField     :    "inputdate_end",
						ifFormat       :    "%Y-%m-%d",
						showsTime      :    false,
						timeFormat     :    "24"
					});
				 </script>&nbsp;
<input class="button_style" name="dosubmit" value=" 查询 " type="submit">
</td>
</tr>
</tbody>
</table>
</form>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中</th>
<th>管理员账号</th>
<th width="200">登录IP</th>
<th width="200">登录地址</th>
<th width="200">登录时间</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
<td><?php echo $value['id']; ?></td>
<td><?php echo $value['username'] ?></td>
<td class="align_c"><?php echo $value['ip'] ?></td>
<td class="align_c"><?php echo $value['address']; ?></td>
<td class="align_c"><?php echo date("Y-m-d H:i:s", $value['add_time']); ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div id="pages" style="margin-top: 5px;">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>