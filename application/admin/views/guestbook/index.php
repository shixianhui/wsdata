<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c"><select class="input_blur" name="status" id="status">
<option value="">请选择处理状态</option>
<?php if ($status_arr) { ?>
<?php foreach ($status_arr as $key=>$value) { ?>
<option value="<?php echo $key ?>"><?php echo $value; ?></option>
<?php }} ?>
</select>
联系方式 <input class="input_blur" name="title" id="title" size="20" type="text">&nbsp;
留言时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;
    - <input class="input_blur" name="inputdate_end"
id="inputdate_end" size="10"  readonly="readonly" type="text">&nbsp;
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
<th width="200">联系方式</th>
<th>留言内容</th>
<th width="120">状态</th>
<th width="120">留言时间</th>
<th width="120">回复时间</th>
<th width="70">管理操作</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td>客户：<?php echo $value['contact_name']; ?><br/>
电话：<?php echo $value['phone']; ?><br/>
手机：<?php echo $value['mobile']; ?><br/>
QQ号：<?php echo $value['qq']; ?><br/>
邮箱：<?php echo $value['email']; ?><br/>
</td>
<td><?php echo $value['content']; ?></td>
<td class="align_c"><?php echo $status_arr[$value['status']]; ?></td>
<td class="align_c"><?php echo date("Y-m-d H:i", $value['add_time']); ?></td>
<td class="align_c"><?php echo date("Y-m-d H:i", $value['reply_time']); ?></td>
<td class="align_c"><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>">回复</a></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input class="button_style_red" name="delete" id="delete" value=" 删除 "  type="button">
</div>
<div id="pages">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>