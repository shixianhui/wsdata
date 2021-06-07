<?php

class Upload extends CI_Controller {

    private $_title = '文件上传';
    private $_tool = '';
    private $_thumbPath = '';
    private $_imageSize = array('exchange' => array('width' => 280, 'height' => 280),
        'watermark' => array('width' => 24, 'height' => 24),
        'user' => array('width' => 100, 'height' => 100),
        'brand' => array('width' => 130, 'height' => 60),
        'product_category' => array('width' => 120, 'height' => 120),
        'list_path_logo' => array('width' => 190, 'height' => 70),
        'store_logo' => array('width' => 220, 'height' => 220),
        'id_card_path_1' => array('width' => 142, 'height' => 80),
        'id_card_path_2' => array('width' => 142, 'height' => 80),
        'license_path' => array('width' => 142, 'height' => 80),
        'comment' => array('width' => 280, 'height' => 280),
        'dishes' => array('width' => 226, 'height' => 171, 'max_width' => 818, 'max_height' => 818),
        'combos' => array('width' => 363, 'height' => 270, 'max_width' => 818, 'max_height' => 818),
        'grasses' => array('width' => 363, 'height' => 270, 'max_width' => 818, 'max_height' => 818),
        'share_goods' => array('width' => 363, 'height' => 270, 'max_width' => 818, 'max_height' => 818),
        'store' => array('width' => 414, 'height' => 414),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('Attachment_model', '', TRUE);
        $this->load->model('Watermark_model', '', TRUE);
        $this->load->model('User_model', '', TRUE);
        $this->load->helper(array('my_fileoperate'));
        $this->load->helper(array('my_fileoperate'));
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

    //批量上传图片进行打水印,批量，上传图片用图片一个插件
    public function uploadImageByW() {
        if ($_POST) {
            $model = $_POST['model'];
            $baseDir = './uploads';
            $verifyToken = md5('unique_salt' . $_POST['timestamp']);
            if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
                if (isset($_FILES['Filedata']) && is_uploaded_file($_FILES['Filedata']['tmp_name']) && $_FILES['Filedata']['error'] == 0) {
                    $uploadFile = $_FILES['Filedata'];
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
                                printAjaxData(array('id' => $attachmentId, 'file_path' => substr($uploadFile['filename'], 2), 'size' => $size, 'ext' => $fileExt));
                            } else {
                                printAjaxError('fail');
                            }
                        } else {
                            printAjaxError('上传失败');
                        }
                    } else {
                        printAjaxError('上传失败');
                    }
                } else {
                    printAjaxError('上传失败');
                }
            } else {
                printAjaxError('上传失败');
            }
        }
    }

    public function uploadImage() {
        $model = $this->input->post('model', TRUE);
        $field = $this->input->post('field', TRUE);
        $ret = $this->_upload($model, $field);
        //用户头像
        if ($model === 'avatar' && !get_cookie('user_id')) {
            printAjaxError('fail', '您尚未登录!');
        }
        if (!$ret) {
            printAjaxError('fail', '请选择上传的文件');
        }
        if ($this->_resize("./" . $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
            $attachment = array(
                'path' => $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext'],
                'width' => $ret['image_width'],
                'height' => $ret['image_height'],
                'size' => $ret['file_size'],
                'alt' => $ret['file_name']
            );
            $ret_id = $this->Attachment_model->save($attachment);
            if ($model === 'avatar' && get_cookie('user_id')) {
                $this->User_model->save(array('path' => $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext']), "id = " . get_cookie('user_id'));
            }
            printAjaxData(array('id' => $ret_id, 'field' => "r=" . rand(10000, 99999) . "", 'file_path' => $this->_thumbPath . '/' . $ret['raw_name'] . $ret['file_ext']));
        } else {
            echo $this->upload->display_errors();
        }
    }

    public function upload_ad_store() {
        checkLoginAjax(true);
        $store_info = $this->Store_model->get('id', array('user_id' => get_cookie('user_id')));
        if ($_POST) {
            $baseDir = './uploads';
            $model = $_POST['model'];
            $fields = $_POST['field'];
            $position = $fields == 'path_file1' ? 1 : 2;
            $count = $this->Ad_store_model->rowCount(array('position' => $position, 'store_id' => $store_info['id']));
            if ($count + count($_FILES[$fields]['tmp_name']) > 5) {
                printAjaxError('fail', '最多上传5张');
            }
            $ret = array();
            if ($_FILES[$fields]['tmp_name']) {
                foreach ($_FILES[$fields]['tmp_name'] as $key => $value) {
                    if (is_uploaded_file($value)) {
                        $uploadPath = createDateTimeDir($baseDir);
                        $uniqueFileName = getUniqueFileName($uploadPath);
                        $fileExt = 'png';
                        $uploadFile['name'] = $uniqueFileName . '.' . $fileExt;
                        $uploadFile['filename'] = $uploadPath . '/' . $uploadFile['name'];
                        if (@move_uploaded_file($value, $uploadFile['filename'])) {
                            if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
                                if ($model == 'house' || $model == 'parking') {
                                    $this->_resize($uploadFile['filename'], $this->_imageSize[$model]['max_width'], $this->_imageSize[$model]['max_height'], '_max');
                                }
                                //打水印
                                $watermarkInfo = $this->Watermark_model->get('*', array('id' => 1, 'is_open' => 1));
                                if ($watermarkInfo && $watermarkInfo['path']) {
                                    $location = explode(',', $watermarkInfo['location']);
                                    $this->_watermark($uploadFile['filename'], "./" . $watermarkInfo['path'], $location[0], $location[1]);
                                }
                                $ad_store_info = $this->Ad_store_model->get('max(sort) as max_sort',array('position'=>$position,'store_id'=>$store_info['id']));
                                if($ad_store_info){
                                    $sort = $ad_store_info['max_sort']+1;
                                }else{
                                    $sort = 0;
                                }
                                $fields = array(
                                    'path' => substr($uploadFile['filename'], 2),
                                    'store_id' => $store_info['id'],
                                    'position' => $position,
                                    'sort' => $sort
                                );
                                $retId = $this->Ad_store_model->save($fields);
                                if ($retId) {
                                    $ret[] = array('id' => $retId, 'field' => "r=" . rand(10000, 99999) . "", 'file_path' => substr($uploadFile['filename'], 2),'sort'=>$sort);
                                }
                            }
                        }
                    }
                }
            }
            printAjaxData($ret);
        }
    }

    public function upload_auth_file(){
        $field = $this->input->post('field', TRUE);
        $baseDir = './uploads';
        $config['upload_path'] = createDateTimeDir($baseDir);
        $config['file_name'] = getUniqueFileName($baseDir);
        $config['allowed_types'] = 'doc|docx';
        $config['max_size'] = 4048;
        $this->load->library('upload', $config);
        $this->_thumbPath = substr($config['upload_path'], 2);
        if ($this->upload->do_upload($field)) {
            $result = $this->upload->data();     
            printAjaxData(array('file_path' =>$this->_thumbPath.'/'.$result['raw_name'] . $result['file_ext'],'client_name'=>$result['client_name']));
        } else {
            printAjaxError('fail',$this->upload->display_errors());
        }
    }

    //原生上传图片
    public function batch_uploadImage() {
        if ($_POST) {
            $field = $this->input->post('field',true);
            $pic_num = $this->input->post('pic_num',true);
            $max_pic_num = $this->input->post('max_pic_num',true);
            if($max_pic_num){
                if ($pic_num > $max_pic_num || count($_FILES[$field]['tmp_name']) > $max_pic_num-$pic_num) {
                    printAjaxError('fail', "最多上传{$max_pic_num}张");
                }
            }
            $baseDir = './uploads';
            $model = $_POST['model'];
            $ret = array();
            if ($_FILES[$field]['tmp_name']) {
                foreach ($_FILES[$field]['tmp_name'] as $key=>$value) {
                    if (is_uploaded_file($value)) {
                        $uploadPath = createDateTimeDir($baseDir);
                        $uniqueFileName = getUniqueFileName($uploadPath);
                        $fileExt = 'png';
                        $uploadFile['name'] = $uniqueFileName.'.'.$fileExt;
                        $uploadFile['filename'] = $uploadPath.'/'.$uploadFile['name'];
                        if(@move_uploaded_file($value, $uploadFile['filename'])) {
                            if ($this->_resize($uploadFile['filename'], $this->_imageSize[$model]['width'], $this->_imageSize[$model]['height'])) {
                                if (!empty($this->_imageSize[$model]['max_width'])) {
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
}

/* End of file upload.php */

/* Location: ./application/admin/controllers/upload.php */