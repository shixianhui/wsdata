<style>
    .but_4 {
        background: #c81624 none repeat scroll 0 0;
        border-radius: 6px;
        color: #fff;
        display: inline-block;
        font: 1.2em/32px "微软雅黑";
        height: 32px;
        margin-left: 10px;
        padding: 0 10px;
        position: relative;
        cursor: pointer;
    }

    .pic_info {
        display: inline-block;
        width: 68px;
        height: 80px;
        text-align: center;
        position: relative;
        margin-top: 5px;
    }

    .action_bar {
        position:absolute; background:#e8e8e8;opacity:1;width:100%;height:19px;bottom:20px;z-index:1;display: none;
    }

    .picture span.icon_arrow {
        width: 30px;
        height: 50px;
        display: inline-block;
        background-position: 0 0;
        bottom: -60px;
        position: absolute;
        background: url(images/default/icon1.png) no-repeat;
        opacity: 0.7;
    }

    #pic_list {
        overflow: hidden;
        max-height: 80px;
        margin: 0 20px;
        white-space: nowrap;
    }

    .pic_img {
        border: 1px solid red;
    }

    img.del_pic {
        cursor: pointer;
        position: absolute;
        right: 7px;
        width: 13px;
        height: 13px;
    }

    img.select_pice_path {
        cursor: pointer;
        position: absolute;
        right: 50px;
        width: 13px;
        height: 13px;
    }

    .b_shop_update .picture img.select_pice_path {
        width: 13px;
        height: 13px;
    }

    .b_shop_update .picture img.del_pic {
        width: 13px;
        height: 13px;
    }

    .m_form li span.pri_text {
        text-align: right;
        padding-left: 3px;
        float: left;
        /*margin-right: 25px;*/
    }

    .hd {
        border-bottom: #e8e8e8 1px solid;
        height: 34px;
        line-height: 32px;
        position: relative;
        margin-top: 20px;
    }

    .hd ul {
        float: left;
        position: absolute;
        left: 0;
        top: -1px;
    }

    .hd ul li {
        float: left;
        text-align: center;
        width: 118px;
        cursor: pointer;
        font-size: 14px;
        color: #666;
        border: transparent 1px solid;
        background: #fff;
    }

    .hd ul li.on {
        border: #c81624 1px solid;
        border-bottom: #fff 1px solid;
        line-height: 34px;
        background: #fff;
        color: #c81624;
    }

    .m_form li.free_exp_tr {
        display: none;
    }

    .m_form li.bonus_tr {
        display: none;
    }
</style>
<div class="member_right mt20">
    <div class="box_shadow clearfix m_border">
        <div class="member_title"><span class="bt">新增套餐</span></div>
        <div class="hd">
            <ul>
                <li class="on">商品信息</li>
                <li>商品描述</li>
            </ul>
        </div>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="jsonForm" id="jsonForm">
            <div style="margin-top: 30px;">
                <h3>基本信息</h3>
                <div class="b_shop_update">
                    <div class="picture mt20" style="position:relative;">
                        <img src="<?php if ($item_info) {
                                        echo str_replace('.', '_thumb.', $item_info['cover_image']);
                                    } ?>" onerror="this.src='/images/default/default.png'">
                        <div id="pic_list" style="float: left;max-width:210px;">
                            <?php
                            if ($attachment_list) {
                                foreach ($attachment_list as $key => $value) {
                            ?>
                                    <div class="pic_info" onmouseenter="enter_pic(this)" onmouseleave="leave_pic(this)">
                                        <img src="<?php echo preg_replace('/\./', '_thumb.', $value['path']); ?>" style="width: 60px;height: 60px" id="list_path" onclick="select_img(this)" />
                                        <input type="hidden" name="batch_path_ids[]" value="<?php echo $value['id']; ?>" />
                                        <input type="hidden" id="select_path" value="<?php echo $value['path']; ?>" />
                                        <p class="action_bar">
                                            <img onclick="javascript:del_pice(this);" <?php if ($value['path'] == $item_info['cover_image']) { ?> data-selected="1" <?php } ?> class="del_pic" src="images/default/close.png" title="删除">
                                            <img onclick="javascript:select_pice(this);" class="select_pice_path" src="images/default/open.png" title="设为封面" id="select_pice_path" <?php if ($value['path'] == $item_info['cover_image']) { ?>style="display: none" <?php } ?>>
                                        </p>

                                    </div>
                            <?php }
                            } ?>
                        </div>
                        <span id="last" class="icon_arrow" style="cursor:pointer; left:0px; transform:rotate(180deg); -moz-transform:rotate(180deg);-webkit-transform:rotate(180deg);display: none"></span>
                        <span id="next" class="icon_arrow" style="cursor:pointer;right:0px;display: none"></span>
                        <a style="position:relative; width:auto;background:none;clear:both;">
                            <span class="but_4">批量上传图片<input style="cursor:pointer;left:0px;top:0px; background:#000; width:100%;height:36px;line-height:36px; position:absolute;filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept="image/*" multiple="multiple" id="path_file" name="path_file[]"></span>
                            <i class="load" id="path_load" style="cursor:pointer;display:none;width:auto;padding-left:0px; left:50%; margin-left:-16px;margin-top:0px;index: 9999;position: absolute;"><img src="images/admin/loading_2.gif" style="width: 32px;height: 32px"></i>
                        </a>
                        <input type="hidden" name="path" value="<?php if ($item_info) {
                                                                    echo $item_info['cover_image'];
                                                                } ?>">
                    </div>
                    <ul class="m_form fl" style="width:67%;">
                        <li class="clearfix"><span>商品名称：</span><input type="text" name="title" value="<?php if ($item_info) {
                                                                                                            echo $item_info['name'];
                                                                                                        } ?>" id="title" valid="required" errmsg="商品名称不能为空!" class="input_txt">
                            <font color="#cc0011">*</font>
                        </li>
                        <li class="clearfix" id="pri_input"><span>价格：</span><input type="text" name="price" value="<?php if ($item_info) {
                                                                                                                        echo $item_info['price'];
                                                                                                                    } ?>" id="sell_price" class="input_txt" valid="isNumber" errmsg="请正确填写价格!" style="width: 100px">&nbsp;元</li>
                        <li class="clearfix" id="pri_input"><span>库存：</span><input type="text" name="stock" value="<?php if ($item_info) {
                                                                                                                        echo $item_info['stock'];
                                                                                                                    } ?>" id="stock" class="input_txt" valid="isNumber" errmsg="库存请填写大于0的数字!" style="width: 100px"></li>
                        <li class="clearfix"><span>商品标签：</span>
                            <div class="xm-select fl">
                                <div class="dropdown">
                                    <label class="iconfont" for="display"></label>
                                    <select id="tag_id" name="tag_id">
                                    <option value="">请选择商品标签</option>
                                    <?php if($tags_list) {
                                        foreach($tags_list as $value){ ?>
                                        <option value="<?=$value['id']?>" <?=!empty($item_info) && $item_info['tag_id'] == $value['id'] ? "selected='selected'" : ''?>><?=$value['name']?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="clearfix">
                            <span>属性：</span>
                            <label><input name="attribute[]" value="hot" type="checkbox" size="10" <?php if($item_info && in_array('hot', explode(',', $item_info['attribute']))){ echo 'checked="checked"';} ?>> 热推</label>
                            <label><input name="attribute[]" value="ms" type="checkbox" size="10" <?php if($item_info && in_array('ms', explode(',', $item_info['attribute']))){ echo 'checked="checked"';} ?>> 秒杀</label>
                        </li>
                        <li class="clearfix"><span>地址：</span><input type="text" name="address" value="<?php if ($item_info) {
                                                                                                            echo $item_info['address'];
                                                                                                        } ?>" id="address" class="input_txt" style="width: 350px;">
                        </li>
                        <li class="clearfix"><span>状态：</span>
                            <div class="xm-select fl">
                                <div class="dropdown">
                                    <label class="iconfont" for="display"></label>
                                    <select id="display" name="display">
                                        <option value="1" <?=empty($item_info) || $item_info['display'] == 1 ? "selected='selected'" : ''?>>上架</option>
                                        <option value="0" <?=!empty($item_info) && $item_info['display'] == 0 ? "selected='selected'" : ''?>>下架</option>
                                    </select>
                                </div>
                            </div>
                        </li>
                        <li class="clearfix">
                            <span>加入吆喝：</span>
                            <label><input name="is_shared" value="1" type="checkbox" size="10" <?php if($item_info && $item_info['is_shared']){ echo 'checked="checked"';} ?> onclick="change_shared(this)"> 是</label>
                        </li>
                        <li class="clearfix" id="shared" <?=$item_info && $item_info['is_shared'] ? '':'style="display:none;"'?>><span>佣金：</span><input type="text" name="reward" value="<?php if($item_info){ echo $item_info['reward'];} ?>" id="reward" class="input_txt" style="width: 100px">&nbsp;元</li>
  

                    </ul>
                </div>
                <div class="clear"></div>

                <ul class="m_form" style="padding-left:0px;margin-top:120px;">
                <h3 style="margin-bottom: 10px;">套餐内容</h3>
                    <div style="width:400px;margin:0px 0px 10px 105px;">
                        <select id="pre-selected-options" multiple="multiple" name="dishes_ids[]">
                          <?php if (! empty($dishes_list)) { ?>
                                    <?php foreach ($dishes_list as $item) {
                                          $selector = '';
                                          if ($item_info) {
                                                  if(in_array($item['id'], explode(',', $item_info['dishes_ids']))){ $selector = 'selected="selected"';}
                                          }
                                     ?>
                                     <option <?php echo $selector; ?> value="<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>"><?php echo my_substr($item['name'],20).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;¥'.floatval($item['price']); ?></option>
                                        <?php }} ?>
                         </select>
                </div>
                <li class="clearfix" id="pri_input"><span>原价：</span><input type="text" name="original_price" value="<?php if ($item_info) {
                                                                                                                        echo $item_info['original_price'];
                                                                                                                    }else{ echo 0;} ?>" id="original_price" class="input_txt" valid="isNumber" errmsg="请正确填写价格!" style="width: 100px">&nbsp;元</li>
                    <h3 style="margin: 10px 0;">购买须知</h3>
                    <?php echo $this->load->view('element/ckeditor_tool', NULL, TRUE); ?>
                <script id="usage_rules" name="usage_rules" type="text/plain"><?php if ($item_info) {
                                                                            echo html($item_info['usage_rules']);
                                                                        } ?></script>
                <script type="text/javascript">
                    var ue = UE.getEditor('usage_rules', {
                        toolbars: [
                            ['source', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'fullscreen']
                        ],
                        initialFrameWidth: 930,
                        initialFrameHeight: 450
                    });
                </script>
                </ul>
                <div class="clear"></div>

                <div style="text-align:center; margin:20px 0px; clear:both; display:block;"><a href="javascript:void(0)" onclick="$('#jsonForm').submit();" class="b_btn">确认提交</a></div>
            </div>
            <div style="display: none">
                <h3 class="mt30" style="margin-bottom: 10px;">商品描述(<span style="font-size:14px;color:#cc0011;">内容最大宽928px</span>)</h3>
                <script id="content" name="content" type="text/plain"><?php if ($item_info) {
                                                                            echo html($item_info['content']);
                                                                        } ?></script>
                <script type="text/javascript">
                    var ue = UE.getEditor('content', {
                        toolbars: [
                            ['source', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', 'simpleupload', 'insertimage', 'template', 'link', 'unlink', '|', 'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', 'fullscreen']
                        ],
                        initialFrameWidth: 930,
                        initialFrameHeight: 450
                    });
                </script>
                <div class="clear"></div>
                <div style="text-align:center; margin:20px 0px; clear:both; display:block;"><a href="javascript:void(0)" onclick="$('#jsonForm').submit();" class="b_btn">确认提交</a></div>
            </div>

        </form>
    </div>

</div>
<script type="text/javascript" src="js/default/jquery.quicksearch.js"></script>
<link href="css/default/multi_select.css" rel="stylesheet" type="text/css"/>
<script src="js/default/jquery.multi-select.js" type="text/javascript"></script>
<script type="text/javascript">
    $('#pre-selected-options').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='搜索'>",
  selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='搜索'>",
  afterInit: function(ms){
    var that = this,
        $selectableSearch = that.$selectableUl.prev(),
        $selectionSearch = that.$selectionUl.prev(),
        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
    .on('keydown', function(e){
      if (e.which === 40){
        that.$selectableUl.focus();
        return false;
      }
    });

    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
    .on('keydown', function(e){
      if (e.which == 40){
        that.$selectionUl.focus();
        return false;
      }
    });
  },
  afterSelect: function(value, price){
    this.qs1.cache();
    this.qs2.cache();
    var original_price = $('#original_price').val();
    $('#original_price').val(parseFloat(original_price)+parseFloat(price))
  },
  afterDeselect: function(value, price){
    this.qs1.cache();
    this.qs2.cache();
    var original_price = $('#original_price').val();
    $('#original_price').val(parseFloat(original_price)-parseFloat(price))
  }
    });

    document.getElementById('next').onclick = function() {
        document.getElementById('pic_list').scrollLeft = document.getElementById('pic_list').scrollLeft + 70;
    }
    document.getElementById('last').onclick = function() {
        document.getElementById('pic_list').scrollLeft = document.getElementById('pic_list').scrollLeft - 70;
    }

    $(document).ready(function() {
        if ($('#pic_list').find('.pic_info').length) {
            $('#last').show();
            $('#next').show();
        }
    });

    $('#pic_list').bind('DOMNodeInserted', function(e) {
        $('#last').show();
        $('#next').show();
    });

    function enter_pic(obj) {
        $(obj).find('p').slideDown('slow');
    }

    function leave_pic(obj) {
        $(obj).find('p').slideUp('slow');
    }

    function del_pice(obj) {
        var data_selected = $(obj).data("data-selected");
        if (data_selected == 1 && typeof(data_selected) != "undefined") {
            $("input[name='path']").val('');
        }
        $(obj).parent().parent().remove();
    }

    function select_pice(obj) {
        var path = $(obj).parent().siblings("#select_path").val();
        $(".picture img:first").attr('src', path);
        $("input[name='path']").val(path);
        $("#pic_list").find(".pic_info").each(function() {
            $(this).find('#select_pice_path').css('display', '');
            $(this).find('.del_pic').removeData('selected');
        });
        $(obj).prev('.del_pic').data("data-selected", "1");
        $(obj).hide();
    }
    //参数mulu
    $(function() {
        //形象照片
        $("input[name='path_file[]']").wrap("<form id='path_upload' action='<?php echo base_url(); ?>index.php/upload/batch_uploadImage' method='post' enctype='multipart/form-data'></form>");
        $("#path_file").change(function() { //选择文件
            $("#path_upload").ajaxSubmit({
                dataType: 'json',
                data: {
                    'model': 'combos',
                    'field': 'path_file'
                },
                beforeSend: function() {
                    $("#path_load").show();
                },
                uploadProgress: function(event, position, total, percentComplete) {},
                success: function(res) {
                    $("#path_load").hide();
                    if (res.success) {
                        var html = '';
                        for (var i = 0; i < res.data.length; i++) {
                            html += '<div class="pic_info" onmouseenter="enter_pic(this)" onmouseleave="leave_pic(this)">';
                            html += '<img src="' + res.data[i].file_path.replace('.', '_thumb.') + '" style="width: 60px;height: 60px" id="list_path" onclick="select_img(this)"/>';
                            html += '<input type="hidden" name="batch_path_ids[]" value="' + res.data[i].id + '" />';
                            html += '<input type="hidden" id="select_path" value="' + res.data[i].file_path + '" />';
                            html += '<p class="action_bar">';
                            html += '<img onclick="javascript:del_pice(this);" style="cursor: pointer;position:absolute;right:7px;bottom:3px;width: 13px;height: 13px;z-index:3;" src="images/default/close.png" title="删除">';
                            html += '<img onclick="javascript:select_pice(this);" style="cursor: pointer;position:absolute;width: 13px;right: 50px;bottom:3px;height: 13px;z-index:3;" src="images/default/open.png" title="设为封面" id="select_pice_path">';
                            html += '</p>';
                            html += '</div>';
                        }
                        $("#pic_list").append(html);
                        var src = res.data[0].file_path.replace('.', '_thumb.');
                        $(".picture img:first").attr('src', src);
                    } else {
                        return my_alert('fail', 0, res.message);
                    }
                },
                error: function(xhr) {}
            });
        });

        $('.hd ul li').click(function() {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $('#jsonForm').children('div').hide();
            $('#jsonForm').children('div').eq($(this).index()).show();
        })
    });

    function select_img(obj) {
        var src = $(obj)[0].src;
        $("#pic_list .pic_info #list_path").removeClass('pic_img');
        $(obj).addClass('pic_img');
        $(".picture img:first").attr('src', src);
    }

    function change_shared(obj) {
        if ($(obj).attr('checked')){
            $('#shared').show();
        } else {
            $('#shared').hide();
        }
    }
</script>