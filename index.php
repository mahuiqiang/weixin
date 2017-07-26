<?php
//用GET获取微信服务器的参数token、 timestamp、 nonce,signature
// $signature = $_GET["signature"];
// $token = 'mhq';
// $timestamp = $_GET["timestamp"];
// $nonce = $_GET["nonce"];
// //将token、 timestamp、 nonce三个参数进行字典序排序
// $tmpArr = array($token, $timestamp, $nonce);
// sort($tmpArr,SORT_STRING);
// //将三个参数字符串拼接成一个字符串进行sha1加密
// $tmpArr = implode($tmpArr);
// $tmpArr = sha1($tmpArr);
// //将获得加密后的字符串可与signature对比
// if($tmpArr == $signature){
// //原样返回echostr参数内容
//  echo $_GET["echostr"];
//  exit;
// }
// exit();
// $p = $_POST['Content'];
// file_get_contents('a.txt',$p);
// echo $p;

//接受xml数据
$poststr = $GLOBALS['HTTP_RAW_POST_DATA'];
//var_dump($poststr);
//处理xml数据,转化成对象
$postobj = simplexml_load_string($poststr,'SimpleXMLElement',LIBXML_NOCDATA);
//把内容写入文件中
//file_put_contents('a.txt',$postobj->Content);


//回复信息时,中从服务器发给用户
// $Touser=$postobj->FromUserName;
// $tpl = "<xml>
// <ToUserName><![CDATA[$Touser]]></ToUserName>
// <FromUserName><![CDATA[$postobj->ToUserName]]></FromUserName>
// <CreateTime>12345678</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[你好]]></Content>
// </xml>";
// echo $tpl;

$tousername=$postobj->FromUserName;//用户  发送方
$fromusername = $postobj->TouserName;//开发者  接受方
$msgType = $postobj->MsgType;//消息类型
$keyword = $postobj->Content;//传过来消息内容
$time = time();//消息创建时间

$tpl = "<xml>
<FromUserName><![CDATA[%s]]></FromUserName>
<ToUserName><![CDATA[%s]]></ToUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
//echo $tpl;

$music_tpl ="<xml>
<FromUserName><![CDATA[%s]]></FromUserName>
<ToUserName><![CDATA[%s]]></ToUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Music>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<MusicUrl><![CDATA[%s]]></MusicUrl>
<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>
</xml>";
//图文
$image_tpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[%s]]></Title>
<Description><![CDATA[%s]]></Description>
<PicUrl><![CDATA[%s]]></PicUrl>
<Url><![CDATA[%s]]></Url>
</item>
</Articles>
</xml> ";
$guanzhu="<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Event><![CDATA[%s]]></Event>
</xml>";

if($msgType == 'event'){
	if ($postobj->Event == 'subscribe') {
			$msgType='text';
			$content = '欢迎关注!';
			//用sprintf转化
			$resultstr = sprintf($tpl,$fromusername,$tousername,$time,$msgType,$content);
			echo $resultstr;		
	}elseif ($postobj->Event == 'CLICK') {
			$msgType='music';
			$title = '花儿好红';
			$Description='为什么呢';
			$url ='http://www.1ting.com/player/d8/player_53004.html';
			$resultStr = sprintf($music_tpl, $fromusername, $tousername, $time, $msgType,$title,$Description,$url,$url);//转化%s
			echo $resultStr;		
	}
}

if ($msgType == 'text') {
		//判断关键字
		if ($keyword == 1) {
			$content = '你好';
			//用sprintf转化
			$resultstr = sprintf($tpl,$fromusername,$tousername,$time,$msgType,$content);
			echo $resultstr;
		}elseif($keyword == '音乐'){
			$msgType='music';
			$title = '花儿好红';
			$Description='为什么呢';
			$url ='http://www.1ting.com/player/d8/player_53004.html';
			$resultStr = sprintf($music_tpl, $fromusername, $tousername, $time, $msgType,$title,$Description,$url,$url);//转化%s
			echo $resultStr;

		}elseif($keyword == '图片'){
						$msgType='news';
						$title='清新小百合';
						$des="更多请关注!!!";
						$pic="http://mhq.tunnel.2bdata.com/1.jpg";
						$url="http://www.baidu.com";
						$resultStr = sprintf($image_tpl,$fromusername,$tousername,$time,$msgType,$title,$des,$pic,$url);
						echo $resultStr;
		}

}elseif ($msgType == 'image') {
		$data = [
			'api_key'=>'0M-5SCXGeJpVXurzZyXOfbKt38hXvc3g',
			'api_secret'=>'FjsEJmXKZcSpl0N6ylssgLCS-Y6EzeEz',
			'image_url'=>$postobj->PicUrl,
			'return_landmark'=>'1',
			'return_attributes'=>'gender,age'
		];
		$url = 'https://api-cn.faceplusplus.com/facepp/v3/detect';
		//1,初始化
		$curl = curl_init();
		//设置参数
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_HEADER,0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		//发送请求信息
		$da = curl_exec($curl);
		//关闭请求资源
		curl_close($curl);
		$arr = json_decode($da,true);//数组
		$num = count($arr['faces']);
		$contentStr = '这个图片有'.$num."个人\n";
		foreach($arr['faces'] as $k=>$v){
			$contentStr .= '第' . ($k + 1) . '个人,性别:'.$v['attributes']['gender']['value'].',年龄:'.$v['attributes']['age']['value'] . "\n"; 
		}

		$msgtype = 'text';
		$result = sprintf($tpl,$fromusername,$tousername,$time,$msgtype,$contentStr);
		echo $result;
}elseif ($msgType == 'voice') {
			$msgType = 'text';
			$content = '你的声音真是好听!';
			//用sprintf转化
			$resultstr = sprintf($tpl,$fromusername,$tousername,$time,$msgType,$content);
			echo $resultstr; 
}elseif ($msgType == 'location'){
		$url = 'http://api.map.baidu.com/place/v2/search?query=%E5%8E%95%E6%89%80&location='.$postobj->Location_X.','.$postobj->Location_Y.'116.404&scope=2&radius=2000&output=json&ak=id0agB7iea770CXqiQthTR4tylyfZUoW';
		$arr = json_decode(file_get_contents($url),true);//加true 得到的是数组

		$contentstr = '';
		foreach ($arr['results'] as $k => $v) {
			$contentstr .= $v['name'] .'在'. $v['address'] . ',距离你有' . $v['detail_info']['distance'] . "米\n";

		}
		$msgType = 'text';
		$resultstr = sprintf($tpl,$fromusername,$tousername,$time,$msgType,$contentstr);
		echo $resultstr; 


}
//u-peZdyHuELUv5epCKvOCEtNQxcEo9Iq3smXi6HmHOxIt28TvkOrI-GWDEK7gXwxAWNvTTRMBLLEirjs4kWh5oUXK8uiXjL0RIWOuT8u3osLAIeAAAHWE