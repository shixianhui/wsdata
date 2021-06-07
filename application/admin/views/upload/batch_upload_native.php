<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>批量上传图片</title>
<base href="<?php echo base_url(); ?>" />
<script>
var controller = '<?php echo $this->uri->segment(1); ?>';
var method = '<?php echo $this->uri->segment(2); ?>';
var param = '<?php echo $this->uri->segment(3); ?>';
var base_url = '<?php echo base_url(); ?>';
</script>
<script src="js/admin/aui-artDialog/lib/jquery-1.10.2.js"></script>
<link rel="stylesheet" href="js/admin/aui-artDialog/css/ui-dialog.css">
<script src="js/admin/aui-artDialog/dist/dialog-plus-min.js"></script>
<script src="js/admin/jquery.form.js"></script>
<script src="js/admin/formvalid.js?v=2.2" type="text/javascript"></script>
<script src="js/admin/index.js?v=2.32" type="text/javascript"></script>
<style>
body { font-size: 14px; color: black; line-height: 150%; background-color:#fff;}
.textarea {
	border: 1px solid #CCCCCC;
    height: 74px;
    width: 430px;
}
.but_4 {
    background: #f08200 none repeat scroll 0 0;
    border-radius: 6px;
    color: #fff;
    display: inline-block;
    font: 1.2em/32px "微软雅黑";
    height: 32px;
    margin-left: 10px;
    padding: 0 10px;
    position: relative;
}
.load {
    background: #fff none repeat scroll 0 0;
    left: 0;
    margin-top: -11px;
    opacity: 0.7;
    padding-left: 0px;
    position: absolute;
    top: 0;
    width: 63px;
}
</style>
<body>
<p>
<a style=" position:relative;" >
<span style="cursor:pointer;" class="but_4">批量上传图片<input style="left:0px;top:0px; background:#000; width:105px;height:35px;line-height:30px; position:absolute;filter:alpha(opacity=0);-moz-opacity:0;opacity:0;" type="file" accept=".gif,.jpg,.jpeg,.png" multiple="multiple" id="path_file" name="path_file[]" ></span>
 <i class="load" id="path_load" style="text-align:center;cursor:pointer;display:none;width:130px;padding-left:0px;"><img src="images/admin/loading_2.gif" width="32" height="32"></i>
</a>
</p>
<table width="100%" id="preview" style="background-color: #FFFFFF;border: 1px solid #CCCCCC;margin-top: 10px;width: 572px;">
   <tbody>
   <tr>
   <td>注：只支持*.jpg; *.jpeg; *.png; *.gif格式的图片</td>
   </tr>
   <?php if ($attachmentList) { ?>
	<?php foreach ($attachmentList as $value) { ?>
      <tr align="center" class="previewTemplate">
		<td>
		<input type="hidden" name="upload_id[]" value="<?php echo $value['id']; ?>">
		<img width="105" height="74" src="<?php echo preg_replace('/\./', '_thumb.', $value['path']); ?>" />
		<textarea style="width: 340px;" maxlength="400" name="image_alt[]" class="textarea" rows="2" cols="40"><?php echo $value['alt']; ?></textarea>
		</td>
		<td>
		<button onclick="javascript:to_top(this);" class="prev">上移</button><br/>
		<button onclick="javascript:to_down(this);" class="next">下移</button><br/>
		<button onclick="javascript:to_delete(this);" class="next">删除</button>
		</td>
	</tr>
  <?php }} ?>
  </tbody>
</table>
<table width="100%" style="width: 572px; margin-top: 10px;">
  <tbody>
   <tr align="center">
   <td><input name="update_alt" id="update_alt" type="button" value="修改注释" /></td>
   </tr>
  </tbody>
</table>
<script type="text/javascript">
function to_top(obj) {
	$(obj).parents('tr').insertBefore($(obj).parents('tr').prev('.previewTemplate'));
	var ids = '';
	$("input[name='upload_id[]']").each(function(i,n){
		ids += $(this).val() + "_";
	});
	<?php if ($id) { ?>
		window.parent.document.getElementById("batch_path_ids_<?php echo $id; ?>").value = ids;
	<?php } else { ?>
		window.parent.document.getElementById("batch_path_ids").value = ids;
	<?php } ?>
}

function to_down(obj) {
	$(obj).parents('tr').insertAfter($(obj).parents('tr').next());
	var ids = '';
	$("input[name='upload_id[]']").each(function(i,n){
		ids += $(this).val() + "_";
	});
	<?php if ($id) { ?>
		window.parent.document.getElementById("batch_path_ids_<?php echo $id; ?>").value = ids;
	<?php } else { ?>
		window.parent.document.getElementById("batch_path_ids").value = ids;
	<?php } ?>
}

function to_delete(obj) {
	$(obj).parents('tr').remove();
	var ids = '';
	$("input[name='upload_id[]']").each(function(i,n){
		ids += $(this).val() + "_";
	});
	<?php if ($id) { ?>
		window.parent.document.getElementById("batch_path_ids_<?php echo $id; ?>").value = ids;
	<?php } else { ?>
		window.parent.document.getElementById("batch_path_ids").value = ids;
	<?php } ?>
}

//参数mulu
$(function () {
	//形象照片
	$("input[name='path_file[]']").wrap("<form id='path_upload' action='<?php echo base_url(); ?>admincp.php/upload/uploadImage3' method='post' enctype='multipart/form-data'></form>");
    $("#path_file").change(function(){ //选择文件
	$("#path_upload").ajaxSubmit({
			dataType:  'json',
			data: {
                'model': '<?php echo $model; ?>',
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
        			var ids = '';
        			var html = '';
        			for(var i = 0; i < res.data.length; i++) {
        				ids += ""+res.data[i].id+"_";
        				html += '<tr align="center" class="previewTemplate">'+
        				'   <td>'+
        				'      <input type="hidden" name="upload_id[]" value="'+res.data[i].id+'">'+
        				'      <img width="105" height="74" src="'+res.data[i].file_path.replace('.', '_thumb.')+'" >'+
        				'      <textarea style="width: 340px;" maxlength="400" name="image_alt[]" class="textarea" rows="2" cols="40"></textarea>'+
        				'   </td>'+
        				'<td>'+
        				'<button onclick="javascript:to_top(this);" class="prev">上移</button><br/>'+
        				'<button onclick="javascript:to_down(this);" class="next">下移</button><br/>'+
        				'<button onclick="javascript:to_delete(this);" class="next">删除</button>'+
        				'</td>'+
        				'</tr>';

            		}
    				<?php if ($id) { ?>
    				window.parent.document.getElementById("batch_path_ids_<?php echo $id; ?>").value +=ids;
    				<?php } else { ?>
    				window.parent.document.getElementById("batch_path_ids").value +=ids;
                    <?php } ?>
                    $("#preview tr:last").after(html);
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

    $("#update_alt").click(function(){
		$attachmentIds = "";
		$alts = "";
		$("textarea[name='image_alt[]']").each(function(i,n){
    		$alts += $(this).val().replace(/,/g, "")+ ",";
    	});
    	$("input[name='upload_id[]']").each(function(i,n){
    		$attachmentIds += $(this).val() + ",";
    	});
		$.post("admincp.php/upload/updateAlt",
				{	"attachmentIds": $attachmentIds.substr(0, $attachmentIds.length - 1),
			        "alts": $alts.substr(0, $alts.length - 1),
				},
				function(res){
					if(res.success){
						alert(res.message);
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
</body>
</html>