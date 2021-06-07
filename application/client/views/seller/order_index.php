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
    .m_btn{
        margin-top: 5px;
    }

</style>
<div class="member_right mt20">
  <div class="box_shadow clearfix m_border">
     <div class="member_title"><span class="bt">我的订单</span></div>
<div class="member_tab mt20">
 <div class="hd">
 <ul>
	<li <?php if ($select_status == 'all') {echo 'class="on"';} ?> onclick="location.href = '<?php echo base_url().getBaseUrl(false, "", "{$template}/order_index/all.html", $client_index); ?>'">所有订单</li>
	<li <?php if ($select_status == '0') {echo 'class="on"';} ?> onclick="location.href = '<?php echo base_url().getBaseUrl(false, "", "{$template}/order_index/0.html", $client_index); ?>'">待付款</li>
	<li <?php if ($select_status == '1') {echo 'class="on"';} ?> onclick="location.href = '<?php echo base_url().getBaseUrl(false, "", "{$template}/order_index/1.html", $client_index); ?>'">已付款</li>
	<li <?php if ($select_status == '2') {echo 'class="on"';} ?> onclick="location.href = '<?php echo base_url().getBaseUrl(false, "", "{$template}/order_index/2.html", $client_index); ?>'">已使用</li>
	<li <?php if ($select_status == '3') {echo 'class="on"';} ?> onclick="location.href = '<?php echo base_url().getBaseUrl(false, "", "{$template}/order_index/3.html", $client_index); ?>'">已评价</li>
</ul>
 </div>
 <div class="bd">
 <div class="clearfix">
     <form class="product_search" id="product_search" method="post" action="index.php/<?php echo $template; ?>/order_index.html">
         <div style="padding-top: 20px;">
             <label>
                 订单状态：<div><span class="down"></span>
                     <select class="" name="status">
                         <option value="">选择状态</option>
                         <?php if ($status) { ?>
                             <?php foreach ($status as $key=>$value) { ?>
                                 <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                             <?php }} ?>
                     </select>
                 </div>
             </label>
             <span>订单编号：</span><input style="width:100px;" type="text" name="order_number">
             <span>下单时间：</span><input type="text" name="create_time_start" id="create_time_start" style="width: 80px;" readonly="readonly">--<input type="text" name="create_time_end" id="create_time_end" style="width: 80px" readonly="readonly">
             <input type="submit" value="查询" style="width: 70px;color: #000;margin-left: 5px">
         </div>
     </form>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="member_table mt10">
   <thead>
  <tr>
    <th width="36%" class="tal">商品信息</th>
    <th width="13%">单价（元）</th>
    <th width="10%">数量</th>
    <th width="15%">实付款（元）</th>
    <th width="14%">订单状态</th>
    <!-- <th width="14%">操作</th> -->
  </tr>
  </thead>
  </table>
<?php if ($item_list) { ?>
<?php foreach ($item_list as $key=>$value) {
          $view_url = getBaseUrl($html, "", "{$template}/order_view/{$value['id']}.html", $client_index);
?>
  <table style="margin-top: 10px;" width="100%" border="0" cellspacing="0" cellpadding="0" class="member_table">
  <tbody>
  <tr>
    <th colspan="6" align="left"><div class="fl"><input type="checkbox" name="order_ids[]" value="checkbox" style="margin-right:5px;" /><font class="c9">下单时间：</font><?=$value['create_time']?>&nbsp;&nbsp;&nbsp;订单编号：<?php echo $value['order_number']; ?></div></th>
    </tr>
    <?php if ($value['order_detail_list']) { ?>
	<?php foreach ($value['order_detail_list'] as $od_key=>$od_value) {
		      $url = $od_value['item_type'] ? getBaseUrl($html, "", "seller/my_save_share_goods/{$od_value['item_id']}.html", $client_index) : getBaseUrl($html, "", "seller/my_save_combos/{$od_value['item_id']}.html", $client_index);
	?>
    <tr>
    <td width="36%" valign="middle"><div class="info"><a href="<?php echo $url; ?>" target="_blank"><img src="<?php if ($od_value['item_image']) { echo preg_match('/^http/', $od_value['item_image']) ? $od_value['item_image'] : preg_replace('/\./', '_thumb.', $od_value['item_image']);}else{echo 'images/default/load.jpg';} ?>"><span style="color: #ff6a00;"><?=$od_value['item_type'] ? '[吆喝商品]' :'[套餐]'?></span><br><?php echo $od_value['item_name']; ?>
	</a></div></td>
    <td width="13%" align="center"><small>¥</small><?php echo $od_value['sell_price']; ?></td>
    <td width="10%" align="center"><?php echo $od_value['buy_number']; ?></td>
    <?php if($od_key == 0) { ?>
    <td rowspan="<?php echo count($value['order_detail_list']); ?>" width="15%" align="center">
    <span class="red"><small>¥</small><?php echo $value['total']; ?></span><br>
    <?php if ($od_value['item_type']) {?><span class="red">佣金：<small>¥</small><?php echo $od_value['reward']; ?></span><?php } ?>
    </td>
    <td rowspan="<?php echo count($value['order_detail_list']); ?>" width="14%" align="center" >
        <font class="c9"><?php echo $status[$value['status']]; ?></font><br>
        <!-- <a href="<?php echo $view_url; ?>"><font class="c9 mt5">查看详情</font></a> -->
    </td>
    <!-- <td rowspan="<?php echo count($value['order_detail_list']); ?>" width="14%" align="center">
        <?php if ($value['status'] == 0) { ?>
            <a style="display: none" onclick="change_pay(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?=$value['create_time']?>','<?php echo $value['total']; ?>');" href="javascript:void(0);" title="用于线下付款修改订单状态" class="m_btn">改为已付款</a>
            <a onclick="change_price(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?=$value['create_time']?>','<?php echo $value['total']; ?>');" class="m_btn" href="javascript:void(0);">修改金额</a>
            <a onclick="close_order(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?=$value['create_time']?>','<?php echo $value['total']; ?>');" class="m_btn" href="javascript:void(0);">交易关闭</a>
        <?php } else if ($value['status'] == 1) { ?>
            <a onclick="delivery(<?php echo $value['id']; ?>,'<?php echo $value['order_number']; ?>','<?=$value['create_time']?>','<?php echo $value['total']; ?>');" class="m_btn" href="javascript:void(0);">发货</a><br/>
        <?php } ?>
    </td> -->
    <?php } ?>
  </tr>
  <?php }} ?>
  </tbody>
</table>
<?php }} ?>
<div class="delete_cuont mt20">
<!-- <input type="checkbox" name="checkbox" style="margin-right:2px;" onclick="javascript:select_all(this);" /> 全选 -->
<!-- <a href="javascript:void(0);"><span class="icon delete_icon"></span>删除</a></div> -->
<div class="clear"></div>
<div class="pagination">
	<ul>
		<?php echo $pagination;?>
	</ul>
</div>
 </div>
</div>
</div>
   </div>
 </div>
<script type="text/javascript">
function select_all(obj) {
	if($(obj).attr("checked") == "checked"){
		$("input[name='order_ids[]']").prop('checked', true);
	} else {
		$("input[name='order_ids[]']").prop('checked', false);
	}
}


//修改金额
    function change_price(id, order_num, add_time, total) {
        var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
        html += '<tr><th width="30%"><strong>订单编号</strong> <br/></th>';
        html += '<td>'+order_num+'</td></tr>';
        html += '<tr><th width="30%"><strong>下单时间</strong> <br/></th>';
        html += '<td>'+add_time+'</td></tr>';
        html += '<tr><th width="30%"><strong>订单金额</strong> <br/></th>';
        html += '<td>'+total+'元</td></tr>';
        html += '<tr><th width="30%"> <strong>订单金额修改为</strong> <br/></th>';
        html += '<td>';
        html += '<input id="order_total" value="'+total+'" size="10" class="input_blur" type="text" /> 元';
        html += '</td>';
        html += '</tr>';
        html += '</table>';
        var d = dialog({
            width:350,
            fixed: true,
            title: '修改订单金额提示',
            content: html,
            okValue: '确认修改订单金额',
            ok: function () {
                var order_total = $('#order_total').val();
                if (!order_total) {
                    return my_alert('order_total', 1, '订单金额不能为空');
                }
                if (total == order_total) {
                    return my_alert('order_total', 1, '修改金额不能跟原金额一样');
                }
                $.post(base_url+"index.php/"+controller+"/seller_change_price",
                    {	"id": id,
                        "total":order_total
                    },
                    function(res){
                        if(res.success){
                            return my_alert_flush('fail', 0, res.message);
                        } else {
                            if (res.field == 'fail') {
                                return my_alert('fail', 0, res.message);
                            } else {
                                return my_alert(res.field, 1, res.message);
                            }
                        }
                    },
                    "json"
                );
                return false;
            },
            cancelValue: '取消',
            cancel: function () {
            }
        });
        d.show();
    }

//关闭交易
function close_order(id, order_num, add_time, total) {
    var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
    html += '<tr><th width="30%"><strong>订单编号</strong> <br/></th>';
    html += '<td>'+order_num+'</td></tr>';
    html += '<tr><th width="30%"><strong>下单时间</strong> <br/></th>';
    html += '<td>'+add_time+'</td></tr>';
    html += '<tr><th width="30%"><strong>订单金额</strong> <br/></th>';
    html += '<td>'+total+'元</td></tr>';
    html += '<tr><th width="30%"> <strong>关闭原因</strong> <br/></th>';
    html += '<td>';
    html += '<textarea id="cancel_cause" rows="4" cols="30"  class="textarea_style"></textarea>';
    html += '</td>';
    html += '</tr>';
    html += '</table>';
    var d = dialog({
        width:350,
        fixed: true,
        title: '关闭交易提示',
        content: html,
        okValue: '确认关闭交易',
        ok: function () {
            var cancel_cause = $('#cancel_cause').val();
            if (!cancel_cause) {
                return my_alert('cancel_cause', 1, '请输入关闭原因');
            }
            $.post(base_url+"index.php/"+controller+"/seller_close_order",
                {	"id": id,
                    "cancel_cause":cancel_cause
                },
                function(res){
                    if(res.success){
                        return my_alert_flush('fail', 0, res.message);
                    } else {
                        if (res.field == 'fail') {
                            return my_alert('fail', 0, res.message);
                        } else {
                            return my_alert(res.field, 1, res.message);
                        }
                    }
                },
                "json"
            );
            return false;
        },
        cancelValue: '取消',
        cancel: function () {
        }
    });
    d.show();
}

//状态设置为已付款
function change_pay(id, order_num, add_time, total) {
    var html = '<font color="red" style="font-size: 12px;line-height: 200%">注：确定要将订单状态修改为已付款吗，请确认已线下收到客户的打款？</font>';
    html += '<table class="table_form" cellpadding="0" cellspacing="1">';
    html += '<tr><th width="30%"><strong>订单编号</strong> <br/></th>';
    html += '<td>'+order_num+'</td></tr>';
    html += '<tr><th width="30%"><strong>下单时间</strong> <br/></th>';
    html += '<td>'+add_time+'</td></tr>';
    html += '<tr><th width="30%"><strong>订单金额</strong> <br/></th>';
    html += '<td>'+total+'元</td></tr>';
    html += '<tr><th width="30%"> <strong>备注</strong> <br/></th>';
    html += '<td>';
    html += '<textarea id="remark" placeholder="请输入打款凭证" rows="4" cols="35"  class="textarea_style"></textarea>';
    html += '</td>';
    html += '</tr>';
    html += '</table>';
    var d = dialog({
        width:400,
        fixed: true,
        title: '修改订单状态提示',
        content: html,
        okValue: '确认',
        ok: function () {
            var remark = $('#remark').val();
            if (!remark) {
                return my_alert('remark', 1, '请输入备注');
            }
            $.post(base_url+"index.php/"+controller+"/seller_change_status_2",
                {	"id": id,
                    "remark":remark
                },
                function(res){
                    if(res.success){
                        return my_alert_flush('fail', 0, res.message);
                    } else {
                        if (res.field == 'fail') {
                            return my_alert('fail', 0, res.message);
                        } else {
                            return my_alert(res.field, 1, res.message);
                        }
                    }
                },
                "json"
            );
            return false;
        },
        cancelValue: '取消',
        cancel: function () {
        }
    });
    d.show();
}

//发货
function delivery(id, order_num, add_time, total) {
    var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
    html += '<tr><th width="25%"><strong>订单编号</strong> <br/></th>';
    html += '<td>'+order_num+'</td></tr>';
    html += '<tr><th width="25%"><strong>下单时间</strong> <br/></th>';
    html += '<td>'+add_time+'</td></tr>';
    html += '<tr><th width="25%"><strong>订单金额</strong> <br/></th>';
    html += '<td>'+total+'元</td></tr>';
    html += '<tr><th width="25%"> <strong>快递名称</strong> <br/></th>';
    html += '<td>';
    html += '<input id="delivery_name" size="20" class="input_blur" type="text" />';
    html += '</td>';
    html += '</tr>';
    html += '<tr><th width="25%"> <strong>快递单号</strong> <br/></th>';
    html += '<td>';
    html += '<input id="express_number" size="20" class="input_blur" type="text" />';
    html += '</td>';
    html += '</tr>';
    html += '<tr><th width="25%"> <strong>备注</strong> <br/></th>';
    html += '<td>';
    html += '<textarea id="remark" placeholder="请输入备注" rows="4" cols="35"  class="textarea_style"></textarea>';
    html += '</td>';
    html += '</tr>';
    html += '</table>';
    var d = dialog({
        width:400,
        fixed: true,
        title: '修改订单状态提示',
        content: html,
        okValue: '确认',
        ok: function () {
            var delivery_name = $('#delivery_name').val();
            var express_number = $('#express_number').val();
            var remark = $('#remark').val();
            if (!delivery_name) {
                return my_alert('delivery_name', 1, '请输入快递名称');
            }
            if (!express_number) {
                return my_alert('express_number', 1, '请输入快递单号');
            }
            $.post(base_url+"index.php/"+controller+"/seller_delivery",
                {	"id": id,
                    "delivery_name":delivery_name,
                    "express_number":express_number,
                    "remark":remark
                },
                function(res){
                    if(res.success){
                        return my_alert_flush('fail', 0, res.message);
                    } else {
                        if (res.field == 'fail') {
                            return my_alert('fail', 0, res.message);
                        } else {
                            return my_alert(res.field, 1, res.message);
                        }
                    }
                },
                "json"
            );
            return false;
        },
        cancelValue: '取消',
        cancel: function () {
        }
    });
    d.show();
}

//确认收货
function receiving(id, order_num, add_time, total) {
    var html = '<font color="red">注：确认对此订单进行收货操作？</font>';
    html += '<table class="table_form" cellpadding="0" cellspacing="1">';
    html += '<tr><th width="25%"><strong>订单编号</strong> <br/></th>';
    html += '<td>'+order_num+'</td></tr>';
    html += '<tr><th width="25%"><strong>下单时间</strong> <br/></th>';
    html += '<td>'+add_time+'</td></tr>';
    html += '<tr><th width="25%"><strong>订单金额</strong> <br/></th>';
    html += '<td>'+total+'元</td></tr>';
    html += '</table>';
    var d = dialog({
        width:300,
        fixed: true,
        title: '确认收货提示',
        content: html,
        okValue: '确认收货',
        ok: function () {
            $.post(base_url+"index.php/"+controller+"/seller_receiving",
                {	"id":id
                },
                function(res){
                    if(res.success) {
                        return my_alert_flush('fail', 0, res.message);
                    } else {
                        if (res.field == 'fail') {
                            return my_alert('fail', 0, res.message);
                        } else {
                            return my_alert(res.field, 1, res.message);
                        }
                    }
                },
                "json"
            );
        },
        cancelValue: '取消',
        cancel: function () {
        }
    });
    d.show();
}
</script>
