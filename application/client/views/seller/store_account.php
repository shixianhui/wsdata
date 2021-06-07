<div class="member_right mt20">

    <div class="member_headline box_shadow clearfix">

        <ul class="short1">
            <Li><b>账户余额</b>
                <span class="red"><small>￥</small><?php echo $item_info['account_amount'];?></span>
            </Li>
            <Li><b>未入账金额</b>
                <span class="red"><small>￥</small><?php echo $item_info['unrecorded_orders_total'];?></span>
            </Li>
            <Li style="margin-top:15px;"><a href="<?php echo getBaseUrl(false, "", "seller/store_draw.html", $client_index); ?>" class="m_btn">提现</a></Li>
        </ul>
    </div>
    <div class="box_shadow clearfix mt20 m_border">
        <div class="member_title"><span class="bt">账户记录</span></div>

        <div class="member_tab mt20">
            <div class="hd">
                <ul>
                    <Li class="on">提现记录</Li>
                </ul>
            </div>
            <div class="bd">
                <div class="clearfix">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="member_table mt10">
                        <tbody>
                            <tr>
                                <th class="tal"><strong>操作时间</strong></th>
                                <th ><strong>类型</strong></th>
                                <th ><strong>提现金额（元）</strong></th>
                                <th ><strong>到账金额（元）</strong></th>
                                <th ><strong>备注</strong></th>
                                <th ><strong>状态</strong></th>
                            </tr>
                           <?php
                                if($item_list){
                                    foreach($item_list as $item){
                                ?>
                                <tr>
                                    <td width="20%" align="left"><?=$item['create_time']?></td>
                                    <td width="10%" align="center">
                                        提现
                                    </td>
                                    <td width="15%" align="center"><b class="purple f14"><?php echo $item['amount'];?></b></td>
                                    <td width="15%" align="center"><b class="purple f14"><?php echo $item['arrival_amount'];?></b></td>
                                    <td width="25%" align="center" ><?php echo $item['client_remark'];?></td>
                                    <td width="15%" align="center" ><?php echo $item['status'] ? '已打款' : '待审核';?></td>
                                </tr>
                                <?php }}?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
               <div class="clear"></div>
            <div class="pagination">
                <ul><?php echo $pagination; ?></ul>
            </div>
    </div>
</div>