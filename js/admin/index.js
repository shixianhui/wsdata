$(document).ready(function() {

	//设置选定项
	if (controller) {
		$("#"+controller+"_"+method).addClass("toolColor");
		// if (method == 'index') {
		// 	$("#"+controller+"_").addClass("toolColor");
		// }
	}
	//change color
	$("#title_color").change(function(){
		$color = $('#title_color').val();
		$('#title_color').css('background-color', $color);
		$("#title").css("color", $color);
	});
	//Change the display colors and layers
	$("#basic").click(function(){
		tab("basic");
	});
	$("#advanced").click(function(){
		tab("advanced");
	});
	$("#group").click(function(){
		tab("group");
	});

    $("#appAdvanced").click(function(){
        tab("appAdvanced");
    });
    $("#kefu").click(function(){
        tab("kefu");
    });
    $("#chouzhi").click(function(){
        tab("chouzhi");
    });
	function tab(tabid){
		$("#basic").removeClass("selected");
		$("#advanced").removeClass("selected");
		$("#group").removeClass("selected");
        $("#appAdvanced").removeClass("selected");
        $("#kefu").removeClass("selected");
        $("#chouzhi").removeClass("selected");
		$("#basics").hide();				
		$("#advanceds").hide();
		$("#groups").hide();
        $("#appAdvanceds").hide();
        $("#kefus").hide();
        $("#chouzhis").hide();
		if (tabid != null && tabid != "") {
			$("#"+tabid+"s").show();
			$("#"+tabid).addClass("selected");
		}
	}
	//Abstract word limit
	$("#abstract").keyup(function(){
		$value = $('#abstract').val()
		$length = 255 - $value.length;
		if ($length > 0) {
			$("#ls_description").html($length);
		} else {
			$("#ls_description").html("0");
			$('#abstract').val($value.substring(0,255));
			$('#abstract').focus();
		}
		
	});
	//Generate random numbers
    $("#hits").val(parseInt(200*Math.random()));
    //Select color
    $color = $('#title_color').val();
    $("#title_color>option").each(function(i,n){
		if($(n).val() == $color){
			 $("#title_color").get(0).options[i].selected = true;
			 $('#title_color').css('background-color', $color);
			 $("#title").css("color", $color);
			 return;
		}
	})
	
	/*****************************通用部分方法*********************************/
    //Select news category
    $categoryId = $("#select_category_id").val();
    $("#category>option").each(function(i,n){
    	if($(n).val() == $categoryId){
    		$("#category").get(0).options[i].selected = true;
    		return;
    	}
    })
	//sort
    $("#list_order").click(function(){
        $ids = "";
        $sorts = "";
    	$("input[name='ids']:checked").each(function(i,n){
    		$ids += $(this).val() + ",";
    		$sorts += $("#sort_" + $(this).val()).val() + ',';
    	});
    	if (! $ids) {
    		var d = dialog({
    			fixed: true,
    			width:300,
    		    title: '提示',
    		    content: "请选定值!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
    		return false;
    	}
		$.post(base_url+"admincp.php/"+controller+"/sort", 
				{	"ids": $ids.substr(0, $ids.length - 1), 
					"sorts": $sorts.substr(0, $sorts.length - 1)
				},
				function(res){
					if(res.success){
						var d = dialog({
			    			fixed: true,
			    			width:300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
                            window.location.reload();
			    		}, 1000);
						return false;
					}else{
						var d = dialog({
			    			fixed: true,
			    			width:300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
			    		}, 3000);
						return false;
					}
				},
				"json"
		);
	});
    //Delete items
    $("#delete").click(function(){
    	var con = confirm("你确定要删除数据吗？删除后将不可恢复！");
    	if (con == true) {
    		$ids = "";
        	$("input[name='ids']:checked").each(function(i,n){
        		$ids += $(this).val() + ",";
        	});
        	if (! $ids) {
        		var d = dialog({
	    			fixed: true,
	    			width:300,
	    		    title: '提示',
	    		    content: "请选定值!"
	    		});
	    		d.show();
	    		setTimeout(function () {
	    		    d.close().remove();
	    		}, 3000);
				return false;
        	}
    		$.post(base_url+"admincp.php/"+controller+"/delete", 
    				{	"ids": $ids.substr(0, $ids.length - 1)
    				},
    				function(res){
    					if(res.success){
    						for (var i = 0, data = res.data.ids, len = data.length; i < len; i++){
    							$("#id_"+data[i]).remove();
    						}
    						return false;
    					}else{
    						alert(res.message);
    						return false;
    					}
    				},
    				"json"
    		);
    	}        
	});
    //Change category
    $("#category_id").change(function(){
        $ids = "";
        $categoryId = $("#category_id").val();
    	$("input[name='ids']:checked").each(function(i,n){
    		$ids += $(this).val() + ",";
    	});
    	if (! $ids) {
    		var d = dialog({
    			fixed: true,
    			width:300,
    		    title: '提示',
    		    content: "请选定值!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
    	if (! $categoryId) {
    		var d = dialog({
    			fixed: true,
    			width:300,
    		    title: '提示',
    		    content: "选择栏目!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
		$.post(base_url+"admincp.php/"+controller+"/category", 
				{	"ids": $ids.substr(0, $ids.length - 1),
			        "categoryId": $categoryId
				},
				function(res){
					if(res.success){
						//alert(res.message);
						window.location.reload();
						return false;
					}else{
						var d = dialog({
			    			fixed: true,
			    			width:300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
			    		}, 3000);
						return false;
					}
				},
				"json"
		);
	});
    //Change display
    $("#select_display").change(function(){
        $ids = "";
        $display = $("#select_display").val();
    	$("input[name='ids']:checked").each(function(i,n){
    		$ids += $(this).val() + ",";
    	});
    	if (! $ids) {
    		var d = dialog({
    			fixed: true,
    			width:300,
    		    title: '提示',
    		    content: "请选定值!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
    	if ($display == "") {
    		var d = dialog({
    			fixed: true,
    			width:300,
    		    title: '提示',
    		    content: "请选择状态!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
		$.post(base_url+"admincp.php/"+controller+"/display", 
				{	"ids": $ids.substr(0, $ids.length - 1),
			        "display": $display
				},
				function(res){
					if(res.success){
						//alert(res.message);
						window.location.reload();
						return false;
					}else{
						var d = dialog({
			    			fixed: true,
			    			width: 300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
			    		}, 3000);
						return false;
					}
				},
				"json"
		);
	});
    //Change is_checked
    $("#select_is_checked").change(function(){
        $ids = "";
        $display = $("#select_is_checked").val();
        $("input[name='ids']:checked").each(function(i,n){
            $ids += $(this).val() + ",";
        });
        if (! $ids) {
            var d = dialog({
                fixed: true,
                width:300,
                title: '提示',
                content: "请选定值!"
            });
            d.show();
            setTimeout(function () {
                d.close().remove();
            }, 3000);
            return false;
        }
        if ($display == "") {
            var d = dialog({
                fixed: true,
                width:300,
                title: '提示',
                content: "请选择状态!"
            });
            d.show();
            setTimeout(function () {
                d.close().remove();
            }, 3000);
            return false;
        }
        $.post(base_url+"admincp.php/"+controller+"/change_is_checked",
            {	"ids": $ids.substr(0, $ids.length - 1),
                "display": $display
            },
            function(res){
                if(res.success){
                    //alert(res.message);
                    window.location.reload();
                    return false;
                }else{
                    var d = dialog({
                        fixed: true,
                        width: 300,
                        title: '提示',
                        content: res.message
                    });
                    d.show();
                    setTimeout(function () {
                        d.close().remove();
                    }, 3000);
                    return false;
                }
            },
            "json"
        );
    });
    //Change attribute
    $("#custom_attribute").change(function(){
        $ids = "";
        $customAttribute = $("#custom_attribute").val();
    	$("input[name='ids']:checked").each(function(i,n){
    		$ids += $(this).val() + ",";
    	});
    	if (! $ids) {
    		var d = dialog({
    			fixed: true,
    			width: 300,
    		    title: '提示',
    		    content: "请选定值!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
    	if (! $customAttribute) {
    		var d = dialog({
    			fixed: true,
    			width: 300,
    		    title: '提示',
    		    content: "选择属性!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
		$.post(base_url+"admincp.php/"+controller+"/attribute", 
				{	"ids": $ids.substr(0, $ids.length - 1),
			        "custom_attribute": $customAttribute
				},
				function(res){
					if(res.success){
						//alert(res.message);
						window.location.reload();
						return false;
					}else{
						var d = dialog({
			    			fixed: true,
			    			width: 300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
			    		}, 3000);
						return false;
					}
				},
				"json"
		);
	});
    //Update html
    $("#html_update").click(function(){
        $ids = "";
    	$("input[name='ids']:checked").each(function(i,n){
    		$ids += $(this).val() + ",";
    	});
    	if (! $ids) {
    		var d = dialog({
    			fixed: true,
    			width: 300,
    		    title: '提示',
    		    content: "请选定值!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
		$.post(base_url+"admincp.php/"+controller+"/htmlUpdate", 
				{	"ids": $ids.substr(0, $ids.length - 1)
				},
				function(res){
					if(res.success){
						//alert(res.message);
						window.location.reload();
						return false;
					}else{
						var d = dialog({
			    			fixed: true,
			    			width: 300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
			    		}, 3000);
						return false;
					}
				},
				"json"
		);
	});
    //Delete html
    $("#html_delete").click(function(){
        $ids = "";
    	$("input[name='ids']:checked").each(function(i,n){
    		$ids += $(this).val() + ",";
    	});
    	if (! $ids) {
    		var d = dialog({
    			fixed: true,
    			width: 300,
    		    title: '提示',
    		    content: "请选定值!"
    		});
    		d.show();
    		setTimeout(function () {
    		    d.close().remove();
    		}, 3000);
			return false;
    	}
		$.post(base_url+"admincp.php/"+controller+"/htmlDelete", 
				{	"ids": $ids.substr(0, $ids.length - 1)
				},
				function(res){
					if(res.success){
						//alert(res.message);
						window.location.reload();
						return false;
					}else{
						var d = dialog({
			    			fixed: true,
			    			width: 300,
			    		    title: '提示',
			    		    content: res.message
			    		});
			    		d.show();
			    		setTimeout(function () {
			    		    d.close().remove();
			    		}, 3000);
						return false;
					}
				},
				"json"
		);
	});
    /***********************************file upload*******************************************/
    //upload image, open new window
    $("#upload_image").click(function(){
    	var imagePath = $("#path").val();
    	var model = $("#model").val();
    	var parseImagePath = imagePath.replace(/\//g, ":");
	    parseImagePath = parseImagePath.replace(/\./g, "_");
    	var d = dialog({
			fixed: true,
		    title: '上传图片：',
		    content: '<iframe frameborder="0" src="'+base_url+'admincp.php/upload/index/'+model+'/'+parseImagePath+'" width="680" height="400"></iframe>'
		});
		d.show();
    });
    //upload file, open new window
    $("#upload_file").click(function(){
    	var filePath = $("#file_path").val();
    	var parseFilePath = filePath.replace(/\//g, ":");
	    parseFilePath = parseFilePath.replace(/\./g, "_");
    	window.open(base_url+"admincp.php/upload/uploadFile/"+parseFilePath, "uploadFile", "top=100, left=200, width=500, height=400, scrollbars=1, resizable=yes");
    });   
    //upload movie, open new window
    $("#upload_movie").click(function(){
    	var filePath = $("#path").val();
    	var parseFilePath = filePath.replace(/\//g, ":");
    	parseFilePath = parseFilePath.replace(/\./g, "_");
    	window.open(base_url+"admincp.php/upload/uploadMovie/"+parseFilePath, "uploadFile", "top=100, left=200, width=500, height=400, scrollbars=1, resizable=yes");
    });
    //batch upload image, open new window
    $("#batch_upload_image").click(function(){
    	var batchPathIds = $("#batch_path_ids").val();
    	var model = $("#model").val();
    	var parseImagePathIds = batchPathIds.replace(/\//g, ":");
    	parseImagePathIds = parseImagePathIds.replace(/\./g, "_");
    	
    	var d = dialog({
			fixed: true,
		    title: '批量上传图片：',
		    content: '<iframe frameborder="0" src="'+base_url+'admincp.php/upload/batch_upload/'+model+'/'+parseImagePathIds+'" width="680" height="400"></iframe>'
		});
		d.show();
    });
    //select image, open new window
    $("#select_image").click(function(){
    	window.open(base_url+"admincp.php/upload/select", "select", "top=100, left=200, width=700, height=450, scrollbars=1, resizable=yes");
    });   
    //select movie, open new window
    $("#select_movie").click(function(){
    	window.open(base_url+"admincp.php/upload/selectMovie", "selectMovie", "top=100, left=200, width=500, height=450, scrollbars=1, resizable=yes");
    });
    //cut image, open new window
    $("#cut_image").click(function(){
    	var imagePath = $("#path").val();
    	var model = $("#model").val();
    	var cutImagePath = imagePath.replace(/\//g, ":");
    	cutImagePath = cutImagePath.replace(/\./g, "_");
    	window.open(base_url+"admincp.php/upload/cutpicture/"+model+"/"+cutImagePath, "cutpic", "top=100, left=200, width=700, height=500, scrollbars=1, resizable=yes");
    });
    
    /*********************************menu**********************************/
    //Select menu
    $parent = $("#parentSelectMenu").val();
    $("#parent>option").each(function(i,n){
    	if($(n).val() == $parent){
    		$("#parent").get(0).options[i].selected = true;
    		return;
    	}
    })
    //Select model
    $model = $("#selectModel").val();
    $("#model>option").each(function(i,n){
    	if($(n).val() == $model){
    		$("#model").get(0).options[i].selected = true;
    		return;
    	}
    })
     $("#model").change(function(){
    	 $md = $("#model").val();
    	 $("#template").val($md);
    	 return false;
    });
    
    //select type of the menu
    $("#menu_type").change(function(){
    	var menuType = $("input[name='menu_type']:checked").val();
    	if (menuType == 3) {
    		$("#link_url").show();
    	    $("#selectModelTr").hide();
    	    $("#select_template").hide();
    	} else {    		
    		$("#link_url").hide();
    		$("#selectModelTr").show();
    		$("#model").get(0).options[0].selected = true;
    		$("#select_template").show();
    		$("#template").val('');
    		if (menuType == 2) {
    			$("#model>option").each(function(i,n){
    		    	if($(n).val() == 'page'){
    		    		$("#model").get(0).options[i].selected = true;
    		    		$("#template").val('page');
    		    		return ;
    		    	}
    		    })
    		}
    	}    	
    });
    //on load, select type of the menu
    $menuType = $("input[name='menu_type']:checked").val();
    if ($menuType !=null && $menuType !="" && $menuType == 3) {
		$("#link_url").show();
	    $("#selectModelTr").hide();
	    $("#select_template").hide();
	} 
    //delete menu
    //menu sort
    $("#menu_list_order").click(function(){
        $menuIds = "";
        $menuSorts = "";
    	$("input[name='menu_id']:checked").each(function(i,n){
    		$menuIds += $(this).val() + ",";
    		$menuSorts += $("#sort_" + $(this).val()).val() + ',';
    	});
    	
		$.post(base_url+"admincp.php/menu/sort", 
			{	"menuIds": $menuIds.substr(0, $menuIds.length - 1), 
					"menuSorts": $menuSorts.substr(0, $menuSorts.length - 1)
			},
			function(res){
				if(res.success){
					alert(res.message);
					return false;
				} else {
					alert(res.message);
					return false;
				}
			},
			"json"
		);
	});
    
    /************************************ad***************************************/
    //on load, select type of the link
    $adType = $("input[name='ad_type']:checked").val();
    if ($adType !=null && $adType !="" && ($adType == "image" || $adType == "flash")) {
    	$("#tr_image_path").show();
		$("#tr_content").hide();
	} else if ($adType !=null && $adType !="" && ($adType == "html" || $adType == "text")) {
		$("#tr_image_path").hide();
		$("#tr_content").show();
	}
    //select type of the link
    $("input[name='ad_type']").click(function(){
    	var adType = $("input[name='ad_type']:checked").val();
    	if (adType == "image" || adType == "flash") {
    		$("#tr_image_path").show();
    		$("#tr_content").hide();
    	} else {
    		$("#tr_image_path").hide();
    		$("#tr_content").show();
    	}
    });
    
    /************************************delete file***************************************/
    $(".deleteFile").click(function(){
    	var path = $(this).parents('tr').attr('id');
		$.post(base_url+"admincp.php/file/deleteFile", 
			{	"path": path
			},
			function(res){
				if(res.success){
					window.location.reload();
					return false;
				} else {
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				}
			},
			"json"
		);
	});
    
    /************************************backup database***************************************/
    //批量优化表
    $("#optimize_tables").click(function(){
    	$tables = "";
     	$("input[name='ids[]']:checked").each(function(i,n){
     		$tables += $(this).val() + ",";
     	});
		$.post(base_url+"admincp.php/backup/optimize", 
			{	"tables": $tables.substr(0, $tables.length - 1)
			},
			function(res){
				if(res.success){
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				} else {
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				}
			},
			"json"
		);
	});
    //批量修复表
    $("#repair_tables").click(function(){
    	$tables = "";
     	$("input[name='ids[]']:checked").each(function(i,n){
     		$tables += $(this).val() + ",";
     	});
		$.post(base_url+"admincp.php/backup/repair", 
			{	"tables": $tables.substr(0, $tables.length - 1)
			},
			function(res){
				if(res.success){
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				} else {
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				}
			},
			"json"
		);
	});
    
    /***************************************标题关键词***************************************/
    //获取标题关键词
    $("#title").change(function(){
    	$title = $("#title").val();
		$.post(base_url+"admincp.php/"+controller+"/getKeycode", 
			{	"title": $title
			},
			function(res){
				if(res.success){
					$("#keyword").val(res.data.keycode);
					return false;
				} else {
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				}
			},
			"json"
		);
	});
    /***************************************栏目标题(中文转换成拼音)***************************************/
    $("#parent").change(function(){
    	$("#menu_name").change();
    });
    $("#menu_name").change(function(){
    	$menu_name = $("#menu_name").val();
    	$parent_id = $("#parent").val();
		$.post(base_url+"admincp.php/"+controller+"/getPinyin", 
			{	"menu_name": $menu_name,
				"parent_id": $parent_id
			},
			function(res){
				if(res.success){
					$("#html_path").val(res.data.pinyin);
					return false;
				} else {
					var d = dialog({
		    			fixed: true,
		    			width: 300,
		    		    title: '提示',
		    		    content: res.message
		    		});
		    		d.show();
		    		setTimeout(function () {
		    		    d.close().remove();
		    		}, 3000);
					return false;
				}
			},
			"json"
		);
	});
    
    /****添加/编辑****/	
	$('#jsonForm').ajaxForm({
        dataType:  'json',
        beforeSubmit: showRequest,
        success:   showReponse 
    });
	function showReponse(data) {
		if(data && data.success){
			if (data.message) {
                var d = dialog({
                    fixed: true,
                    width:300,
                    title: '提示',
                    content: data.message
                });
                d.show();
                setTimeout(function () {
                    d.close().remove();
                    window.location.href = data.url;
                }, 1500);
			} else {
				window.location.href = data.url;
			}			
			return false;
		}else{
			alert(data.message);
			return false;
		}
	}
	function showRequest() {
		if(validator(document.forms['jsonForm'])) {
			return true;
		} else {
			return false;
		}
	}




});
function my_alert(field, is_focus, msg) {
    if (is_focus == 1) {
        $('#'+field).focus();
    }
    var d = dialog({
        fixed: true,
        width:300,
        title: '提示',
        content: msg
    });
    d.show();
    setTimeout(function () {
        d.close().remove();
    }, 3000);
    return false;
}

function my_alert_flush(field, is_focus, msg) {
    if (is_focus == 1) {
        $('#'+field).focus();
    }
    var d = dialog({
        fixed: true,
        width:300,
        title: '提示',
        content: msg
    });
    d.show();
    setTimeout(function () {
        window.location.reload();
        d.close().remove();
    }, 2000);
    return false;
}

function copy_pattern(id) {
	var p_d = dialog({
		fixed: true,
		width:300,
	    title: '复制模型',
	    content: '<table width="280" border="0">'+
	    	     '<tr><td height="25px">模型名称：</td><td><input class="input_blur" type="text" name="title" id="title" /></td></tr>'+
	             '<tr><td height="25px">别名：</td><td><input class="input_blur" type="text" name="alias" id="alias" /></td></tr>'+
	             '<tr><td height="25px">模型文件：</td><td><input class="input_blur" type="text" name="file_name" id="file_name" /></td></tr></table>',
	    okValue: '确定复制',
	    ok: function () {
	        var title = $("#title").val();
	        var alias = $("#alias").val();
	        var file_name = $("#file_name").val();
		    if (!title) {
		    	var d = dialog({
				    title: '提示',
				    content: "请填写模型名称"
				});
				d.show();
				setTimeout(function () {
				    d.close().remove();
				}, 2000);
				return false;
		    }
		    if (!alias) {
		    	var d = dialog({
				    title: '提示',
				    content: "请填写别名"
				});
				d.show();
				setTimeout(function () {
				    d.close().remove();
				}, 2000);
				return false;
		    }
		    if (!file_name) {
		    	var d = dialog({
				    title: '提示',
				    content: "请填写模型文件名"
				});
				d.show();
				setTimeout(function () {
				    d.close().remove();
				}, 2000);
				return false;
		    }
		    $.post(base_url+"admincp.php/pattern/copy", 
					{	"id":        id,
		    	        "title":     title,
		    	        "alias":     alias,
		    	        "file_name": file_name
					},
					function(res){
						if(res.success) {
							var d = dialog({
								fixed: true,
								width:300,
							    title: '提示',
							    content: res.message,
							    okValue: '确定',
							    ok: function () {
								    window.location.reload();
							    }
							});
							d.show();
							p_d.close().remove();
							return false;
						} else {
							var d = dialog({
							    title: '提示',
							    content: res.message
							});
							d.show();
							setTimeout(function () {
							    d.close().remove();
							}, 2000);
							return false;
						}
					},
					"json"
			);
		    return false;
	    }
	});
	p_d.show();
}

function delete_pattern(id, title) {
	var p_d = dialog({
		fixed: true,
		width:280,
	    title: '删除模型',
	    content: '删除模型会删除模型相关的文件与表，您确定要删除【<font color="red">'+title+'</font>】模型吗，删除后将不可恢复？',
	    okValue: '确定删除',
	    ok: function () {	        
		    $.post(base_url+"admincp.php/pattern/delete_pattern", 
					{	"id": id
					},
					function(res){
						if(res.success) {
							$("#id_"+id).remove();
							p_d.close().remove();
							return false;
						} else {
							var d = dialog({
							    title: '提示',
							    content: res.message
							});
							d.show();
							setTimeout(function () {
							    d.close().remove();
							}, 2000);
							return false;
						}
					},
					"json"
			);
		    return false;
	    }
	});
	p_d.show();
}


