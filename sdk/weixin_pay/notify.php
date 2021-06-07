<?php
require_once "lib/WxPay.Api.php";
require_once 'lib/WxPay.Notify.php';

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);

	    $str='./uploads/textxxx.sql';
	    @file_put_contents($str, json_encode($result));

		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}

	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		$str='./uploads/textxxxccc.sql';
		@file_put_contents($str, json_encode($data));
		$notfiyOutput = array();

		if(!array_key_exists("transaction_id", $data)){
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			return false;
		}
		return true;
	}
}