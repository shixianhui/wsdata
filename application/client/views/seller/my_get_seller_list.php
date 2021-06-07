<style type="text/css">
	.product_search span{font-size:13px;line-height:30px;}
	.product_search input{line-height:30px;border:1px solid #eaebeb;padding:0 5px;color:#666;}
	.product_search label{position: relative;vertical-align:middle;font-size:13px;color:#333;line-height:30px;}
	.product_search label div{display:inline-block;position: relative;
    overflow: hidden;
    height: 30px;
    border: 1px solid #eaebeb;
    background: #fff;
    color: #666;
    -webkit-transition: border-color 0.2s linear;
    transition: border-color 0.2s linear;
    vertical-align:top; padding:0; line-height:20px;}

	.product_search select{-webkit-box-sizing: border-box;
    box-sizing: border-box;
    height: 30px;
    margin: 0;
    border: 0;
    padding: 0 20px 0 5px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    font-size: 12px;
    font-weight: 400;
    line-height: 29px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    vertical-align: middle;
    background: none;
    color: #666;
    outline: none;
    cursor: pointer;}
    .down{    position: absolute;
    right: 5px;
    top: 10px;
    z-index: 1;
    width: 16px;
    height: 16px;
    color: #b0b0b0;
    cursor: pointer;
    pointer-events: none;
    background: url(images/default/icon.png) no-repeat -402px -96px;
}
</style>
<div class="member_right mt20">

    <div class="box_shadow clearfix m_border">
        <div class="member_title"><span class="bt">账号管理</span>
            <a href="<?php echo getBaseUrl(false, '', 'seller/my_save_seller.html', $client_index); ?>" class="add_btn">+ 新增账号</a></div>
        </div>
        <form class="product_search" id="product_search" method="post" action="index.php/<?php echo $template; ?>/my_get_seller_list.html">
            <div style="padding-top: 20px;">

            <span>账号：</span><input style="width:100px;" type="text" name="username">
            <label>
                所属部门：<div><span class="down"></span>
                <select class="" name="seller_group">
                    <option value="">选择部门</option>
                    <?php if ($group_name_arr) { ?>
                        <?php foreach ($group_name_arr as $key=>$value) { ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php }} ?>
                </select>
                </div>
            </label>
                <span>加入时间：</span><input type="text" name="add_time_start" id="add_time_start" style="width: 80px;">--<input type="text" name="add_time_end" id="add_time_end" style="width: 80px">
                <input type="submit" value="查询" style="width: 70px;color: #000;margin-left: 5px">
            </div>
        </form>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="b_shop_table">
            <tr>
                <th width="15%">用户名/账号</th>
                <th width="10%">所属部门</th>
                <th width="15%">昵称</th>
                <th width="15%">姓名</th>
                <th width="15%">手机号</th>
                <th width="15%">操作</th>
            </tr>
            <?php
               if($item_list){
                   foreach($item_list as $item){
                       ?>
            <tr>
                <td align="center"><?php echo $item['username']; ?></td>
                <td align="center"><?php if ($group_name_arr) { echo $group_name_arr[$item['seller_group_id']]; } ?></td>
                <td align="center"><?php echo $item['nickname'];?></td>
                <td align="center"><?php echo $item['real_name'];?></td>
                <td align="center"><?php echo $item['mobile'];?></td>
                <td align="center">
                    <a href="<?php echo getBaseUrl(false,'','seller/my_save_seller/'.$item['id'],$client_index)?>">修改</a>
                    <a href="javascript:void(0)" onclick="my_delete_seller(<?php echo $item['id'];?>,this);">删除</a>
                </td>
            </tr>
                   <?php }}?>
        </table>
         <div class="clear"></div>
	<div class="pagination">
		<ul>
		<?php echo $pagination; ?>
		</ul>
	</div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('#add_time_start').calendar({ maxDate:'#add_time_end', btnBar:false });
        $('#add_time_end').calendar({ minDate:'#add_time_start', btnBar:false });
    });

    function my_delete_seller(id, obj) {
        var d = dialog({
            title: '提示',
            width: 300,
            fixed: true,
            content: '您确定要删除此账号吗？！',
            okValue: '确定',
            ok: function() {
                $.post(base_url + 'index.php/seller/my_delete_seller', {
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