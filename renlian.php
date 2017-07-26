<?php 
$data = [
'api_key'=>'AWHXLEMVlhTju6raJrlAJS5R4gPe8jX_',
'api_secret'=>'j3etjvp-DZsz_SJpXdZNN0IOovVgO1KK',
'image_url'=>'http://mhq.tunnel.2bdata.com/konglong1.jpg', //图片地址
'return_landmark'=>'1',
'return_attributes'=>'age'
];
$url = 'https://api-cn.faceplusplus.com/facepp/v3/detect';
//初始化
$curl = curl_init();
//设置参数
curl_setopt($curl,CURLOPT_URL,$url);//设置请求的url
curl_setopt($curl, CURLOPT_HEADER,0);//是否需要header头  0不需要
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //是否需要输出
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);//ssl证书不需要
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//传的数据

//发送请求消息
$da = curl_exec($curl);
//关闭请求资源
curl_close($curl);

$arr = json_decode($da,true);//返回数组

var_dump($arr['faces']);//输出人脸的年龄 人数等消息

 ?>