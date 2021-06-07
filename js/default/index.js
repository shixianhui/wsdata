$(document).ready(function () {

    /****添加/编辑****/
    var flag = false;
    /****添加/编辑****/
    $('#jsonForm').ajaxForm({
        dataType: 'json',
        beforeSubmit: showRequest,
        success: showReponse
    });
    function showReponse(data) {
        if (data && data.success) {
            $(":submit").attr("disabled", true);
            if(data.message){
                if (data.field == 'success_login_go') {
                    flag = false;
                    var d = dialog({
                        width: 300,
                        title: '提示',
                        fixed: true,
                        content: '登录成功'
                    });
                    d.show();
                    setTimeout(function () {
                        window.location.href = data.message;
                        d.close().remove();
                    }, 2000);
                    return false;
                } else if (data.field == 'success_store') {
                    flag = false;
                    var d = dialog({
                        width: 300,
                        title: '提示',
                        fixed: true,
                        content: data.message
                    });
                    d.show();
                    setTimeout(function () {
                        window.location.href = base_url+'index.php/seller/my_join.html';
                        d.close().remove();
                    }, 1000);
                    return false;
                } else if (data.field == 'success_get_pass') {
                    flag = false;
                    var d = dialog({
                        width: 300,
                        title: '提示',
                        fixed: true,
                        content: data.message
                    });
                    d.show();
                    setTimeout(function () {
                        window.location.href = base_url + 'index.php/user/login.html';
                        d.close().remove();
                    }, 2000);
                    return false;
                } else if (data.field == 'success_back') {
                    flag = false;
                    $("#id_card").val("");
                    $("#id_pass").val("");
                    var d = dialog({
                        width: 300,
                        title: '提示',
                        fixed: true,
                        content: data.message
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 2000);
                    return false;
                } else {
                    flag = false;
                    var d = dialog({
                        width: 300,
                        title: '提示',
                        fixed: true,
                        content: data.message
                    });
                    d.show();
                    setTimeout(function () {
                        if (data.field == 'success') {
                            window.location.reload();
                        } else {
                            window.location.href = data.field;
                        }
                        d.close().remove();
                    }, 2000);
                    return false;
                }
            }else{
                window.location.href = data.field;
            }

            return false;
        } else {
            flag = false;
            if (data.field == 'fail_task_recharge') {
                var d = dialog({
                    title: '提示',
                    width: 300,
                    fixed: true,
                    content: data.message,
                    okValue: '确定',
                    ok: function () {
                        window.open(base_url + "index.php/user/advance.html");
                    }
                });
                d.show();
            } else {
                var d = dialog({
                    title: '提示',
                    width: 300,
                    fixed: true,
                    zIndex: 20000,
                    content: data.message,
                    okValue: '确定',
                    ok: function () {
                        if (data.field == 'code_fail') {
                            $("#valid_code_pic").click();
                        }
                        $("#" + data.field).focus();
                    },
                    cancelValue: '取消',
                    cancel: function () {
                        if (data.field == 'code_fail') {
                            $("#valid_code_pic").click();
                        }
                        $("#" + data.field).focus();
                    }
                });
                d.show();
                $("#" + data.field).focus();
            }
            return false;
        }
        
    }
    function showRequest() {
        if (flag == true) {
            return false;
        }
        if (validator(document.forms['jsonForm'])) {
            flag = true;
            return true;
        } else {
            flag = false;
            return false;
        }
    }

    $("#create_time_start").jeDate({
        onClose:false,
        isTime:false,
        format: "YYYY-MM-DD"
    });
    $("#create_time_end").jeDate({
        onClose:false,
        isTime:false,
        format: "YYYY-MM-DD"
    });
});
 function get_city(cur_id, next_id, next_select_val, prev_select_val, is_city) {
	var parent_id = $("#"+cur_id).val();
	if (prev_select_val) {
		parent_id = prev_select_val;
	}
	$.post(base_url+"index.php/user/get_city",
			{	"parent_id": parent_id
			},
			function(res){
				if(res.success){
					var html = '<option value="">--选择市--</option>';
					if (is_city == 0) {
						html = '<option value="">--选择区/县--</option>';
					}
					for (var i = 0, data = res.data, len = data.length; i < len; i++){
						if (data[i]['id'] == next_select_val) {
							html += '<option selected="selected" value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
						} else {
							html += '<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
						}
					}
					$("#"+next_id).html(html);
					return false;
				}else{
					alert(res.message);
					return false;
				}
			},
			"json"
	);
}

function my_alert(field, is_focus, msg) {
    if (is_focus == 1) {
        $('#'+field).focus();
    }
    var d = dialog({
        fixed: true,
        width:300,
        title: '提示',
        content: msg
    });
    d.show();
    setTimeout(function () {
        d.close().remove();
    }, 3000);
    return false;
}

function my_alert_flush(field, is_focus, msg) {
    if (is_focus == 1) {
        $('#'+field).focus();
    }
    var d = dialog({
        fixed: true,
        width:300,
        title: '提示',
        content: msg
    });
    d.show();
    setTimeout(function () {
        window.location.reload();
        d.close().remove();
    }, 2000);
    return false;
}

function jump(page_uri, per_page) {
    var page = $('#page').val();
    if (page > 0 && page_uri) {
        page = (page - 1) * per_page;
        window.location.href = page_uri + page + '.html';
    }
}