<?php echo $tool; ?>
<form name="search" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">
ID号 <input class="input_blur" name="id" id="id" size="20" type="text">&nbsp;
用户名 <input class="input_blur" name="username" id="username" size="20" type="text">&nbsp;
昵称 <input class="input_blur" name="nick_name" id="nick_name" size="20" type="text">&nbsp;
<select class="input_blur" name="type" style="display: none">
<option value="">选择会员类型</option>
<?php if ($typeArr) { ?>
<?php foreach ($typeArr as $key=>$value) { ?>
<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php }} ?>
</select>&nbsp;
<select class="input_blur" name="display">
<option value="">选择状态</option>
<?php if ($displayArr) { ?>
<?php foreach ($displayArr as $key=>$value) {?>
<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php }} ?>
</select>
</select>
添加时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;<script language="javascript" type="text/javascript">
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
</tbody></table>
</form>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中(ID)</th>
<th>昵称</th>
<th width="100">姓名</th>
<th width="100">性别</th>
<!--<th width="100">会员种类</th>-->
<th width="100">手机</th>
<th width="100">所在地</th>
<th width="100">积分</th>
<th width="100">余额</th>
<th width="150">推荐人</th>
<th width="100">添加时间</th>
<th width="100">状态</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>" ><?php echo $value['nickname']; ?></a>
    <?php if ($value['is_id_card_auth']){ ?><font color="red">[实名认证]</font><?php } ?>
    <?php if ($value['type'] != '0'){ ?><font color="red">[VIP]</font><?php } ?>
</td>
<td class="align_c"><?php echo $value['real_name']; ?></td>
<td class="align_c"><?php echo $sexArr[$value['sex']]; ?></td>
<!--<td class="align_c">--><?php //echo $typeArr[$value['type']]; ?><!--</td>-->
<td class="align_c"><?php echo $value['mobile']; ?></td>
<td class="align_c"><?php echo $value['address']; ?></td>
<td class="align_c"><?php echo $value['score']; ?></td>
    <td class="align_c"><?php echo number_format($value['total'], 2, '.', ''); ?></td>
<td class="align_c"><?php if ($value['parent_id']){ echo $value['parent_name'].'[ID:'.$value['parent_id'].']'; } ?></td>
<td class="align_c"><?php echo date('Y-m-d H:i', $value['add_time']); ?></td>
<td class="align_c"><?php echo $value['display']?'已激活':'<font color="#FF0000">未激活</font>'; ?></td>
<td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>">修改</a></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style_red" name="delete" id="delete" value=" 删除 "  type="button">
<select class="input_blur" name="select_display" id="select_display" onchange="#">
<option value="">选择状态</option>
<?php if ($displayArr) { ?>
<?php foreach ($displayArr as $key=>$value) {?>
<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php }} ?>
</select>
</div>
<div id="pages">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>