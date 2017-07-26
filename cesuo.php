<?php 
$url = 'http://api.map.baidu.com/place/v2/search?query=%E5%8E%95%E6%89%80&location=39.915,116.404&scope=2&radius=2000&output=json&ak=id0agB7iea770CXqiQthTR4tylyfZUoW';
//var_dump(file_get_contents($url));
//$json_info= file_get_contents($url);
//var_dump($json_info);
$arr= json_decode(file_get_contents($url),true);
 //$arr
 var_dump($arr);
 ?>