<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2016/12/16
 * Time: 16:32
 */
header("Content-Type:text/html;charset=utf-8");
// 发送短信地址，以下为示例地址，具体地址询问网关获取
$url_send_sms = "http://43.243.130.33:8860/sendSms";
// 用户账号，必填
$cust_code = "100454";
// 用户密码，必填
$cust_pwd = "ZZR9PLBFXY";
// 短信内容，必填
$content = "happy123";
// 接收号码，必填，同时发送给多个号码时,号码之间用英文半角逗号分隔
$destMobiles = "13517210601,17196694824";
// 业务标识，选填，由客户自行填写不超过20位的数字
$uid = "";
// 长号码，选填
$sp_code = "";
// 是否需要状态报告
$need_report = "no";
// 签名，签名内容根据 “短信内容+客户密码”进行MD5编码后获得
$sign = $content.$cust_pwd;
$sign = md5($sign);
$ch = curl_init();
/* 设置验证方式 */
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','charset=utf-8'));
/* 设置返回结果为流 */
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
/* 设置超时时间*/
curl_setopt($ch, CURLOPT_TIMEOUT, 300);
/* 设置通信方式 */
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 发送短信
$data=array('cust_code'=>$cust_code,'sp_code'=>$sp_code,'content'=>$content,'destMobiles'=>$destMobiles,'uid'=>$uid,'need_report'=>$need_report,'sign'=>$sign);
$json_data = json_encode($data);
echo "send msg:".$json_data . PHP_EOL;
$resp_data = send($ch,$json_data,$url_send_sms);
echo "recv msg:".$resp_data . PHP_EOL;
curl_close($ch);
function send($ch,$data,$send_url){
    curl_setopt ($ch, CURLOPT_URL, $send_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    return curl_exec($ch);
}
?>