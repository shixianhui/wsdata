<?php echo $tool; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id="jsonForm" >
<input name="id" value="" type="hidden">
<table class="table_form" cellpadding="0" cellspacing="1">
  <caption>基本信息</caption>
 	<tbody>
 	<tr>
      <th width="20%">
      <strong>分类</strong> <br/>
	  </th>
      <td>
      <select class="input_blur" name="country" id="country" onchange="javascript:get_country();">
       <option value="0">无(请选择一级分类)</option>
       <?php if (! empty($treeList)) { ?>
       <!-- 一级 -->
       <?php foreach ($treeList as $tree) { ?>
       <option value="<?php echo $tree['id']; ?>"><?php echo $tree['name']; ?></option>      
       <?php }} ?>
      </select>
      <select class="input_blur" name="province" id="province" onchange="javascript:get_province();">
       <option value="0">无(请选择二级分类)</option>       
      </select>
      <select class="input_blur" name="city" id="city">
       <option value="0">无(请选择三级分类)</option>       
      </select>      
      </td>
    </tr>
	<tr>
      <th width="20%">
      <font color="red">*</font> <strong>名称</strong> <br/>
	  </th>
      <td>
      <input name="name" id="name" value="<?php if(! empty($itemInfo)){ echo $itemInfo['name'];} ?>" size="100" valid="required" errmsg="名称不能为空!" class="inputtitle input_blur" type="text">
	<br/><font color="red">注：以英文的"|"分隔，如“a|b|c|d”</font>
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
<script type="text/javascript">
$select_country = <?php if(! empty($itemInfo)){ echo $itemInfo['parent_id'];} else {echo 0;} ?>;
$select_province = <?php if(! empty($itemInfo)){ echo $itemInfo['sub_parent_id'];} else {echo 0;} ?>;
$select_city = <?php if(! empty($itemInfo)){ echo $itemInfo['sub_sub_parent_id'];} else {echo 0;} ?>;
if ($select_country) {
	$("#country>option").each(function(i,n){
    	if($(n).val() == $select_country){
    		$("#country").get(0).options[i].selected = true;
    	}
    });    
}
//省	
if ($select_province) {
	globol_country($select_country, $select_province);
}
//市	
if ($select_city) {
	globol_province($select_province, $select_city);
}
//选择国家
function globol_country(country, select_id) {
	$.post(base_url+"admincp.php/"+controller+"/getCity", 
			{	"parent_id": country
			},
			function(res){
				if(res.success){
					var html = '<option value="0">无(请选择二级分类)</option>';
					for (var i = 0, data = res.data, len = data.length; i < len; i++){
						if (select_id) {
							if (select_id == +data[i]['id']) {
    							html += '<option selected="selected" value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
    						} else {
    							html += '<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
    						}
						} else {
							html += '<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
						}
					}
					$("#province").html(html);
					return false;
				}else{
					alert(res.message);
					return false;
				}
			},
			"json"
	);
}
function get_country() {
	var country = $("#country").val();
    if (country) {
    	globol_country(country, 0);
    }
}
//选择省
function globol_province(province, select_id) {
	$.post(base_url+"admincp.php/"+controller+"/getCity", 
			{	"parent_id": province
			},
			function(res){
				if(res.success){
					var html = '<option value="0">无(请选择三级分类)</option>';
					for (var i = 0, data = res.data, len = data.length; i < len; i++){
						if (select_id) {
							if (select_id == +data[i]['id']) {
    							html += '<option selected="selected" value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
    						} else {
    							html += '<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
    						}
						} else {
							html += '<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
						}
					}
					$("#city").html(html);
					return false;
				}else{
					alert(res.message);
					return false;
				}
			},
			"json"
	);
}
function get_province() {
	var province = $("#province").val();
    if (province) {
    	globol_province(province, 0);
    }
}
</script>