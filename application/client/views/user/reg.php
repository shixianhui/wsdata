<section class="warp">
    <div class="log_box">
        <h3 >已有账号，<a href="<?php echo getBaseUrl($html, 'user/login.html', 'user/login.html', $client_index); ?>" class="red">直接登录</a></h3>
        <form action="<?php echo getBaseUrl(false, "", "user/reg.html", $client_index); ?>" method="post" id="jsonForm" name="jsonForm">
            <ul class="log_form">
                <Li><input type="text" id="mobile" name="mobile" placeholder="请输入手机号码" valid="required|isMobile" errmsg="手机号不能为空|请输入正确的手机号" class="log_txt"></Li>
                <Li><input type="password" id="password" name="password" placeholder="密码由6-20位字母、数字和符号组合" valid="required" errmsg="密码不能为空" class="log_txt"></Li>
                <Li><input type="password" id="ref_password" name="ref_password" placeholder="请再次输入密码" class="log_txt" valid="eqaul" eqaulName="ref_password" errmsg="密码前后不一致"></Li>
                <Li class="clearfix">
                    <input type="text" class="log_txt" valid="required" errmsg="验证码不能为空" maxlength="4" name="code" id="code" placeholder="输入验证码" style="width:100px;">
                    <img  id="valid_code_pic" src="<?php echo getBaseUrl(false, "", "verifycode/index/1", $client_index); ?>" alt="看不清，换一张" onclick="javascript:this.src = this.src + 1;" />
                    <a style="color:#333;" onclick="javascript:document.getElementById('valid_code_pic').src = document.getElementById('valid_code_pic').src + 1;" href="javascript:void(0);">换一张</a>
                </Li>
                <Li class="clearfix"><input type="text" id="smscode" name="smscode" valid="required" errmsg="短信验证码不能为空" placeholder="短信验证码" class="log_txt" style=" width:140px; float:left; margin-right:20px;"><a href="javascript:void(0)" class="getyzm" id="getyzm">获取验证码</a></Li>
                <div class="clear"></div>
                <li ><a href="javascript:void(0)" onclick="$('#jsonForm').submit();" class="btn_r">立即注册</a></li>
                <li><label><input checked="checked" name="remember" value="1" type="checkbox">我已认真阅读并接受<a href="index.php/page/index/292/52.html" target="_blank" class="red">《凑活平台用户注册协议》</a>、<a href="index.php/page/index/292/41.html" target="_blank" class="red">《凑活平台隐私条款》</a></label></li>
            </ul>
        </form>
    </div>
    <div class="log_img">
    <?php $adList = $this->advdbclass->getAd(6, 1);
if ($adList) {
foreach ($adList as $key=>$value) {
	?>
<img alt="<?php echo clearstring($value['ad_text']); ?>" src="<?php echo $value['path']; ?>">
<?php }} ?>
    </div>
</section>
<script>
    var times = 60, cuttime;
    function getyzm(idn) {
        times--;
        if (times > 0 && times < 60) {
            $(idn).text(times + "秒后重新获取");
            $(idn).addClass("fail");
            cuttime = setTimeout(function () {
                getyzm(idn)
            }, 1000);
        } else {
            $(idn).text("获取短信验证码");
            times = 60;
            $(idn).removeClass("fail");
            clearTimeout(cuttime);
        }
    }
    $(function () {
        $("#getyzm").bind("click", function () {
            var message = '';
            var mobile = $("input[name=mobile]").val();
            if (!/^1[356789]\d{9}$/.test(mobile)) {
                message = '手机号码格式不正确';
                var d = dialog({
                    width: 300,
                    title: '提示',
                    fixed: true,
                    content: message
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 2000);
                return false;
            }
            var password = $("input[name=password]").val();
            if (password.length < 6 || password.length > 20) {
                message = '密码由6-20位字母、数字和符号组合';
                var d = dialog({
                    width: 300,
                    title: '提示',
                    fixed: true,
                    content: message
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 2000);
                return false;
            }
            var ref_password = $("input[name=ref_password]").val();
            if (ref_password != password) {
                message = '密码不一致';
                var d = dialog({
                    width: 300,
                    title: '提示',
                    fixed: true,
                    content: message
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 2000);
                return false;
            }
            var code = $("input[name=code]").val();
            if (!/^\w{4}$/.test(code)) {
                message = '请正确填写验证码';
                var d = dialog({
                    width: 300,
                    title: '提示',
                    fixed: true,
                    content: message
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                }, 2000);
                return false;
            }
            if (times == 60) {
                $.ajax({
                    url: base_url + 'index.php/user/get_reg_sms_code',
                    type: 'post',
                    data: {
                        type: "reg",
                        mobile: mobile,
                        code: code
                    },
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            getyzm("#getyzm");
                        }
                        var d = dialog({
                            width: 300,
                            title: '提示',
                            fixed: true,
                            content: json.message
                        });
                        d.show();
                        setTimeout(function () {
                            d.close().remove();
                        }, 2000);
                    }
                })
            }
            return false;
        });
    });
</script>