<form action="admincp.php/upload/cutPicture/<?php echo $model; ?>/<?php echo preg_replace(array('/\//', '/\./'), array(':', '_'), $filePath); ?>" method="post" id="jsonForm" name="jsonForm" enctype="multipart/form-data" >
<table cellpadding="2" cellspacing="1" class="table_form" align="center">
<caption>图片截取</caption>
  <tr>
    <td style=" text-align: center;"  width="50%">
    <?php 
    $percent = 1;
    $per = 1;
    $str = '';
    if ($width > 300 && $height > 300) {    	
    	if ($width >= $height) {
    		$percent = $width/300;
    		$per = 300/$width;
    		$str = 'width="300"';
    	} else {
    		$percent = $height/300;
    		$per = 300/$height;
    		$str = 'height="300"';
    	}
    } else if ($width > 300 && $height <= 300) {
    	$percent = $width/300;
    	$per = 300/$width;
    	$str = 'width="300"';
    } else if ($width <= 300 && $height > 300) {
    	$percent = $height/300;
    	$per = 300/$height;
    	$str = 'height="300"';
    }
    ?>
    <img src="<?php if (! empty($filePath)){echo $filePath;} ?>" <?php echo $str; ?> id="ladybug_ant">
    </td>
    <td style=" text-align: center;" width="50%"><img src="<?php if (! empty($filePath)){echo preg_replace('/\./', '_thumb.', $filePath);} ?>" id="preview" >
    <br/><font color="red">若上面的图片没有实时变化，请点<a href="javascript:window.location.reload();">刷新</a></font>
    </td>
  </tr>
</table>
<table cellpadding="2" cellspacing="1" class="table_form" align="center">
  <tr>
   <td>坐标</td>
  </tr>
  <tr>
   <td>
   x1：<input type="text" name="x1" id="x1"  size="10" /> x2：<input type="text" name="x2" id="x2"  size="10" />
   </td>
  </tr>
  <tr>
   <td>
   y1：<input type="text" name="y1" id="y1"  size="10" /> y2：<input type="text" name="y2" id="y2"  size="10" />
   </td>
  </tr>
  <tr>
    <td>
    <input type="hidden" name="cut_image_path" value="<?php if (! empty($filePath)){echo $filePath;} ?>" />
    剪切框：宽度=<input type="text" name="width" id="width" size="5" />px 高度=<input type="text" name="height" id="height" size="5"  />px
<input type="submit" name="dosubmit" value="剪切图片" />
</td>
  </tr>
</table>
</form>
<script type="text/javascript">
$(document).ready(function () {
var width = $('#preview').width();
var height = $('#preview').height();
$('#width').val(width);
$('#height').val(height);

$('#ladybug_ant').imgAreaSelect({aspectRatio: '<?php echo $w; ?>:<?php echo $h; ?>', handles: true, x1: 0, y1: 0, x2: width, y2: height, onSelectEnd: function (img, selection) {
	$("#x1").val(Math.round(selection.x1*<?php echo $percent; ?>));
    $("#y1").val(Math.round(selection.y1*<?php echo $percent; ?>));
    $("#x2").val(Math.round(selection.x2*<?php echo $percent; ?>));
    $("#y2").val(Math.round(selection.y2*<?php echo $percent; ?>));
    $("#width").val(Math.round(selection.width));
    $("#height").val(Math.round(selection.height));
	}});
});
</script>