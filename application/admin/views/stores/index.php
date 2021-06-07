<?php echo $tool; ?>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
<caption>信息管理</caption>
<tbody>
<tr class="mouseover">
<th width="70">选中</th>
<th width="50">排序</th>
<th width="150">商户名</th>
<th width="80">分类</th>
<th width="80">类型</th>
<th>地址</th>
<th width="100">电话</th>
<th width="80">营业状态</th>
<th width="60">状态</th>
<th width="150">操作</th>
</tr>
<?php if (! empty($item_list)): ?>
<?php foreach ($item_list as $key=>$value): ?>
<tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background=''">
<td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"> <?php echo $value['id']; ?></td>
<td class="align_c"><input style="background-color:#E0E0E0;" class="input_blur" name="sort[]" id="sort_<?php echo $value['id']; ?>" value="<?php echo $value['sort']; ?>" size="4" type="text"></td>
<td><?php echo $value['store_name']; ?><?=$value['is_branch']?'  [分店]':''?></td>
<td class="align_c"><?php echo $value['type_str']; ?></td>
<td class="align_c"><?php echo $store_type_arr[$value['store_type']]; ?></td>
<td><?php echo $value['address']; ?></td>
<td class="align_c"><?php echo $value['phone']; ?></td>
<td class="align_c"><?php echo $business_status_arr[$value['business_status']]; ?></td>
<td class="align_c"><?php echo $status_arr[$value['status']]; ?></td>
<td class="align_c">
 <a href="admincp.php/<?php echo $table; ?>/save/<?php echo $value['id']; ?>">修改</a>
  | <a href="javascript:delete_item(<?php echo $value['id']; ?>)">删除
  </a>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
<input style="margin-left: 10px;" class="button_style" name="list_order" id="list_order" value=" 排序 "  type="button">

</div>
<br/><br/>
<script language="javascript" type="text/javascript">
function delete_item(id) {
	var con = confirm("你确定要删除[ID:"+id+"]吗？删除后将不可恢复！");
	if (con == true) {
		$.post("<?php echo base_url(); ?>admincp.php/"+controller+"/delete",
			{	"id": id
			},
			function(res){
				if(res.success){
					$("#id_"+res.data.id).remove();
					return false;
				}else{
					alert(res.message);
					return false;
				}
			},
			"json"
		);
	}
}
</script>