<?php
/**
 * 惠本地分销接口
 * User: sxh
 * Date: 2021/02/24
 * Time: 17:06
 */
class Hbdapiclass
{
    private $appid = "38b48704b1754f13916a944cd5f55de8";
    private $appkey = "259427f91e7348b28d176a3314d3df14";
    private $url = "http://www.huibendi.cn/openapi/gateway";
    protected $CI;
    public function __construct()
    {
        $this->CI = & get_instance();
    }

    /**
     * 查询可分销商品列表
     */
    public function hbd_query_product_list($page = 1)
    {
        $timestamp = time();
        $sign = strtolower(md5("appid={$this->appid}&appkey={$this->appkey}&timestamp={$timestamp}"));

        $data = [
            'method' => 'Distributor.QueryProductList2', 
            'appid' => $this->appid, 
            'timestamp' => $timestamp, 
            'sign' => $sign, 
            'biz_content' => json_encode(["PageSize"=>100, "PageIndex"=>$page]), 
        ];
        $result = http_curl($this->url, json_encode($data), 1);
        $result = json_decode($result, true);
        if ($result['Status'] == 0) {
            $product_list = $result['Data']['list'];
            if ($product_list) {
                $batch_values_str = '';
                $this->CI->load->model('Combos_model', '', TRUE);
                foreach ($product_list as $value) {
                    $product_info = $this->hbd_query_product_info($value['ID']);
                    $datas = [
                        'store_id' => 8,
                        'type' => 1,
                        'product_id' => $value['ID'],
                        'name' => $value['NAME'],
                        'cover_image' => $value['BookImg'],
                        'price' => $value['PRICE'],
                        'original_price' => $value['OldPrice'],
                        'city' => $product_info['City'] ? $product_info['City'] : '',
                        'content' => unhtml($product_info['REMARK']),
                        'usage_rules' => unhtml($product_info['SPXZ']),
                        'display' => 1
                    ];
                    if (strtotime($product_info['XJ_DATE']) < time() || strtotime($product_info['END_DATE']) < time()) {
                        $datas['display'] = 0;
                    }
                    $values_str = '(';
                    foreach ($datas as $item) {
                        $values_str .= $this->CI->db->escape($item).",";
                    }
                    $batch_values_str .= substr($values_str, 0, -1).'),';
                    // $this->CI->db->query("INSERT INTO combos (store_id,type,product_id,name,cover_image,price,original_price,city,content,usage_rules,display) VALUES 
                    // {$values_str} 
                    // ON DUPLICATE KEY UPDATE 
                    // price = VALUES(price),
                    // original_price = VALUES(original_price),
                    // display = VALUES(display)
                    // ;");
                }
                if ($batch_values_str) {
                    $batch_values_str = substr($batch_values_str, 0, -1);
                    $this->CI->db->query("INSERT INTO combos (store_id,type,product_id,name,cover_image,price,original_price,city,content,usage_rules,display) VALUES 
                    {$batch_values_str} 
                    ON DUPLICATE KEY UPDATE 
                    price = VALUES(price),
                    original_price = VALUES(original_price),
                    display = VALUES(display)
                    ;");
                }
                
            }
        }

    }

    /**
     * 查询商品详情
     */
    public function hbd_query_product_info($id = NULL)
    {
        $product_data = [];
        if (!$id) {
            return $product_data;
        }
        $timestamp = time();
        $sign = strtolower(md5("appid={$this->appid}&appkey={$this->appkey}&timestamp={$timestamp}"));

        $data = [
            'method' => 'Distributor.QueryProductInfo', 
            'appid' => $this->appid, 
            'timestamp' => $timestamp, 
            'sign' => $sign, 
            'biz_content' => json_encode(["ProductID"=>$id]), 
        ];
        $result = http_curl($this->url, json_encode($data), 1);
        $result = json_decode($result, true);
        if ($result['Status'] == 0) {
            $product_data = $result['Data'];
        }
        return $product_data;
    }

    /**
     * 创建订单(批量)
     */
    public function hbd_batch_import_order($biz_content = NULL)
    {
        $timestamp = time();
        $sign = strtolower(md5("appid={$this->appid}&appkey={$this->appkey}&timestamp={$timestamp}"));

        
        $data = [
            'method' => 'Distributor.BatchImportOrder', 
            'appid' => $this->appid, 
            'timestamp' => $timestamp, 
            'sign' => $sign, 
            'biz_content' => json_encode($biz_content), 
        ];
        $result = http_curl($this->url, json_encode($data), 1);
        $result = json_decode($result, true);
        logs($result);
        if ($result['Status'] == 0 && !empty($result['Data']['List'][0])) {
            return $result['Data']['List'][0];
        } else {
            return [];
        }

    }
}
