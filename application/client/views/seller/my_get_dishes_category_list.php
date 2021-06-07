<div class="member_right mt20">
    <div class="box_shadow clearfix m_border">
        <div class="member_title"><span class="bt">分类设置</span>
        <span style="color:#9c9c9c;margin-left:50px;">注：产品分类设置，在发布商品页面可以快捷分类选项，在店铺导航栏左侧下显示。</span>
        <a href="<?php echo getBaseUrl(false, '', 'seller/my_save_dishes_category.html', $client_index); ?>" class="add_btn">+ 新增分类</a></div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="b_shop_table">
            <tr>
                <th width="20%"><a href="<?php echo $_SERVER['REQUEST_URI'];?>" style="color:#c81624;">点击排序</a>&nbsp;&nbsp;排序</th>
                <th width="30%">分类名称</th>
                <!-- <th width="20%">图片</th> -->
                <th width="30%">状态</th>
                <th width="30%">操作</th>
            </tr>
            <?php
            if ($item_list) {
                foreach ($item_list as $item) {
                    ?>
                    <tr class="form_tr">
                        <td align="center"><span class="input_sort" data-id="<?php echo $item['id']; ?>"><?php echo $item['sort']; ?></span></td>
                        <td width="30%"><?php echo $item['name']; ?></td>
                        <!-- <td align="center">
                            <?php
                            if ($item['path']) {
                                ?>
                                <img src="<?php echo str_replace('.', '_thumb.', $item['path']); ?>">
                            <?php } ?>
                        </td> -->
                        <td><?=$item['display'] ? '显示' : '隐藏'?></td>
                        <td class="link_action">
                            <!-- <a href="<?php echo getBaseUrl(false, '', 'seller/my_save_dishes_category/' . $item['id'] . '.html', $client_index); ?>">添加子分类</a>&nbsp; -->
                            <a href="<?php echo getBaseUrl(false, '', 'seller/my_save_dishes_category/0/' . $item['id'] . '.html', $client_index); ?>">修改</a>&nbsp;
                            <a href="javascript:void(0)" onclick="my_delete_dishes_category(<?php echo $item['id']; ?>, this)">删除</a>
                        </td>
                    </tr>

                <?php }
            } ?>
        </table>
    </div>
</div>
<style>
    .form_tr{
        line-height:30px;
    }
    .none_border{
        border-bottom: none;
    }
    .none_border td{
        border-bottom: none;
    }
    .b_shop_table td {
        padding:5px 0px;
    }
    .b_shop_table td img{
        height:40px;
    }
    .form_tr input{
        text-align:center;
    }
    .sort_btn{
        line-height: 24px;
        font-size: 14px;
        width: 50px;
        height: 24px;
        text-align: center;
        display: inline-block;
        background: #cc0011;
        color:#fff;
    }
    .input_sort{
        display:inline-block;
        width:49px;
        height:24px;
        line-height:24px;
        background-color:#E0E0E0;
        cursor:pointer;
        text-align:center;
    }
</style>
<script>
    function my_delete_dishes_category(id, obj) {
        var d = dialog({
            title: '提示',
            width: 300,
            fixed: true,
            content: '您确定要删除此分类吗？',
            okValue: '确定',
            ok: function () {
                $.post(base_url + 'index.php/seller/my_delete_dishes_category', {
                    'id': id
                }, function (data) {
                    if (data.success == false) {
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
                    }
                    $(obj).parents('tr').remove();
                }, 'json');
            },
            cancelValue: '取消',
            cancel: function () {
            }
        });
        d.show();
    }
    function my_sort(id, obj) {
        $.post(base_url + 'index.php/seller/my_change_dishes_category_sort', {'id': id, 'sort': $(obj).val()}, function (data) {
            if (data.success == false) {
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
            }
            var number = $(obj).val();
            var parent_node = $(obj).parent();
            $(obj).remove();
            parent_node.html('<span class="input_sort" data-id="' + id + '">' + number + '</span>');
        }, 'json');
    }
    $("td").delegate('.input_sort', 'click', function () {
        var number = $(this).html();
        var id = $(this).data('id');
        var parent_node = $(this).parent();
        $(this).remove();
        parent_node.html('<input size="4" type="text" value="' + number + '" class="input_txt" onblur="my_sort(' + id + ',this)">');
    })

</script>
