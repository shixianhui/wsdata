<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <table class="table_form" cellpadding="0" cellspacing="1">
        <caption>信息查询</caption>
        <tbody>
        <tr>
            <td class="align_c">
                申请人手机号 <input class="input_blur" name="username" id="username" size="20" type="text">&nbsp;
                <select class="input_blur" name="status">
                    <option value="">选择状态</option>
                    <?php if ($status_arr) { ?>
                        <?php foreach ($status_arr as $key=>$value) { ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php }} ?>
                </select>&nbsp;
                申请时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;<script language="javascript" type="text/javascript">
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
        </tbody>
    </table>
</form>
<table class="table_list" id="news_list" cellpadding="0" cellspacing="1">
    <caption>信息管理</caption>
    <tbody>
    <tr class="mouseover">
        <th width="70">选中</th>
        <th width="120">会员信息</th>
        <th width="80">账号类型</th>
        <th width="150">开户名</th>
        <th width="150">银行卡号</th>
        <th width="150">银行</th>
        <th width="80">提现金额</th>
        <th width="150">申请时间</th>
        <th width="150">处理时间</th>
        <th width="120">状态</th>
        <th width="120">处理凭证</th>
        <th width="70">管理操作</th>
    </tr>
    <?php if (! empty($item_list)): ?>
        <?php foreach ($item_list as $key=>$value): ?>
            <tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background='#FFFFFF'">
                <td class="align_c"><?php echo $value['id']; ?></td>
                <td class="align_c"><?php echo $value['username']; ?></td>
                <td class="align_c"><?php echo $value['type']; ?></td>
                <td class="align_c"><?php echo $value['realname']; ?></td>
                <td class="align_c"><?php echo $value['account']; ?></td>
                <td class="align_c"><?php echo $value['bank'].' '.$value['location']; ?></td>
                <td class="align_c"><?php echo $value['amount']; ?></td>
                <td class="align_c"><?php echo $value['create_time']; ?></td>
                <td class="align_c"><?php if($value['update_time']){echo $value['update_time'];} ?></td>
                <td class="align_c"><?php echo $status_arr[$value['status']]; ?></td>
                <td class="align_c"><a id="path_src_a" title="点击查看大图" href="<?php if ($value['path']){echo $value['path'];}else{echo 'images/admin/no_pic.png';} ?>" target="_blank"><img id="path_src" width="60px" height="60px" src="<?php if ($value['path']){echo preg_replace('/\./', '_thumb.', $value['path']);}else{echo 'images/admin/no_pic.png';} ?>" onerror="javascript:this.src='images/admin/no_pic.png';" /></a></td>
                <td class="align_c"><a href="admincp.php/<?php echo $table; ?>/save/<?php echo $value['id']; ?>">处理</a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<!--<div class="button_box">-->
<!--<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/-->
<!--<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>-->
<!--    <input class="button_style" name="delete" id="delete" value=" 删除 "  type="button">-->
<!--</div>-->
<div id="pages" style="margin-top: 5px;">
    <?php echo $pagination; ?>
    <a>总条数：<?php echo $paginationCount; ?></a>
    <!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>
<br/><br/>
<!-- <input type="button" id="btnOK" class="button_style"  value="获取表格" />
<a style="display: none;text-decoration: underline;padding-left: 10px;" id="download" href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/admincp.php/draw/download">点击此处下载表格</a> -->
<script language="javascript" type="text/javascript">
    $(function() {
        $("#btnOK").click(function() {
            $.ajax({
                type: "Post",
                url: base_url+"admincp.php/draw/download",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function() {
                    //返回的数据用data.d获取内容

                }
            });
            $("#download").css('display','inline-block');
            //禁用按钮的提交
            return false;
        });
    });
</script>