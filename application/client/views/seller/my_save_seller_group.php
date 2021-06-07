<div class="member_right mt20">
    <div class="box_shadow clearfix m_border">
        <div class="member_title"><span class="bt">新增部门</span><span style="float: right;font-size:16px;"><a href="<?php echo getBaseUrl(false, '', 'seller/my_seller_group_list.html', $client_index) ?>" style="color:#333;">返回</a></span></div>
<!--       <form action="--><?php //echo $_SERVER['REQUEST_URI'];?><!--" method="post" name="jsonForm" id="jsonForm">-->
        <div class="b_shop_update">
            <ul class="m_form fl">
                <input name="id" id="id" value="<?php if(! empty($item_info)){ echo $item_info['id'];} ?>" type="hidden">
                <li class="clearfix"><span>部门名称：</span><input type="text" name="group_name" id="group_name" value="<?php if($item_info){ echo $item_info['group_name'];}?>" valid="required" maxlength="100" errmsg="部门名称不能为空" class="input_txt">

                </li>
                <li class="clearfix"><span>权限设置：</span>
                    <font color="red" style="font-size: 14px">注：查看权限优先；删除，修改等权限一般在列表上才能看到</font><br/>
                    <div id="permissions_tree" style="margin-left: 100px;"></div>
                </li>

            </ul>
        </div>
        <div class="clear"></div>
        <div style="margin:20px 0px 20px 200px; clear:both; display:block;">
            <a href="javascript:void(0);" class="b_btn" id="btn_admin_group_save">确认提交</a>
        </div>
<!--        </form>-->
    </div>
</div>
<script type="text/javascript" src="js/admin/jquery.tree.js"></script>
<script type="text/javascript" src="js/admin/jquery.tree.checkbox.js"></script>
<script>
    //==================================管理员组权限====================================
    var permissions = "<?php if(! empty($item_info)){ echo $item_info['permissions'];} ?>".split(',');
    var permissionsList = [{
        data: '店铺管理 ',
        attributes:{'permission':'seller_g'},
        state: "close",
        children:[{
            data: '我的店铺',
            attributes:{'permission':'seller'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'seller_index'}},
                {data: '添加',attributes:{'permission':'seller_add'}},
                {data: '修改',attributes:{'permission':'useller_edit'}},
                {data: '删除',attributes:{'permission':'seller_delete'}}]
        }]
    },{
        data: '菜品管理 ',
        attributes:{'permission':'dishes_g'},
        state: "close",
        children:[{
            data: '菜品列表',
            attributes:{'permission':'dishes'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'dishes_index'}},
                {data: '添加',attributes:{'permission':'dishes_add'}},
                {data: '修改',attributes:{'permission':'dishes_edit'}},
                {data: '删除',attributes:{'permission':'dishes_delete'}}]
        },{
            data: '分类设置',
            attributes:{'permission':'dishes_category'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'dishes_category_index'}},
                {data: '添加',attributes:{'permission':'dishes_category_add'}},
                {data: '修改',attributes:{'permission':'dishes_category_edit'}},
                {data: '删除',attributes:{'permission':'dishes_category_delete'}}]
        },{
            data: '新增套餐',
            attributes:{'permission':'combos'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'combos_index'}},
                {data: '添加',attributes:{'permission':'combos_add'}},
                {data: '修改',attributes:{'permission':'combos_edit'}},
                {data: '删除',attributes:{'permission':'combos_delete'}}]
        },{
            data: '新增吆喝',
            attributes:{'permission':'share_goods'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'share_goods_index'}},
                {data: '添加',attributes:{'permission':'share_goods_add'}},
                {data: '修改',attributes:{'permission':'share_goods_edit'}},
                {data: '删除',attributes:{'permission':'share_goods_delete'}}]
        },{
            data: '新增种草',
            attributes:{'permission':'grasses'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'grasses_index'}},
                {data: '添加',attributes:{'permission':'grasses_add'}},
                {data: '修改',attributes:{'permission':'grasses_edit'}},
                {data: '删除',attributes:{'permission':'grasses_delete'}}]
        }]
    },{
        data: '交易管理 ',
        attributes:{'permission':'order_g'},
        state: "close",
        children:[{
            data: '订单列表',
            attributes:{'permission':'order'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'order_index'}},
                {data: '添加',attributes:{'permission':'order_add'}},
                {data: '修改',attributes:{'permission':'order_edit'}},
                {data: '删除',attributes:{'permission':'order_delete'}}]
        }]
    },{
        data: '子账号管理 ',
        attributes:{'permission':'seller_group_g'},
        state: "close",
        children:[{
            data: '部门设置',
            attributes:{'permission':'seller_group'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'seller_group_index'}},
                {data: '添加',attributes:{'permission':'seller_group_add'}},
                {data: '修改',attributes:{'permission':'seller_group_edit'}},
                {data: '删除',attributes:{'permission':'seller_group_delete'}}]
        },{
            data: '账号管理',
            attributes:{'permission':'user'},
            state: "open",
            children:[{data: '查看',attributes:{'permission':'user_index'}},
                {data: '添加',attributes:{'permission':'user_add'}},
                {data: '修改',attributes:{'permission':'user_edit'}},
                {data: '删除',attributes:{'permission':'user_delete'}}]
        }]
    }];
    if ($("#permissions_tree").size()) {
        $("#permissions_tree").tree({
            data: {
                'type': "json",
                opts: {
                    'static': {
                        data: "所有权限",
                        children: permissionsList,
                        state: "open"
                    }
                }
            },
            ui: {
                theme_name: "checkbox"
            },
            plugins: {
                checkbox: {}
            },
            types: {
                'default':{
                    draggable	: false,
                }
            }
        });
        if(permissions){
            $.each($("#permissions_tree li"),function(i,n){
                if(jQuery.inArray($(n).attr('permission'),permissions)!=-1){
                    $(n).children('a')[0].className = 'checked';
                }
            });

        }
    }
    $(document).ready(function() {
        $("#btn_admin_group_save").click(function(){
            var $id = $("#id").val();
            var $group_name = $("#group_name").val();
            var $permission = '';
            $.each($("#permissions_tree a.checked"),function(i,n){
                if($(n).parent().attr('permission')){
                    $permission += $(n).parent().attr('permission')+',';
                }
            })
            if (! $group_name) {
                alert('部门名称不能为空！');
                $("#group_name").focus();
                return false;
            }
            if (! $permission) {
                alert('权限设置不能为空！');
                return false;
            }
            $.post(base_url+"index.php/"+controller+"/my_save_seller_group/"+$id,
                {	"group_name": $group_name,
                    "permissions": $permission.substr(0, $permission.length-1)
                },
                function(res){
                    if(res.success){
                        window.location.href = res.field;
                        return false;
                    }else{
                        alert(res.message);
                        return false;
                    }
                },
                "json"
            );
        });
    });
</script>
