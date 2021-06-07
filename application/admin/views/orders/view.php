<?php echo $tool; ?>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>订单状态跟踪</caption>
 	<tbody>
 	<?php if($orders_process_list) { ?>
 	<?php foreach ($orders_process_list as $ordersprocess) { ?>
	<tr>
      <th width="20%">
      <strong><?php echo $ordersprocess['create_time']; ?></strong> <br/>
	  </th>
      <td>
      <?php echo $ordersprocess['content']; ?>
	</td>
	<?php }} ?>
    </tr>
</tbody>
</table>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>订单信息</caption>
 	<tbody>
	<tr>
      <th width="20%">
      <strong>订单编号</strong> <br/>
      </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['order_number'];} ?>
	</td>
    </tr>
    <tr>
      <th> <strong>下单时间</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $item_info['create_time'];} ?>
      </td>
    </tr>
    <tr>
      <th> <strong>订单状态</strong> <br/>
	  </th>
      <td>
      <?php if(! empty($item_info)){ echo $status_arr[$item_info['status']];} ?>
      </td>
    </tr>
</tbody>
</table>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>买家信息</caption>
 	<tbody>
    <tr>
      <th> <strong>昵称</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['nickname'];} ?>
      </td>
      <th> <strong>手机</strong> <br/>
	  </th>
      <td>
      <?php if($user_info){ echo $user_info['mobile'];} ?>
      </td>
    </tr>
</tbody>
</table>
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>商品信息</caption>
 	<tbody>
	<tr>
	<th class="align_c" width="60" scope="col">&nbsp;</th>
    <th class="align_c" scope="col"><strong>商品</strong></th>
    <th class="align_c" scope="col" width="90"><strong>数量</strong></th>
    <th class="align_c" scope="col" width="90"><strong>单价</strong></th>
    <th class="align_c" scope="col" width="90"><strong>佣金</strong></th>
  </tr>
  <?php if ($orders_detail_list) {
        foreach ($orders_detail_list as $key=>$orderdetail) {
  	?>
  <tr>
    <td class="align_c">
    <img src="<?php if ($orderdetail['item_image']){ echo preg_match('/^http/', $orderdetail['item_image']) ? $orderdetail['item_image'] : preg_replace('/\./', '_thumb.', $orderdetail['item_image']);;}else{echo 'images/admin/nopic.gif';} ?>" width="50px" height="50px" />
    </td>
    <td>
    <?php echo $orderdetail['item_name']; ?></td>
     <td class="align_c"><?php echo $orderdetail['buy_number']; ?></td>
    <td class="align_c">¥<?php echo $orderdetail['sell_price']; ?></td>
    <td class="align_c">¥<?php echo $orderdetail['reward']; ?></td>
  </tr>
  <?php }} ?>
  <tr>
    <td colspan="5" style="text-align:right;height:25px;">商品总价：<span style="font-size:14px;margin-right:20px;">+ ￥<?php if(! empty($item_info)){ echo $item_info['total'];} ?></span></td>
  </tr>

  <tr>
    <td colspan="5" >
    实收款：
    <span style="font-size:18px; font-weight:bold;color:#E36439;">￥<?php if(! empty($item_info)){ echo $item_info['total'];} ?></span>
    </td>
  </tr>
</tbody>
</table>
</div>
<br/>
