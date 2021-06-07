<form name="search" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>信息查询</caption>
<tbody>
<tr>
<td class="align_c">
ID <input class="input_blur" name="id" id="id" size="20" type="text">&nbsp;
手机号 <input class="input_blur" name="username" id="username" size="20" type="text">&nbsp;
昵称 <input class="input_blur" name="nick_name" id="nick_name" size="20" type="text">&nbsp;
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
<th width="100">手机</th>
<th width="150">推荐人</th>
<th width="100">添加时间</th>
<th width="80">状态</th>
</tr>
<?php if (! empty($itemList)): ?>
<?php foreach ($itemList as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#f2f2f2'" onMouseOut="this.style.background='#FFFFFF'">
    <td><input onclick="javascript:select_user('<?php echo $value['id']; ?>', '<?php echo $value['nickname']; ?>');"  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="radio"> <?php echo $value['id']; ?></td>
<td><a href="admincp.php/<?php echo $template; ?>/save/<?php echo $value['id']; ?>" ><?php echo $value['nickname']; ?></a></td>
<td class="align_c"><?php echo $value['mobile']; ?></td>
<td class="align_c"><?php if ($value['parent_id']){ echo $value['parent_name'].'[ID:'.$value['parent_id'].']'; } ?></td>
<td class="align_c"><?php echo date('Y-m-d H:i', $value['add_time']); ?></td>
<td class="align_c"><?php echo $displayArr[$value['display']]; ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">

</div>
<div id="pages">
<?php echo $pagination; ?>
<a>总条数：<?php echo $paginationCount; ?></a>
<!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>
<script type="text/javascript">
    function select_user(id, nickname) {
        window.opener.document.getElementById("user_id").value=id;
        window.opener.document.getElementById("span_nickname").innerHTML = nickname;
        window.close();
    }
</script>