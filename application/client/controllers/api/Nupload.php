<?php

class Nupload extends CI_Controller {

    private $_title = '文件上传';
    private $_tool = '';
    private $_thumbPath = '';
    private $_imageSize = array(
        'activity' => array('width' => 750, 'height' => 400),
        'watermark' => array('width' => 24, 'height' => 24),
        'id_card' => array('width' => 154, 'height' => 154),
        'supply' => array('width' => 180, 'height' => 180),
        'user' => array('width' => 100, 'height' => 100),
        'brand' => array('width' => 130, 'height' => 60),
        'product_category' => array('width' => 120, 'height' => 120),
        'ad_store' => array('width' => 750, 'height' => 400),
        'store' => array('width' => 414, 'height' => 414),
        'product' => array('width' => 280, 'height' => 280, 'max_width' => 418, 'max_height' => 418),
        'comment' => array('width' => 280, 'height' => 280, 'max_width' => 818, 'max_height' => 818),
        'life_news' => array('width' => 340, 'height' => 340, 'max_width' => 818, 'max_height' => 818),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('Users_model', '', TRUE);
        $this->load->model('Attachment_model', '', TRUE);
        $this->load->model('Watermark_model', '', TRUE);
        $this->load->helper(array('url', 'my_fileoperate', 'my_ajaxerror'));
        $this->_beforeFilter();
    }

    private function _upload($model = '', $field, $filePath = './uploads', $ext = 'gif|jpg|jpeg|png', $maxSize = '4048') {
        $baseDir = $filePath;
        $config['upload_path'] = createDateTimeDir($baseDir);
        $config['file_name'] = getUniqueFileName($baseDir);
        $config['allowed_types'] = $ext;
        $config['max_size'] = $maxSize;
        $this->load->library('upload', $config);
        $this->_thumbPath = substr($config['upload_path'], 2);

        if ($this->upload->do_upload($field)) {
            return $this->upload->data();
        } else {
            return false;
        }

        return false;
    }

    private function _resize($fileName, $width = '100', $height = '100', $thumbMarker = '_thumb') {
        $this->config->load('image_config', TRUE);
        $imageConfig = $this->config->item('image_config');
        $imageConfig['source_image'] = $fileName;
        $imageConfig['new_image'] = $fileName;
        $img_info = getimagesize($fileName);
        $width = $img_info[0] > $width ? $width : $img_info[0];
        $height = $img_info[0] > $width ? $height : $img_info[1];
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


    //修改用户头像
    public function change_user_image() {
        if (!$this->session->userdata("user_id")) {
            printAjaxError('login', '请登录');
        }
        if ($_POST) {
            $user_id = $this->session->userdata("user_id");
            $user_info = $this->Users_model->get('id', array('id' => $user_id));
            if (!$user_info) {
                printAjaxError('fail', '请登录');
            }
            $model = $_POST['model'];
            $baseDir = './uploads';
            $verifyToken = md5('unique_salt' . $_POST['timestamp']);
            if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
                if (isset($_FILES['field']) && is_uploaded_file($_FILES['field']['tmp_name']) && $_FILES['field']['error'] == 0) {
                    $uploadFile = $_FILES['field'];
                    $uploadPath = createDateTimeDir($baseDir);
                    $uniqueFileName = getUniqueFileName($uploadPath);
                    $fileExt = getFileExt($uploadFile['name']);
                    $uploadFile['name'] = $uniqueFileName . '.' . $fileExt;
                    $uploadFile['filename'] = $uploadPath . '/' . $uploadFile['name'];
                    $size = $uploadFile['size'];
                    if (@move_uploaded_file($uploadFile['tmp_name'], $uploadFile['filename'])) {
                        if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
                            $fields = array(
                                'path' => substr($uploadFile['filename'], 2),
                                'width' => 0,
                                'height' => 0,
                                'size' => $size,
                                'alt' => ''
                            );
                            $attachmentId = $this->Attachment_model->save($fields);
                            if ($attachmentId) {
                                $path = substr($uploadFile['filename'], 2);
                                $tmp_image_arr = $this->_fliter_image_path($path);
                                if ($model == 'user') {
                                    if (!$this->Users_model->save(array('path' => $path), array('id' => $user_info['id']))) {
                                        printAjaxError('fail', '用户头像修改失败');
                                    }
                                }
                                printAjaxData(array('id' => $attachmentId, 'path' => $path, 'path_url' => $tmp_image_arr['path'], 'path_thumb_url' => $tmp_image_arr['path_thumb']));
                            } else {
                                printAjaxError('fail', '上传图片失败');
                            }
                        } else {
                            printAjaxError('fail', '生成缩略图失败');
                        }
                    } else {
                        printAjaxError('fail', '复制文件错误');
                    }
                } else {
                    printAjaxError('field', 'field构造错误');
                }
            } else {
                printAjaxError('token', 'token参数错误');
            }
        }
    }

    public function uploadImage() {
        if ($_POST) {
            $model = $this->input->post('model', TRUE);
            $user_id = $this->session->userdata("user_id");
            $user_info = $this->Users_model->get('id', array('id' => $user_id));
            if (!$user_info) {
                printAjaxError('login', '用户信息不存在');
            }

            $baseDir = './uploads';
            $verifyToken = md5('unique_salt' . $_POST['timestamp']);
            if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
                if (isset($_FILES['field']) && is_uploaded_file($_FILES['field']['tmp_name']) && $_FILES['field']['error'] == 0) {
                    //审核图片违规
                    $this->load->library('baiduapiclass');
                    $result = $this->baiduapiclass->image_review($_FILES['field']['tmp_name']);
                    if (!$result) {
                        printAjaxError('fail', '图片违规');
                    }
                    $uploadFile = $_FILES['field'];
                    $uploadPath = createDateTimeDir($baseDir);
                    $uniqueFileName = getUniqueFileName($uploadPath);
                    $fileExt = getFileExt($uploadFile['name']);
                    $uploadFile['name'] = $uniqueFileName . '.' . $fileExt;
                    $uploadFile['filename'] = $uploadPath . '/' . $uploadFile['name'];
                    $size = $uploadFile['size'];
                    if (@move_uploaded_file($uploadFile['tmp_name'], $uploadFile['filename'])) {
                        //判断ios旋转
//                        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
                        $image = imagecreatefromstring(file_get_contents($uploadFile['filename']));
                        $exif = @exif_read_data($uploadFile['filename']);
                        if (!empty($exif['Orientation'])) {
                            switch ($exif['Orientation']) {
                                case 8:
                                    $image = imagerotate($image, 90, 0);
                                    break;
                                case 3:
                                    $image = imagerotate($image, 180, 0);
                                    break;
                                case 6:
                                    $image = imagerotate($image, -90, 0);
                                    break;
                            }
                            imagejpeg($image, $uploadFile['filename']);
                        }
//                        }
                        if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
                            if (!empty($this->_imageSize[$model]['max_width'])) {
                                $this->_resize($uploadFile['filename'], $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '_max');
                            }
                            $fields = array(
                                'path' => substr($uploadFile['filename'], 2),
                                'width' => 0,
                                'height' => 0,
                                'size' => $size,
                                'alt' => ''
                            );
                            $attachmentId = $this->Attachment_model->save($fields);
                            if ($attachmentId) {
                                $path = substr($uploadFile['filename'], 2);
                                $tmp_image_arr = $this->_fliter_image_path($path);

                                printAjaxData(array('id' => $attachmentId, 'path' => $path, 'path_url' => $tmp_image_arr['path'], 'path_thumb_url' => $tmp_image_arr['path_thumb']));
                            } else {
                                printAjaxError('fail', '上传图片失败');
                            }
                        } else {
                            printAjaxError('fail', '生成缩略图失败');
                        }
                    } else {
                        printAjaxError('fail', '复制文件错误');
                    }
                } else {
                    printAjaxError('field', 'field构造错误');
                }
            } else {
                printAjaxError('token', 'token参数错误');
            }
        }
    }



//h5批量传图
    public function uploadImageBatch() {
        if ($_POST) {

            $baseDir = './uploads';
            $model = $_POST['model'];
            $ret = array();
            if ($_FILES['path_file']['tmp_name']) {
                if (count($_FILES['path_file']['tmp_name']) > 3) {
                    printAjaxError('fail', '最多上传3张');
                }
                foreach ($_FILES['path_file']['tmp_name'] as $key=>$value) {
                    if (is_uploaded_file($value)) {
                        $uploadPath = createDateTimeDir($baseDir);
                        $uniqueFileName = getUniqueFileName($uploadPath);
                        $fileExt = 'png';
                        $uploadFile['name'] = $uniqueFileName.'.'.$fileExt;
                        $uploadFile['filename'] = $uploadPath.'/'.$uploadFile['name'];
                        if(@move_uploaded_file($value, $uploadFile['filename'])) {
                            if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
                                if ($model == 'house' || $model == 'parking') {
                                    $this->_resize($uploadFile['filename'], $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '_max');
                                }
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
                                    $path = substr($uploadFile['filename'], 2);
                                    $tmp_image_arr = $this->_fliter_image_path($path);
                                    $ret[] = array('id' => $attachmentId, 'path' => $path, 'path_url' => $tmp_image_arr['path'], 'path_thumb_url' => $tmp_image_arr['path_thumb']);
                                }
                            }
                        }
                    }
                }
            }
            printAjaxData(array('item_list'=>$ret));
        }
    }

    
    private function _fliter_image_path($image_path = NULL) {
        $path = '';
        $path_thumb = '';
        if ($image_path) {
            $path = base_url() . $image_path;
            $path_thumb = base_url() . preg_replace('/\./', '_thumb.', $image_path);
        }

        return array('path' => $path, 'path_thumb' => $path_thumb);
    }

    private function _beforeFilter() {
        $sid = $this->input->get('sid');
        if ($sid) {
            $sid = preg_replace('/sid-/', '', $sid);
            if ($sid) {
                $ret = NULL;
                $this->db->select('timestamp');
                $query = $this->db->get_where(config_item('sess_save_path'), array('id'=>$sid));
                if ($query->num_rows() > 0) {
                    $ret = $query->result_array();
                    $ret = $ret[0];
                }
                if (!$ret) {
                    $this->session->sess_destroy();
                    return FALSE;
                }
                //大于默认时间，系统 会自动更新
                if (($ret['timestamp'] + config_item('sess_time_to_update')) >= time()) {
                    return FALSE;
                } else {
                    if (config_item('sess_use_database') == TRUE) {
                        $this->db->update(config_item('sess_save_path'), array('timestamp'=>time()), array('id'=>$sid));
                    }
                }
            }
        }
    }

}

/* End of file upload.php */

/* Location: ./application/admin/controllers/upload.php */