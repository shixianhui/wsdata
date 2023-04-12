<?php
use App\Tools\Oss;
class Upload extends CI_Controller {
	private $_title = '文件上传';
	private $_tool = '';
	private $_thumbPath = '';
	private $_imageSize = [];
	//oss对象
	// protected $oss;  
	public function __construct() {
		parent::__construct();
		// $this->load->library('movietransformclass');
		$this->load->model('Attachment_model', '', TRUE);
		$this->load->model('Watermark_model', '', TRUE);
		$this->load->helper(array('url', 'my_fileoperate'));

		$this->_imageSize = get_image_size();
        $this->load->library('oss');

	}

	public function index($model = 'news', $filePath = NULL) {
		$data = array(
		               'ext'=>'gif|jpg|jpeg|png',
		               'filePath'=>$filePath?preg_replace(array('/:/', '/_/'), array('/', '_thumb.'), $filePath):NULL,
                       'size'=>'2 M',
		               'width'=>$this->_imageSize[$model]['width'],
		               'height'=>$this->_imageSize[$model]['height'],
					   'model'=>$model
		               );

		$layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/index', $data, TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

	public function get_image_size($model = NULL) {
    	return $this->_imageSize[$model];
    }

	public function select($filePath = ':uploads:') {
		$baseArray = array();
		$path = preg_replace('/:/', '/', $filePath);
		$baseDirectory = '.' . $path;
		$handler = opendir( $baseDirectory );
		$i = 0;
		while(($fileName = readdir( $handler )) !== false){
			if($fileName != '.' && $fileName != '..' && ! strpos($fileName, '_thumb') && ! strpos($fileName, '_max')) {
			    $baseArray[$i]['fileName'] = $fileName;
			    $baseArray[$i]['fileSize'] = getFileSize(filesize($baseDirectory . $fileName));
				if (file_exists('.'.$path . $fileName) && is_file('.'.$path . $fileName)) {
			    	//原图大小
			    	$artworkSize = @getimagesize('.'.$path . $fileName);
			    	if ($artworkSize) {
			    		$baseArray[$i]['artworkSize'] = "{$artworkSize[0]}x{$artworkSize[1]}";
			    	} else {
			    		$baseArray[$i]['artworkSize'] = '---';
			    	}
			    	
			    	//缩略图大小
			        $thumbnailSize = @getimagesize('.'.$path . preg_replace('/\./', '_thumb.', $fileName));
			    	if ($thumbnailSize) {
			    		$baseArray[$i]['thumbnailSize'] = "{$thumbnailSize[0]}x{$thumbnailSize[1]}";
			    	} else {
			    		$baseArray[$i]['thumbnailSize'] = '---';
			    	}
			    } else {
			        $baseArray[$i]['artworkSize'] = '---';
			        $baseArray[$i]['thumbnailSize'] = '---';
			    }
			    $baseArray[$i]['fileMTime'] = date('Y-m-d H:i', filemtime($baseDirectory . $fileName));
			    $baseArray[$i]['fileType'] = filetype($baseDirectory . $fileName);
			    $i++;
			}
		}
		closedir($handler);
	    $preFilePath = $filePath;
		$files = explode(":", preg_replace(array('/^:/', '/:$/'), array('', ''), $filePath));
		if (count($files) > 1) {
			$preFilePath = ':';
			foreach ($files as $key=>$value) {
				if ($key+1 != count($files)) {
				    $preFilePath .= $value.':';	
				}	     
			}
		}
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/select', array('files'=>$baseArray, 'path'=>$path, 'cusPath'=>$filePath, 'prePath'=>$preFilePath), TRUE)
			      );

	    $this->load->view('layout/default', $layout);
	}

    public function selectFile($domId, $filePath = ':uploads:') {
		$baseArray = array();
		$path = preg_replace('/:/', '/', $filePath);
		$baseDirectory = '.' . $path;
		$handler = opendir( $baseDirectory );
		$i = 0;
		while(($fileName = readdir( $handler )) !== false){
			if($fileName != '.' && $fileName != '..' && ! strpos($fileName, '_thumb') && ! strpos($fileName, '_max')) {
			    $baseArray[$i]['fileName'] = $fileName;
			    $baseArray[$i]['fileSize'] = getFileSize(filesize($baseDirectory . $fileName));
			    $baseArray[$i]['fileMTime'] = date('Y-m-d H:i:s', filemtime($baseDirectory . $fileName));
			    $baseArray[$i]['fileType'] = filetype($baseDirectory . $fileName);
			    $i++;
			}
		}
		closedir($handler);
        $preFilePath = $filePath;
		$files = explode(":", preg_replace(array('/^:/', '/:$/'), array('', ''), $filePath));
		if (count($files) > 1) {
			$preFilePath = ':';
			foreach ($files as $key=>$value) {
				if ($key+1 != count($files)) {
				    $preFilePath .= $value.':';	
				}	     
			}
		}
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/select_file', array('files'=>$baseArray, 'path'=>$path, 'cusPath'=>$filePath, 'prePath'=>$preFilePath, 'domId'=>$domId), TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}



    public function selectMovie($filePath = '_uploads_') {
		$baseArray = array();
		$path = preg_replace('/_/', '/', $filePath);
		$baseDirectory = '.' . $path;
		$handler = opendir( $baseDirectory );
		$i = 0;
		while(($fileName = readdir( $handler )) !== false){
			if($fileName != '.' && $fileName != '..' && ! strpos($fileName, '_thumb')) {
			    $baseArray[$i]['fileName'] = $fileName;
			    $baseArray[$i]['fileSize'] = getFileSize(filesize($baseDirectory . $fileName));
			    $baseArray[$i]['fileMTime'] = date('Y-m-d H:i:s', filemtime($baseDirectory . $fileName));
			    $baseArray[$i]['fileType'] = filetype($baseDirectory . $fileName);
			    $i++;
			}
		}
		closedir($handler);
	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/select_movie', array('files'=>$baseArray, 'path'=>$path, 'cusPath'=>$filePath), TRUE)
			      );
	    $this->load->view('layout/default', $layout);
	}

    public function cutPicture($model = 'news', $filePath = NULL) {
		if($_POST) {
			$x1 = $this->input->post('x1');
			$y1 = $this->input->post('y1');
			$x2 = $this->input->post('x2');
			$y2 = $this->input->post('y2');
			$width = $this->input->post('width');
			$height = $this->input->post('height');
			$imagePath = $this->input->post('cut_image_path');

		    if ($this->_crop('./' . $imagePath, $x2 - $x1, $y2 - $y1, $x1, $y1, '_thumb')) {
				if ($this->_resize('./'.preg_replace('/\./', '_thumb.', $imagePath), $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'], '')) {
//					if ($model == 'product') {
//						if ($this->_crop('./' . $imagePath, $x2 - $x1, $y2 - $y1, $x1, $y1, '_max')) {
//							$this->_resize('./'.preg_replace('/\./', '_max.', $imagePath),  $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '');
//						}
//					}
					printAjaxSuccess(base_url().'admincp.php/upload/cutpicture/'.$model.'/'.preg_replace(array('/\//', '/\./'), array(':', '_'), $imagePath), '裁剪成功！');
				}
			}
		}
		$path = preg_replace(array('/:/', '/_/'), array('/', '.'), $filePath);
		$size = array();
		if (file_exists('./'.$path)) {
			$size = getimagesize('./'.$path);
		}
		
		$data = array(
		        'filePath'=>$path,
				'width'=>$size?$size[0]:'',
				'height'=>$size?$size[1]:'',
		        'w'=>$this->_imageSize[$model]['width'],
		        'h'=>$this->_imageSize[$model]['height'],
		        'model'=>$model
		        );

	    $layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/cut_picture', $data, TRUE)
			      );

	    $this->load->view('layout/default', $layout);
	}

	public function uploadImage() {
		$ret = $this->_upload('filePath');
		$model = $this->input->post('model');
		//打水印
		$watermarkInfo = $this->Watermark_model->get('*', array('id'=>1, 'is_open'=>1));
		if ($watermarkInfo && $watermarkInfo['path']) {
			$location = explode(',', $watermarkInfo['location']);
			$this->_watermark("./".$this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], "./".$watermarkInfo['path'], $location[0], $location[1]);
        }

		if ($this->_resize("./".$this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
//			if ($model == 'product') {
//			    $this->_resize("./".$this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '_max');
//			}
			
			$attachment = array(
			              'path'=>$this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'],
			              'width'=>$ret['image_width'],
			              'height'=>$ret['image_height'],
			              'size'=>$ret['file_size'],
			              'alt'=>$ret['file_name']
			              );
			$this->Attachment_model->save($attachment);

		    $data = array(
		            'msg'=>'文件上传成功!',
		            'url'=>base_url()."admincp.php/upload/index/{$model}/".preg_replace('/\//', ':', $this->_thumbPath) . ':' . $ret['raw_name'] . preg_replace('/\./', '_', $ret['file_ext'])
		            );
			$this->session->set_userdata($data);
			redirect('/message/index');
		} else {
		    echo $this->upload->display_errors();
		}
	}

	public function uploadImage2() {
		set_time_limit(0);
		$model = $this->input->post('model', TRUE);
		$field = $this->input->post('field', TRUE);
		$ret = $this->_upload($field);
		if (!$ret) {
			printAjaxError('fail', '请选择上传的文件');
		}
		if ($this->_resize("./" . $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
			if ($model == 'product') {
				$this->_resize("./" . $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '_max');
			}
			//打水印
			$watermarkInfo = $this->Watermark_model->get('*', array('id' => 1, 'is_open' => 1));
			if ($watermarkInfo && $watermarkInfo['path']) {
				$location = explode(',', $watermarkInfo['location']);
				$this->_watermark("./" . $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], "./" . $watermarkInfo['path'], $location[0], $location[1]);
			}
			$attachment = array(
				'path' => $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'],
				'size' => $ret['file_size'],
				'name' => $ret['file_name']
			);
			$ret_id = $this->Attachment_model->save($attachment);
			printAjaxData(array('id' => $ret_id, 'field' => "r=" . rand(10000, 99999) . "", 'file_path' => $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext']));
		} else {
			echo $this->upload->display_errors();
		}
	}

	public function uploadImageOss()
	{
		$field = $this->input->post('field', TRUE);
		$fileName = $_FILES['file']['name'];
		$url = $_FILES['file']['tmp_name'];
		$image = file_get_contents($url);
		$size = getimagesize($url);
		if($size) 
		$extension = image_type_to_extension($size[2]);
		$fileHash = md5($image);
		//待保存的图片名称
		$name = $fileHash.$extension;
		// // 将图像字符串数据编码为base64
		// $file_content = base64_encode($img);
		// $img_base64 = 'data:' . $_FILES['file']['type'] . ';base64,' . $file_content;//合成图片的base64编码
		$data = $this->oss->putFile($name,(string)$image,date('Ymd'));
		//定义返回结果
		$rs = [];
		if (!empty($data)) {
			$rs = [
				'name'      => $fileName?:$name,
				'mime_type' => $_FILES['file']['type'],
				'size'      => $data['fileSize'],
				'type'      => 'oss',
				'path'      => $data['url'],
				'thumb'     => oss_thumb($data['url']),
				'hash'      => $fileHash,
				'source'    => 0
			];
			$ret_id = $this->Attachment_model->save($rs);

			printAjaxData(['id' => $ret_id, 'file_path'=>$data['url'], 'file_path_thumb'=>oss_thumb($data['url'])]);
		}

	}

	/**
     * 图片上传到oss服务器
     *
     * @param Image  $image    图片对象
     * @param string $fileName 图片名称
     *
     * @return array
     * @throws Exception
     */
    private function putToOss($image,$fileName) {
        try{
            //图片后缀
            $ext = (new MimeTypeExtensionGuesser)->guess($image->mime);
            //待保存的图片名称
            $name = $this->fileHash.'.'.$ext;
            $data = $this->oss->putFile($name,(string)$image,date('Ymd'));
            //定义返回结果
            $rs = [];
            if (!empty($data)) {
                $rs = [
                    'name'      => $fileName?:$name,
                    'mime_type' => $image->mime,
                    'size'      => $data['fileSize'],
                    'type'      => $this->config['type'],
                    'path'      => $data['url'],
                    'thumb'     => oss_thumb($data['url']),
                    'hash'      => $this->fileHash,
                    'source'    => $this->source
                ];
            }
            return $rs;

        }catch (Exception $e) {
            throw new Exception(trans('tools.upload_error'));
        }
    }

    public function uploadImage3() {
		if ($_POST) {
			$baseDir = './uploads';
			$model = $_POST['model'];
			$ret = array();
			if ($_FILES['file']['tmp_name']) {
                foreach ($_FILES['file']['tmp_name'] as $key=>$value) {
                	if (is_uploaded_file($value)) {
                		$uploadPath = createDateTimeDir($baseDir);
                		$uniqueFileName = getUniqueFileName($uploadPath);
                		$fileExt = 'png';
                		$uploadFile['name'] = $uniqueFileName.'.'.$fileExt;
                		$uploadFile['filename'] = $uploadPath.'/'.$uploadFile['name'];
                		if(@move_uploaded_file($value, $uploadFile['filename'])) {
                			if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
                				//打水印
                				$watermarkInfo = $this->Watermark_model->get('*', array('id'=>1, 'is_open'=>1));
                				if ($watermarkInfo && $watermarkInfo['path']) {
                					$location = explode(',', $watermarkInfo['location']);
                					$this->_watermark($uploadFile['filename'], "./".$watermarkInfo['path'], $location[0], $location[1]);
                				}
                				$fields = array(
                						'path'=>substr($uploadFile['filename'], 2),
                						'width'=>0,
                						'height'=>0,
                						'size'=>0,
                						'alt'=>''
                				);
                				$attachmentId = $this->Attachment_model->save($fields);
                				if ($attachmentId) {
                					$ret[] = array('id'=>$attachmentId, 'field'=>"r=".rand(10000, 99999)."",  'file_path'=>substr($uploadFile['filename'], 2));
                				}
                			}
                		}
                	}
                }
			}
			printAjaxData($ret);
		}
	}

	public function uploadFile($domId, $filePath = NULL) {
	    if ($_POST) {
	        $ret = $this->_upload('filePath', './uploads', 'apk|exe|ios|rar|zip|nb0|sbf|ipa|xap', $maxSize = '1024000');
            if (! $ret) {
            	echo $this->upload->display_errors();
            	exit;
            }
	        $fields = array(
			              'path'=>$this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'],
			              'width'=>0,
			              'height'=>0,
			              'size'=>$ret['file_size'],
			              'alt'=>$ret['file_name']
			              );
			$this->Attachment_model->save($fields);

		    $data = array(
		            'msg'=>'文件上传成功!',
		            'url'=>base_url().'admincp.php/upload/uploadFile/'.$domId.'/'.preg_replace('/\//', ':', $this->_thumbPath) . ':' . $ret['raw_name'] . $ret['file_ext']
		            );
			$this->session->set_userdata($data);
			redirect('/message/index');
	    }

	    $data = array(
		               'ext'=>'apk|exe|ios|rar|zip|nb0|sbf|ipa|xap',
		               'filePath'=>$filePath?preg_replace('/:/', '/', $filePath):NULL,
		               'size'=>'1 G',
	                   'domId'=>$domId
		               );

		$layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/upload_file', $data, TRUE)
			      );

	    $this->load->view('layout/default', $layout);
	}



	public function uploadMovie($filePath = NULL) {
	    if ($_POST) {
	        $ret = $this->_upload('filePath', './uploads', 'avi|mp4|mov|mpg|mpeg|wmv|vob|m4v|flv', '204800');
	        $fileExt = $ret['file_ext'];
			$ffmpeg = 'D:/xzp/ffmpeg_full_SDK_V3.0/Libs/ffmpeg.exe';
			$soursePath = $ret['full_path'];
			$movieTargetPath = $ret['file_path'].$ret['raw_name'].'.flv';
			$imageTargetPath = $ret['file_path'].$ret['raw_name'].'_thumb.jpg';

			$width = $this->input->post('width');
			$height = $this->input->post('height');

			//进行视频转换，mp4格式的不用转换
			if (strtolower($fileExt) != '.mp4') {
			    if (! $this->movietransformclass->createMovie($ffmpeg, $soursePath, $movieTargetPath, 320, 240)) {
			        @unlink($soursePath);
			        @unlink($movieTargetPath);
			    	$data = array(
				            'msg'=>'文件上传失败!',
				            'url'=>base_url().'admincp.php/upload/videoView/'.$model.'/'
				            );
				    $this->session->set_userdata($data);
				    redirect('/message/index');

			    }
			}
	    }

	    $data = array(
		        'ext'=>'avi|mp4|mov|mpg|mpeg|wmv|vob|m4v|flv',
		        'filePath'=>$filePath?preg_replace(array('/_/', '/\./'), array('/', '_thumb.'), $filePath):NULL,
		        'size'=>'200 M',
		        'width'=>'320',
		        'height'=>'240'
		        );

		$layout = array(
			      'title'=>$this->_title,
				  'content'=>$this->load->view('upload/upload_movie', $data, TRUE)
			      );

	    $this->load->view('layout/default', $layout);
	}
	

	public function updateAlt() {
	    $attachmentIds = $this->input->post('attachmentIds', TRUE);
	    $alts = $this->input->post('alts', TRUE);
	    $attachmentIdsArray = explode(",", $attachmentIds);
	    $altsArray = explode(",", $alts);

	    if ($attachmentIdsArray) {
	        foreach ($attachmentIdsArray as $key=>$value) {
	            $this->Attachment_model->save(array('alt'=>$altsArray[$key]), array('id'=>$value));
	        }
	    }
	    printAjaxSuccess('', '修改成功！');
	}

    private function _upload($field, $filePath = './uploads', $ext = 'gif|jpg|jpeg|png', $maxSize = '20480') {
	    $config['upload_path'] = createDateTimeDir($filePath);
	    $config['file_name'] = getUniqueFileName($filePath);
	    $config['allowed_types'] = $ext;
	    $config['max_size'] = $maxSize;
	    $this->load->library('upload', $config);
	    $this->_thumbPath = substr($config['upload_path'], 2);

	    if ($this->upload->do_upload($field)) {
	        return $this->upload->data();
	    } else {
			var_dump($this->upload->display_errors());
	        return false;
	    }

	    return false;
	}

    private function _resize($fileName, $width = '100', $height = '100', $thumbMarker = '_thumb') {
		$this->config->load('image_config', TRUE);
		$imageConfig = $this->config->item('image_config');
		$imageConfig['source_image'] = $fileName;
		$imageConfig['new_image'] = $fileName;
		$imageConfig['width'] = $width;
		$imageConfig['height'] = $height;
		$imageConfig['thumb_marker'] = $thumbMarker;
		$this->load->library('image_lib');
		$this->image_lib->initialize($imageConfig);

		return $this->image_lib->resize();
	}

	private function _crop($fileName, $width = '100', $height = '100', $x, $y, $thumbMarker = '_thumb') {
		$this->config->load('image_config', TRUE);
		$imageConfig = $this->config->item('image_config');
		$imageConfig['source_image'] = $fileName;
		$imageConfig['new_image'] = $fileName;
		$imageConfig['width'] = $width;
		$imageConfig['height'] = $height;
		$imageConfig['x_axis'] = $x;
		$imageConfig['y_axis'] = $y;
		$imageConfig['thumb_marker'] = $thumbMarker;
		$imageConfig['maintain_ratio'] = false;
		$this->load->library('image_lib');
		$this->image_lib->initialize($imageConfig);

		return $this->image_lib->crop();
	}	

	private function _watermark($sourcFileName, $watermarkPath, $vrt = 'bottom', $hor = 'right') {
		$this->config->load('watermark_config', TRUE);
		$watermarkConfig = $this->config->item('watermark_config');
		$watermarkConfig['source_image'] = $sourcFileName;
		$watermarkConfig['wm_overlay_path'] = $watermarkPath;
		$watermarkConfig['wm_vrt_alignment'] = $vrt;
		$watermarkConfig['wm_hor_alignment'] = $hor;
		$this->load->library('image_lib');
		$this->image_lib->initialize($watermarkConfig);

		return $this->image_lib->watermark();
	}
	
	//批量上传图片
	public function batch_upload($model = 'news', $attachmentIds = NULL, $id = NULL) {
	    $attachmentList = array();
	    //初始化
	    if (! empty($attachmentIds)) {
	        $ids = preg_replace(array('/^_/', '/_$/', '/_/'), array('', '', ','), $attachmentIds);
	        $attachmentList = $this->Attachment_model->gets2($ids);
	    }
	    $this->load->view('upload/batch_upload_native', array('attachmentList'=>$attachmentList, 'model'=>$model, 'id'=>$id));
    }
	
    //批量上传图片进行打水印,批量，上传图片用图片一个插件
	public function uploadImageByW() {
	    if ($_POST) {
			$baseDir = './uploads';
			$verifyToken = md5('unique_salt' . $_POST['timestamp']);
			$model = $_POST['model'];					
			if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
				if (isset($_FILES['Filedata']) && is_uploaded_file($_FILES['Filedata']['tmp_name']) && $_FILES['Filedata']['error'] == 0) {
					$uploadFile = $_FILES['Filedata'];
				    $uploadPath = createDateTimeDir($baseDir);
				    $uniqueFileName = getUniqueFileName($uploadPath);
				    $fileExt = getFileExt($uploadFile['name']);
				    $uploadFile['name'] = $uniqueFileName.'.'.$fileExt;
					$uploadFile['filename'] = $uploadPath.'/'.$uploadFile['name'];
					$size = $uploadFile['size'];					
					if(@move_uploaded_file($uploadFile['tmp_name'], $uploadFile['filename'])) {
						if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
//							if ($model == 'product') {
//			                    $this->_resize($uploadFile['filename'], $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '_max');
//			                }
							//打水印							
							$watermarkInfo = $this->Watermark_model->get('*', array('id'=>1, 'is_open'=>1));
							if ($watermarkInfo && $watermarkInfo['path']) {
								$location = explode(',', $watermarkInfo['location']);
								$this->_watermark($uploadFile['filename'], "./".$watermarkInfo['path'], $location[0], $location[1]);
					        }
							$fields = array(
					              'path'=>substr($uploadFile['filename'], 2),
					              'width'=>0,
					              'height'=>0,
					              'size'=>$size,
					              'alt'=>''
					              );
					        $attachmentId = $this->Attachment_model->save($fields);
							if ($attachmentId) {
								printAjaxData(array('id'=>$attachmentId, 'file_path'=>substr($uploadFile['filename'], 2), 'size'=>$size, 'ext'=>$fileExt));
							} else {
							    printAjaxError('fail', 'fail');
							}
						} else {
						    printAjaxError('fail', '上传失败');
						}					    
					} else {
					    printAjaxError('fail', '上传失败');
					}
				} else {
				    printAjaxError('fail', '上传失败');
				}
			} else {
			    printAjaxError('fail', '上传失败');
			}
		}
	}
}

/* End of file upload.php */

/* Location: ./application/admin/controllers/upload.php */