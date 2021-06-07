<?php echo $tool; ?>
<form name="search" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
    <table class="table_form" cellpadding="0" cellspacing="1">
        <caption>信息查询</caption>
        <tbody>
        <tr>
            <td class="align_c">
                用户名
                <input class="input_blur" name="username" id="username" size="20" type="text">&nbsp;
                真实姓名
                <input class="input_blur" name="real_name" id="real_name" size="20" type="text">&nbsp;
                <!--<select name="category_id" onchange="#">-->
                <!--<option value="">--选择管理组--</option>-->
                <?php //if (! empty($usergroupList)): ?>
                <?php //foreach ($usergroupList as $usergroup): ?>
                <!--<option value="--><?php //echo $usergroup['id'] ?><!--" >--><?php //echo $usergroup['group_name'] ?><!--</option>-->
                <?php //endforeach; ?>
                <?php //endif; ?>
                <!--</select>-->
                <select class="input_blur" name="is_store">
                    <option value="">是否为商家</option>
                    <option value="1">是</option>
                    <option value="0">否</option>
                </select>&nbsp;
                <select class="input_blur" name="display">
                    <option value="">选择状态</option>
                    <?php if($displayArr){
                        foreach ($displayArr as $key => $value){
                            ?>
                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php }} ?>
                </select>&nbsp;
                添加时间 <input class="input_blur" name="inputdate_start" id="inputdate_start" size="10" readonly="readonly" type="text">&nbsp;
                 - <input class="input_blur" name="inputdate_end" id="inputdate_end" size="10"  readonly="readonly" type="text">&nbsp;
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
        <th width="30">选中</th>
        <th width="40">ID</th>
        <th>昵称</th>
        <th width="100">权益</th>
        <th width="100">真实姓名</th>
        <th width="100">手机</th>
        <th width="150">身份证</th>
        <th width="100">所在地</th>
        <th width="80">余额</th>
        <th width="150">推荐人</th>
        <th width="80">添加时间</th>
        <th width="80">状态</th>
        <th width="100">管理操作</th>
    </tr>
    <?php if (! empty($userList)): ?>
        <?php foreach ($userList as $key=>$value): ?>
            <tr id="id_<?php echo $value['id']; ?>"  onMouseOver="this.style.backgroundColor='#ECF7FE'" onMouseOut="this.style.background=''">
                <td><input  class="checkbox_style" name="ids" value="<?php echo $value['id']; ?>" type="checkbox"></td>
                <td><?php echo $value['id']; ?></td>
                <td><?php echo $value['nickname']; ?>   <?php if ($value['store_info']){ ?><font color="red">[商家]</font><?php } ?></td>
                <td class="align_c"><?php echo $value['type_format']; ?></td>
                <td class="align_c"><?php echo $value['real_name']; ?></td>
                <td class="align_c"><?php echo $value['mobile']; ?></td>
                <td class="align_c"><?php echo $value['id_card']; ?></td>
                <td class="align_c"><?php echo $value['address']; ?></td>
                <td class="align_c"><?php echo number_format($value['total'], 2, '.', ''); ?></td>
                <td class="align_c"><?php if ($value['parent_id']){ echo $value['parent_name'].'[ID:'.$value['parent_id'].']'; } ?></td>
                <td class="align_c"><?php echo date('Y-m-d H:i:s', $value['add_time']); ?></td>
                <td class="align_c"><?php echo $displayArr[$value['display']]; ?></td>
                <td class="align_c">
                    <!--<a href="admincp.php/financial/recharge/--><?php //echo $value['id']; ?><!--">充值</a>-->
                    <!--<a href="admincp.php/financial/debit/--><?php //echo $value['id']; ?><!--">扣款</a><br/>-->
                    <a href="admincp.php/financial/index/<?php echo $value['id']; ?>">财务记录</a> |
                    <a href="admincp.php/user/pre_save/<?php echo $value['id']; ?>">修改</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div class="button_box">
<span style="width: 60px;"><a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', true)">全选</a>/
<a href="javascript:void(0);" onclick="javascript:$('input[type=checkbox]').prop('checked', false)">取消</a></span>
    <input class="button_style" name="delete" id="delete" value=" 删除 "  type="button">
    <!--批量移动至：-->
    <!--<select name="category_id" id="category_id" onchange="#">-->
    <!--<option value="">--选择管理组--</option>-->
    <?php //if (! empty($usergroupList)): ?>
    <?php //foreach ($usergroupList as $usergroup): ?>
    <!--<option value="--><?php //echo $usergroup['id'] ?><!--" >--><?php //echo $usergroup['group_name'] ?><!--</option>-->
    <?php //endforeach; ?>
    <?php //endif; ?>
    <!--</select>-->
    <select class="input_blur" name="select_display" id="select_display" onchange="#">
        <option value="">选择状态</option>
        <?php if($displayArr){
            foreach ($displayArr as $key => $value){
                ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php }} ?>
    </select>
</div>
<div id="pages" style="margin-top: 5px;">
    <?php echo $pagination; ?>
    <a>总条数：<?php echo $paginationCount; ?></a>
    <!-- <a>总页数：<?php echo $pageCount; ?></a> -->
</div>
<br/><br/>