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
        <div class="member_title"><span class="bt">吆喝商品管理</span><a href="<?php echo getBaseUrl(false, '', 'seller/my_save_share_goods.html', $client_index); ?>" class="more"><font class="blue">+ 新增吆喝商品</font></a></div>
        <form class="product_search" id="product_search" method="post" action="index.php/<?php echo $template; ?>/my_get_share_goods_list/1.html">
            <div style="padding-top: 20px;">
                <span>商品名称：</span><input style="width:100px;" type="text" name="title">
                <span>价格区间：</span><input type="text" name="sell_price_start" style="width: 50px;">--<input type="text" name="sell_price_end" style="width: 50px">
                <input type="submit" value="查询" style="width: 70px;color: #000;margin-left: 5px">
            </div>
        </form>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="b_shop_table">
            <tr>
                <th width="5%"></th>
                <!-- <th width="6%">排序</th> -->
                <th width="45%" style="text-align:left">商品信息</th>
                <th width="10%">价格</th>
                <th width="10%">佣金</th>
                <th width="10%">库存</th>
                <th width="10%">状态</th>
                <th width="10%">操作</th>
            </tr>
            <?php
                if($item_list){
                    foreach($item_list as $item){
            ?>
            <tr id="id_<?php echo $item['id'];?>">
                <td align="center"><input type="checkbox" name="ids" value="<?php echo $item['id'];?>"></td>
                <td style="display: inline-flex;"><img src="<?php echo !preg_match('/^http/', $item['cover_image']) ? str_replace('.','_thumb.',$item['cover_image']) : $item['cover_image'];?>" style="width: 60px;height:60rpx;margin-right: 4px;"><?php echo my_substr($item['name'],50); ?></td>
                <td align="center"><span class="red"><small>￥</small><?php echo $item['price'];?></span></td>
                <td align="center"><span class="red"><small>￥</small><?php echo $item['reward'];?></span></td>
                <td align="center"><?php echo $item['stock'];?></td>
                <td align="center"><?=$item['status'] == 1 ? ($item['display'] ? '上架': '下架') : $check_status_arr[$item['status']];?></td>
                <td align="center"><a href="<?php echo getBaseUrl(false,'','seller/my_save_share_goods/'.$item['id'],$client_index)?>">编辑</a></td>
            </tr>
                    <?php }} ?>
            <tr>
                <td colspan="8" align="center">
                    <div class="delete">
                    <label><input type="checkbox" id="selectAll">全选</label>
                    <!-- <a href="javascript:void(0);" id="list_product">排序</a> -->
                    <a href="javascript:void(0);" id="deleteProduct">删除</a>
            </div></td>
            </tr>
        </table>
            <div class="clear"></div>
        <div class="pagination">
            <ul>
            <?php echo $pagination; ?>
            </ul>
        </div>
    </div>
</div>
<script>
    $("#selectAll").change(function(){
        if($(this).is(":checked")){
            $("input[type=checkbox]").prop('checked',true);
        }else{
            $("input[type=checkbox]").prop('checked',false);
        };
    });
     $("#deleteProduct").click(function(){
    	var con = confirm("你确定要删除数据吗？删除后将不可恢复！");
    	if (con == true) {
	        $ids = "";
	    	$("input[name='ids']:checked").each(function(i,n){
	    		$ids += $(this).val() + ",";
	    	});
	    	if (! $ids) {
	    		alert("请选定值!");
	    		return false;
	    	}
			$.post(base_url+"index.php/"+controller+"/my_delete_share_goods",
					{	"ids": $ids.substr(0, $ids.length - 1)
					},
					function(res){
						if(res.success){
							for (var i = 0, data = res.data.ids, len = data.length; i < len; i++){
								$("#id_"+data[i]).remove();
							}
							return false;
						}else{
							alert(res.message);
							return false;
						}
					},
					"json"
			);
    	}
	});

    $("#list_product").click(function(){
        $ids = "";
        $sorts = "";
        $("input[name='ids']:checked").each(function(i,n){
            $ids += $(this).val() + ",";
            $sorts += $("#sort_" + $(this).val()).val() + ',';
        });
        if (! $ids) {
            alert("请选定值!");
            return false;
        }
        $.post(base_url+"index.php/"+controller+"/my_sort_combos",
            {	"ids": $ids.substr(0, $ids.length - 1),
                "sorts": $sorts.substr(0, $sorts.length - 1)
            },
            function(res){
                if(res.success){
                    // alert(res.message);
                    window.location.reload();
                    return false;
                }else{
                    alert(res.message);
                    return false;
                }
            },
            "json"
        );
    });
</script>