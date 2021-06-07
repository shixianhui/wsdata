<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>批量上传图片</title>
<base href="<?php echo base_url(); ?>" />
<script src="js/admin/jquery-1.4.2.min.js"></script>
<script src="js/admin/uploadify/js/jquery.uploadify.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="js/admin/uploadify/css/uploadify.css">
<style>
body { font-size: 14px; color: black; line-height: 150%; background-color:#fff;}
.textarea {
	border: 1px solid #CCCCCC;
    height: 74px;
    width: 430px;
}
</style>
<body>
<p><input id="batch_image_upload" name="batch_image_upload" type="file" multiple="true"></p>
<div id="batch_upload_file_queue" style="width: 572px;"></div>
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
		<button onclick="javascript:to_delete(this);" class="delete">删除</button>
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
	window.opener.document.getElementById("batch_path_ids").value = ids;
}

function to_down(obj) {
	$(obj).parents('tr').insertAfter($(obj).parents('tr').next());
	var ids = '';
	$("input[name='upload_id[]']").each(function(i,n){
		ids += $(this).val() + "_";
	});
	window.opener.document.getElementById("batch_path_ids").value = ids;
}

function to_delete(obj) {
	$(obj).parents('tr').remove();
	var ids = '';
	$("input[name='upload_id[]']").each(function(i,n){
		ids += $(this).val() + "_";
	});
	window.opener.document.getElementById("batch_path_ids").value = ids;
}

<?php $timestamp = time();?>
$(function() {
	$('#batch_image_upload').uploadify({
		'formData'     : {
			'timestamp' : '<?php echo $timestamp;?>',
			'token'     : '<?php echo md5('unique_salt' . $timestamp);?>',
			'model'     : '<?php echo $model; ?>'
		},
		'fileTypeExts' : '*.jpg; *.jpeg; *.png; *.gif',
	    'method'   : 'post',
		'multi'    : true,
		'fileSizeLimit' : '50MB',//B, KB, MB, or GB
		'uploadLimit' : 999,
		'removeCompleted' : true,
		'queueID'  : 'batch_upload_file_queue',
		'buttonText' : '批量传图',
		'swf'      : 'js/admin/uploadify/flash/uploadify.swf',
		'uploader' : '<?php echo base_url(); ?>admincp.php/upload/uploadImageByW',	
		'onUploadSuccess' : function(file, data, response) {
			json = eval("(" + data + ")");
			if (json.success) {
				window.opener.document.getElementById("batch_path_ids").value +=""+json.data.id+"_";
				var html = '<tr align="center" class="previewTemplate">'+
				'   <td>'+
				'      <input type="hidden" name="upload_id[]" value="'+json.data.id+'">'+
				'      <img width="105" height="74" src="'+json.data.file_path.replace('.', '_thumb.')+'" >'+
				'      <textarea style="width: 340px;" maxlength="400" name="image_alt[]" class="textarea" rows="2" cols="40"></textarea>'+
				'   </td>'+
				'<td>'+
				'<button onclick="javascript:to_top(this);" class="prev">上移</button><br/>'+
				'<button onclick="javascript:to_down(this);" class="next">下移</button><br/>'+
				'<button onclick="javascript:to_delete(this);" class="delete">删除</button>'+
				'</td>'+
				'</tr>';
			    $("#preview tr:last").after(html);
			}
			return false;
		}
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
