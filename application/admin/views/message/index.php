<div class="layuimini-container">
    <div class="layuimini-main">
      <table cellpadding="0" cellspacing="0" class="table_info" style="width:100%;text-align:center;">
        <caption>
        提示信息
        </caption>
        <tr>
          <td height="60" valign="middle" class="align_c"><?php if (isset($msg)){echo $msg;} ?></td>
        </tr>
        <tr>
          <td height="20" valign="middle" class="align_c">
          <?php if ($url == "goback") { ?>
          <a href="javascript:void(0);" id="goBack" onclick="tools.goBack()">[ 点这里关闭当前页 ]</a>
          <?php } else if (isset($url)) {?>
          <a href="<?php echo $url;?>">如果您的浏览器没有自动跳转，请点击这里</a>
          <script>window.setTimeout("window.location.href='<?php echo $url;?>'",3000);</script>
          <?php } ?>
          </td>
        </tr>
      </table>
    </div>
</div>
<script src="js/admin/lib/layui-v2.6.3/layui.js" charset="utf-8"></script>
<script src="js/admin/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
    layui.use(['miniTab'],function(){
        var layer = layui.layer,
            miniTab = layui.miniTab,
            $ = layui.$;

        $(document).on('click','#goBack',function(){
            layer.close(layer.index);
            //当你在iframe页面关闭自身时
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭 
            miniTab.deleteCurrentByIframe();
        });

        //onclick的写法
        var _tools = {
            goBack: function(){
                console.log('关闭当前页');
            }
        }
        window.tools = _tools;
    });
</script>