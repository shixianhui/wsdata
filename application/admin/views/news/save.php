<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<input name="id" value="" type="hidden">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
 	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>栏目</strong> <br/>
	  </th>
      <td>
      <input name="select_category_id" id="select_category_id" type="hidden" value="<?php if(! empty($itemInfo)){ echo $itemInfo['category_id'];} ?>" >
      <select class="input_blur" name="category_id" id="category" valid="required" errmsg="请选择栏目!">
       <option value="" >请选择栏目</option>
       <?php if (! empty($menuList)) { ?>
       <!-- 一级 -->
       <?php foreach ($menuList as $menu) { ?>
       <option <?php if ($menu['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $menu['id']; ?>"><?php echo $menu['menu_name']; ?></option>
       <!-- 二级 -->
       <?php foreach ($menu['subMenuList'] as $subMenu) { ?>
       <option <?php if ($subMenu['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $subMenu['id']; ?>">&nbsp;&nbsp;┣<?php echo $subMenu['menu_name']; ?></option>
       <!-- 三级 -->
       <?php foreach ($subMenu['subMenuList'] as $sSubMenu) { ?>
       <option <?php if ($sSubMenu['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $sSubMenu['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu['menu_name']; ?></option>
       <!-- 四级 -->
       <?php foreach ($sSubMenu['subMenuList'] as $sSubMenu4) { ?>
       <option <?php if ($sSubMenu4['subMenuList']) {echo 'disabled="disabled"';} ?> value="<?php echo $sSubMenu4['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu4['menu_name']; ?></option>
       <!-- 五级 -->
       <?php foreach ($sSubMenu4['subMenuList'] as $sSubMenu5) { ?>
       <option value="<?php echo $sSubMenu5['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<?php echo $sSubMenu5['menu_name']; ?></option>
       <?php }}}}}} ?>
      </select>
      </td>
    </tr>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>标题</strong> <br/>
	  </th>
      <td>
      <input name="title" id="title" value="<?php if(! empty($itemInfo)){ echo $itemInfo['title'];} ?>" size="80" maxlength="100" valid="required" errmsg="标题不能为空!" class="inputtitle input_blur" type="text">
	</td>
    </tr>
    <tr>
      <th width="20%"><strong>SEO标题</strong> <br/>
	  </th>
      <td>
      <input name="seo_title" id="seo_title" value="<?php if(! empty($itemInfo)){ echo $itemInfo['seo_title'];} ?>" size="80" class="input_blur" type="text">
	</td>
    </tr>
	<tr>
      <th width="20%">
      <strong>颜色</strong> <br/>
	  </th>
      <td>
    <select class="input_blur" name="title_color" id="title_color">
	<option value="<?php if(! empty($itemInfo)){echo $itemInfo['title_color'];} ?>" selected="selected">颜色</option>
	<option value="#000000" class="bg1"></option>
	<option value="#ffffff" class="bg2"></option>
	<option value="#008000" class="bg3"></option>
	<option value="#800000" class="bg4"></option>
	<option value="#808000" class="bg5"></option>
	<option value="#000080" class="bg6"></option>
	<option value="#800080" class="bg7"></option>
	<option value="#808080" class="bg8"></option>
	<option value="#ffff00" class="bg9"></option>
	<option value="#00ff00" class="bg10"></option>
	<option value="#00ffff" class="bg11"></option>
	<option value="#ff00ff" class="bg12"></option>
	<option value="#ff0000" class="bg13"></option>
	<option value="#0000ff" class="bg14"></option>
	<option value="#008080" class="bg15"></option>
	</select>
   </td>
  </tr>
  <tr>
    <th width="20%">
    <strong>缩略图</strong> <br/>
	</th>
    <td>
    <input name="model" id="model"  value="<?php echo $template; ?>" type="hidden" />
    <input name="path" id="path" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['path'];} ?>" type="text" />
    <input class="button_style" name="upload_image" id="upload_image" value="上传图片" style="width: 60px;"  type="button" />  <input class="button_style" value="浏览..."
style="cursor: pointer;" name="select_image" id="select_image" type="button" /> <input class="button_style" style="cursor: pointer;"  name="cut_image" id="cut_image" value="裁剪图片" type="button"  />
    </td>
    </tr>
    <tr>
        <th width="20%">
            <strong>批量上传图片</strong> <br/>
        </th>
        <td>
            <input name="batch_path_ids" id="batch_path_ids" size="50" class="input_blur" value="<?php if(! empty($itemInfo)){ echo $itemInfo['batch_path_ids'];} ?>" type="text" />
            <input class="button_style" name="batch_upload_image" id="batch_upload_image" value="批量添加" style="width: 60px;"  type="button" />
            <input class="button_style" value="浏览..." style="cursor: pointer;" name="batch_select_image" id="batch_select_image" type="button" />
            <font color="red">请上传3张或3张以上</font>
        </td>
    </tr>
    <tr>
      <th width="20%"> <strong>关键词</strong> <br/>
	  多关键词之间用逗号隔开
	  </th>
      <td>
      <input name="keyword" id="keyword" size="50"  value="<?php if(! empty($itemInfo)){ echo $itemInfo['keyword'];} ?>"  maxlength="100" class="input_blur" type="text" />
      </td>
    </tr>
	<tr>
      <th width="20%"> <strong>摘要</strong> <br/>
	  </th>
      <td>还可以输入 <font id="ls_description" color="#ff0000">255</font> 个字符！<br/>
      <textarea name="abstract" id="abstract" rows="4" cols="50"  class="textarea_style" style="width: 80%;" ><?php if(! empty($itemInfo)){ echo $itemInfo['abstract'];} ?></textarea>
 </td>
    </tr>
	<tr>
      <th width="20%"> <strong>内容</strong>
      </th>
      <td>
<?php echo $this->load->view('element/ckeditor_tool', NULL, TRUE); ?>
<script id="content" name="content" type="text/plain" style="width:800px;height:300px;"><?php if(! empty($itemInfo)){ echo html($itemInfo["content"]);}else{echo "";} ?></script>
<script type="text/javascript">
    var ue = UE.getEditor('content');
</script>
      </td>
    </tr>
   <tr>
    <th width="20%">
    <strong>自定义属性</strong> <br/>
	</th>
    <td>
    <label><input class="checkbox_style" name="custom_attribute[]" id="h"  value="h" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "h")>0){echo "checked=true";}} ?> type="checkbox"/> 首页置顶[h]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="c"  value="c" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "c")>0){echo "checked=true";}} ?> type="checkbox"/> 首页推荐[c]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="a"  value="a" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "a")>0){echo "checked=true";}} ?> type="checkbox"/> 特荐[a]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="f"  value="f" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "f")>0){echo "checked=true";}} ?> type="checkbox"/> 幻灯[f]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="s"  value="s" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "s")>0){echo "checked=true";}} ?> type="checkbox"/> 滚动[s]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="b"  value="b" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "b")>0){echo "checked=true";}} ?> type="checkbox"/> 加粗[b]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="p"  value="p" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "p")>0){echo "checked=true";}} ?> type="checkbox"/> 图片[p]</label>
	<label><input class="checkbox_style" name="custom_attribute[]" id="j"  value="j" <?php if(! empty($itemInfo)){if(substr_count($itemInfo['custom_attribute'], "j")>0){echo "checked=true";}} ?> type="checkbox"/> 跳转[j]</label>
    </td>
    </tr>
    <tr>
      <th width="20%"> <strong>相关内容</strong> <br/>
	  </th>
      <td>
      <input name="relation" id="relation" size="50" value="<?php if(! empty($itemInfo)){ echo $itemInfo['relation'];} ?>" class="input_blur" type="text" /> <font color="red">注：填写相关内容的ID，如“1,2,3,4”</font>
      </td>
    </tr>
    <tr>
      <th width="20%"> <strong>作者</strong> <br/>
	  </th>
      <td>
      <input name="author" id="author" size="30" value="<?php if(! empty($itemInfo)){ echo $itemInfo['author'];} ?>" maxlength="12" class="input_blur" type="text" />
      </td>
    </tr>
	<tr>
      <th width="20%">
      <strong>来源</strong> <br/>
	  </th>
      <td>
      <input name="source" id="source" size="30"  value="<?php if(! empty($itemInfo)){ echo $itemInfo['source'];} ?>"  maxlength="100" class="input_blur" type="text" />
      </td>
    </tr>
	<tr>
      <th width="20%"> <strong>浏览次数</strong> <br/>
	  </th>
      <td>
      <input class="input_blur" name="hits" id="hits<?php if(! empty($itemInfo)){ echo $itemInfo['hits'];} ?>" value="<?php if(! empty($itemInfo)){ echo $itemInfo['hits'];} ?>"  type="text" valid="required|isNumber" errmsg="浏览次数不能为空!|浏览次数必须为数字!"/>
      </td>
    </tr>
	<tr>
      <th width="20%"> <strong>发布时间</strong> <br/>
	  </th>
      <td>
	<input class="input_blur" name="add_time" id="add_time"  size="21" readonly="readonly" type="text"/>&nbsp;
          <script language="javascript" type="text/javascript">
              datetime = "<?php if(! empty($itemInfo)){ echo date('Y-m-d H:i:s', $itemInfo['add_time']);} ?>";
              date = new Date();
              if (datetime == "" || datetime == null) {
                  datetime = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
              }
              document.getElementById ("add_time").value =datetime;
          </script>
	 </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
	  <input class="button_style" name="dosubmit" value=" 保存 " type="submit">
	  &nbsp;&nbsp; <input onclick="javascript:window.location.href='<?php echo $prfUrl; ?>';" class="button_style" name="reset" value=" 返回 " type="button">
	  </td>
    </tr>
</tbody>
</table>
</form>