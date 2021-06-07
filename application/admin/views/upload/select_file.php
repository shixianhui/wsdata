<table width='100%' border='0' cellspacing='0' cellpadding='0' >
<tr>
<td width="100px">当前目录：</td>
<td align="left">
<?php if (isset($path)){echo ".".$path;} ?>
</td>
<td align="center">
<?php if (isset($cusPath)) { ?>
<?php if ($cusPath != "_uploads_") { ?>
<a href="admincp.php/upload/selectFile/<?php echo substr($cusPath, 0, -5); ?>">返回上级目录</a>
<?php }} ?>
</td>
</tr>
</table>
<table cellpadding="0" cellspacing="1" class="table_list">
<caption>文件选择</caption>
<tr>
 <th class="align_c" >文件名</th>
 <th width="100">大小</th>
 <th class="align_c" width="100">尺寸</th>
 <th width="100">上次修改时间</th>
</tr>
<?php foreach ($files as $key=>$value ): ?>
<tr>
 <td align='left'>
 <?php if ($value["fileType"] == "dir"){?>
 <img src="images/admin/dir.gif" />
 <a href="admincp.php/upload/selectFile/<?php echo $cusPath . $value["fileName"] . "_" ; ?>"><?php echo $value["fileName"];?></a> 
 <?php } else if ($value["fileType"] == "file") {?>
 <img src="images/admin/jpg.gif" /> <a href="#" onclick="javascript:selectFile('<?php echo $path . $value["fileName"]; ?>');"><?php echo $value["fileName"];?></a><br/>
 <a href="#" onclick="javascript:selectFile('<?php echo $path . $value["fileName"]; ?>');">
 <img src="<?php echo $path . $value["fileName"]; ?>" width="80px" />
 </a>
 <?php }?>
 </td>
 <td width='55' class="align_c">
 <?php if ($value["fileType"] == "dir"){?>
 <目录>
 <?php } else if ($value["fileType"] == "file") {?>
 <?php echo $value["fileSize"];?>
 <?php }?> 
 </td>
 <td class="align_c">fff</td>
 <td width='140' class="align_c"><?php echo $value["fileMTime"];?></td>
</tr>
<?php endforeach; ?>
</table>
<table cellpadding='0' cellspacing='0' border='0' width='100%' height='10'>
<tr>
<td></td>
</tr>
</table>
<script language="javascript" type="text/javascript">
function selectFile(filePath) {
	if (filePath != null && filePath !="") {
		window.opener.document.getElementById("file_path").value=filePath;
		window.close();
	}	
}
</script>