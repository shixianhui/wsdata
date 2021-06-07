<div class="member_right mt20">
    <div class="box_shadow clearfix m_border">
        <div class="member_title"><span class="bt">部门设置</span>
        <span style="color:#9c9c9c;margin-left:50px;"></span>
        <a href="<?php echo getBaseUrl(false, '', 'seller/my_save_seller_group.html', $client_index); ?>" class="add_btn">+ 新增部门</a></div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="b_shop_table">
            <tr>
                <th>部门名称</th>
                <th width="15%">操作</th>
            </tr>
            <?php
              if($item_list){
                  foreach($item_list as $item){
            ?>
            <tr class="form_tr">

                <td style="text-align:center;"><?php echo $item['group_name'];?></td>
                <td class="link_action">
                    <a href="<?php echo getBaseUrl(false,'','seller/my_save_seller_group/'.$item['id'].'.html',$client_index);;?>">修改</a>
                    <a href="javascript:void(0)" onclick="my_delete_seller_group(<?php echo $item['id'];?>,this);">删除</a>
                </td>
            </tr>
              <?php }}?>
        </table>
    </div>
</div>
<script>
	function my_delete_seller_group(id, obj) {
		var d = dialog({
			title: '提示',
			width: 300,
			fixed: true,
			content: '您确定要删除此部门吗？部门下账号将会清空！',
			okValue: '确定',
			ok: function() {
				$.post(base_url + 'index.php/seller/my_delete_seller_group', {
					'id': id
				}, function(data) {
					if(data.success == false) {
						var d = dialog({
							width: 300,
							title: '提示',
							fixed: true,
							content: data.message
						});
						d.show();
						setTimeout(function() {
							d.close().remove();
						}, 2000);
						return false;
					}
					$(obj).parents('tr').remove();
				}, 'json');
			},
			cancelValue: '取消',
			cancel: function() {}
		});
		d.show();
	}
</script>