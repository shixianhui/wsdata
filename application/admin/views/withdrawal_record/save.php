<style>

    .in {
        width: 60px;
        height: 60px;
    }
</style>
<?php echo $tool; ?>
<table class="table_form" cellpadding="0" cellspacing="1">
    <caption>基本信息</caption>
    <tbody>
    <tr>
        <th width="20%"><strong>会员信息</strong> <br/>
        </th>
        <td>
            <?php if(! empty($user_info)){ echo $user_info['username'];} ?>
        </td>
    </tr>
    <tr>
        <th width="20%"><strong>账户类型</strong> <br/>
        </th>
        <td>
            <?php if ($item_info) {echo $item_info['type'];} ?>
        </td>
    </tr>
    <tr>
        <th width="20%"><strong>开户名</strong> <br/>
        </th>
        <td>
            <?php if(! empty($item_info)){ echo $item_info['realname'];} ?>
        </td>
    </tr>
    <tr>
        <th width="20%"><strong>银行卡号</strong> <br/>
        </th>
        <td>
            <?php if(! empty($item_info)){ echo $item_info['account'];} ?>
        </td>
    </tr>
    <tr>
        <th width="20%"><strong>银行</strong> <br/>
        </th>
        <td>
            <?php if(! empty($item_info)){ echo $item_info['bank'].' '.$item_info['location'];} ?>
        </td>
    </tr>
    <tr>
        <th width="20%"><strong>提现金额</strong> <br/>
        </th>
        <td>
            <?php if(! empty($item_info)){ echo $item_info['amount'];} ?> 元
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>申请时间</strong> <br/>
        </th>
        <td>
            <?php if(! empty($item_info)){ echo $item_info['create_time'];} ?>
        </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>最后处理时间</strong> <br/>
        </th>
        <td>
            <?php if($item_info){ echo $item_info['update_time'];} ?>
        </td>
    </tr>
    <?php if ($item_info['admin_remark']){ ?>
    <tr>
        <th width="20%"><strong>商家备注</strong> <br/>
        </th>
        <td>
            <?php echo $item_info['admin_remark']; ?>
        </td>
    </tr>
    <?php } ?>
    <?php if ($item_info['client_remark']){ ?>
    <tr>
        <th width="20%"><strong>对用户备注</strong> <br/>
        </th>
        <td>
            <?php echo $item_info['client_remark']; ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <th width="20%">
            <strong>当前处理状态</strong> <br/>
        </th>
        <td>
            <?php if(! empty($item_info)){ echo $status_arr[$item_info['status']];} ?>
            <?php if ($item_info) { ?>
                <?php if ($item_info['status'] == 0 || $item_info['status'] == 1) { ?>
                    <input onclick="javascript:change_check('<?php echo $item_info['id']; ?>','<?php echo $item_info['create_time']; ?>','<?php echo $item_info['amount']; ?>');" style="margin-left: 20px;" class="button_style" value="审核" type="button">
                <?php }  ?>
            <?php } ?>
        </td>
    </tr>

    <tr>
        <th width="20%">
            <font color="red">*</font> <strong>凭证图片</strong> <br/>
        </th>
        <td>
            <a id="path_src_a" title="点击查看大图" href="<?php if ($item_info && $item_info['path']){echo $item_info['path'];}else{echo 'images/admin/no_pic.png';} ?>" target="_blank" style="float:left;"><img id="path_src" width="60px" src="<?php if ($item_info && $item_info['path']){echo preg_replace('/\./', '_thumb.', $item_info['path']);}else{echo 'images/admin/no_pic.png';} ?>" onerror="javascript:this.src='images/admin/no_pic.png';" /></a>

            <div style="float:left; margin-top:22px;">
                <a style=" position:relative; width:auto; " >
                    <span style="cursor:pointer;" class="but_4">上传照片<input style="left:0px;top:0px; background:#000; width:100%;height:36px;line-height:36px; position:absolute;filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" id="path_file" name="path_file" ></span>
                    <i class="load" id="path_load" style="cursor:pointer;display:none;width:auto;padding-left:0px; left:50%; margin-left:-16px;"><img src="images/admin/loading_2.gif" width="32" height="32"></i>
                </a>

                <input value="<?php if ($item_info){echo $item_info['path'];} ?>" type="hidden" id="path" name="path">
                <input name="model" id="model"  value="<?php echo $table; ?>" type="hidden" />
                <span onclick="save_image();" style="cursor:pointer;" class="but_4">保存图片</span>
<!--                --><?php //$image_size_arr = get_image_size($table);
//                if ($image_size_arr) {
//                    ?>
<!--                    <span style="color:#9c9c9c;margin-left:30px;">注：缩略图大小＝--><?php //echo $image_size_arr['width']; ?><!--x--><?php //echo $image_size_arr['height']; ?><!--</span>-->
<!--                --><?php //} ?>
            </div>

        </td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td>
            <input onclick="javascrpt:window.location.reload();" class="button_style" name="dosubmit" value=" 刷新 " type="button">
            <input style="margin-left: 20px;" onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
        </td>
    </tr>
    </tbody>
</table>
<br/><br/>
<script src="js/admin/index.js" type="text/javascript"></script>
<script  type="text/javascript">
    function change_check(id, add_time, total) {
        var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
        html += '<tr><th width="30%"><strong>申请时间</strong> <br/></th>';
        html += '<td>'+add_time+'</td></tr>';
        html += '<tr><th width="30%"><strong>提现金额</strong> <br/></th>';
        html += '<td>'+total+' 元</td></tr>';
        html += '<tr><th width="30%"><font color="red">*</font> <strong>审核状态</strong><br/></th>';
        html += '<td>';
        html += '<label><input type="radio" value="2" name="status" class="radio_style"> 审核通过</label>';
        html += '&nbsp;<label><input type="radio" value="1" name="status" class="radio_style"> 审核未通过</label>';
        html += '</td>';
        html += '</tr>';
        html += '<tr><th width="30%"><strong>备注</strong><br/><font color="red">[会员看的]</font></th>';
        html += '<td>';
        html += '<textarea maxlength="140" id="client_remark" rows="4" cols="30"  class="textarea_style"></textarea>';
        html += '</td>';
        html += '</tr>';
        html += '<tr><th width="30%"> <strong>备注</strong><br/><font color="red">[管理员看的]</font></th>';
        html += '<td>';
        html += '<textarea maxlength="140" id="admin_remark" rows="4" cols="30"  class="textarea_style"></textarea>';
        html += '</td>';
        html += '</tr>';
        html += '</table>';
        var d = dialog({
            width:350,
            fixed: true,
            title: '退款审核提示',
            content: html,
            okValue: '确认',
            ok: function () {
                var status = $('input[name="status"]:checked').val();
                var client_remark = $('#client_remark').val();
                var admin_remark = $('#admin_remark').val();
                if (!status) {
                    return my_alert('fail', 0, '请选择状态');
                }
                if (status == 1) {
                    if (!client_remark) {
                        return my_alert('client_remark', 1, '备注不能为空');
                    }
                    if (!admin_remark) {
                        return my_alert('admin_remark', 1, '备注不能为空');
                    }
                }
                $.post(base_url+"admincp.php/"+controller+"/change_check",
                    {	"id": id,
                        "status":status,
                        "client_remark":client_remark,
                        "admin_remark":admin_remark
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

    function refund_to_balance(id, order_num, add_time, total, username) {
//        var html = '<font color="red">注：尽量使用【原路返回退款】进行退款，【退款到余额】只在第三方支付过了退款期了，才用这个方法</font>';
        var html = '';
        html += '<table class="table_form" cellpadding="0" cellspacing="1">';
        html += '<tr><th width="30%"><strong>退款订单编号</strong> <br/></th>';
        html += '<td>'+order_num+'</td></tr>';
        html += '<tr><th width="30%"><strong>申请时间</strong> <br/></th>';
        html += '<td>'+add_time+'</td></tr>';
        html += '<tr><th width="30%"><strong>退款金额</strong> <br/></th>';
        html += '<td>'+total+' 元</td></tr>';
        html += '<tr><th width="30%"><strong>申请人</strong> <br/></th>';
        html += '<td>'+username+'</td></tr>';
        html += '</table>';
        var d = dialog({
            width:350,
            fixed: true,
            title: '退款到余额提示',
            content: html,
            okValue: '确认退款到余额',
            ok: function () {
                $.post(base_url+"admincp.php/"+controller+"/refund_to_balance",
                    {	"id": id
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

    function refund_to_third(id, order_num, add_time, total, username) {
        var html = '<table class="table_form" cellpadding="0" cellspacing="1">';
        html += '<tr><th width="30%"><strong>退款订单编号</strong> <br/></th>';
        html += '<td>'+order_num+'</td></tr>';
        html += '<tr><th width="30%"><strong>申请时间</strong> <br/></th>';
        html += '<td>'+add_time+'</td></tr>';
        html += '<tr><th width="30%"><strong>退款金额</strong> <br/></th>';
        html += '<td>'+total+' 元</td></tr>';
        html += '<tr><th width="30%"><strong>申请人</strong> <br/></th>';
        html += '<td>'+username+'</td></tr>';
        html += '</table>';
        var d = dialog({
            width:350,
            fixed: true,
            title: '原路返回退款提示',
            content: html,
            okValue: '确认原路返回退款',
            ok: function () {
                $.post(base_url+"admincp.php/"+controller+"/refund_to_third",
                    {	"id": id
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
</script>

<link rel="stylesheet" href="js/admin/lightbox2-master/src/css/lightbox.css">
<script src="js/admin/lightbox2-master/src/js/lightbox.js"></script>
<script>
    lightbox.option({
        'resizeDuration': 500,
        'wrapAround': true,
        'positionFromTop': 200
    })
</script>
<script>
    function file_change_upload(obj) {
        $(obj).parent().parent().find(".myupload").ajaxSubmit({
            dataType:  'json',
            data: {
                'model': 'product_size_color',
                'field': 'good_url_path_file'
            },
            beforeSend: function() {
                $(obj).parent().parent().parent().find(".good_url_path_file_load").show();
            },
            uploadProgress: function(event, position, total, percentComplete) {
            },
            success: function(res) {
                $(obj).parent().parent().parent().find(".good_url_path_file_load").hide();
                if (res.success) {
                    $(obj).parent().parent().parent().parent().parent().parent().find(".good_url_path_file_src_a").attr("href", res.data.file_path);
                    $(obj).parent().parent().parent().parent().parent().parent().find(".good_url_path_file_src").attr("src", res.data.file_path.replace('.', '_thumb.')+"?"+res.data.field);
                    $(obj).parent().parent().parent().parent().find("input[name='attribute_path[]']").attr("value", res.data.file_path);
                } else {
                    var d = dialog({
                        fixed: true,
                        title: '提示',
                        content: res.message
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 2000);
                }
            },
            error:function(xhr){
            }
        });
    }
    //参数mulu
    $(function () {
        //多链接传图
        if (!$(".good_url_path_file").parent('form').hasClass("myupload")) {
            $(".good_url_path_file").wrap("<form class='myupload' action='<?php echo base_url(); ?>admincp.php/upload/uploadImage2' method='post' enctype='multipart/form-data'></form>");
        }
        //形象照片
        $("#path_file").wrap("<form id='path_upload' action='<?php echo base_url(); ?>admincp.php/upload/uploadImage2' method='post' enctype='multipart/form-data'></form>");
        $("#path_file").change(function(){ //选择文件
            $("#path_upload").ajaxSubmit({
                dataType:  'json',
                data: {
                    'model': 'product_category',
                    'field': 'path_file'
                },
                beforeSend: function() {
                    $("#path_load").show();
                },
                uploadProgress: function(event, position, total, percentComplete) {
                },
                success: function(res) {
                    $("#path_load").hide();
                    if (res.success) {

                        $("#path_src_a").attr("href", res.data.file_path);
                        $("#path_src").attr("src", res.data.file_path.replace('.', '_thumb.')+"?"+res.data.field);
                        $("#path").val(res.data.file_path);
                    } else {
                        var d = dialog({
                            fixed: true,
                            title: '提示',
                            content: res.message
                        });
                        d.show();
                        setTimeout(function () {
                            d.close().remove();
                        }, 2000);
                    }
                },
                error:function(xhr){
                }
            });
        });
    });
    load_image();
    function load_image() {
        var path = $('#path').val();
        if (path) {
            $("#path_src_a").attr("href", path);
            $("#path_src").attr("src", path.replace('.', '_thumb.'));
        }
    }
</script>
<script>
    function save_image(){
        var id = <?=$item_info['id']?>;
        var path = $('#path').val();
        $.post(base_url+"admincp.php/"+controller+"/save_path",
            {
                "id": id,
                "path":path,
            },
            function (data) {
            //json 字符串转为对象
            var res =JSON.parse(data);

                var d = dialog({
                    fixed: true,
                    title: '提示',
                    content: res.message
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 2000);
            }
        );
    }
</script>