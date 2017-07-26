<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use EasyWeChat\Foundation\Application;
use App\User;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\Image;
use DB;
class WxController extends Controller
{	
   
    		public $app='';
    		//构造方法  实例化
    		public function __construct(){
			$options = [
			    'debug'  => true,
			    'app_id' => 'wxc4cc6691ce898bce',
			    'secret' => '4f2e0b4f32e1952298b39e2c4b025043',
			    'token'  => 'mhq',
			    // 'aes_key' => null, // 可选   下面是日志
			    'log' => [
			        'level' => 'debug',
			        'file'  => 'D:\xampp\htdocs\weixin\fx\easywechat.log', // XXX: 绝对路径！！！！
			    ],
			    //...
			];
			$this->app = new Application($options);
		}

		/**
		 * [index 接入微信]
		 * @return [type] [description]
		 */
		public function index(){
			//$response = $app->server->serve();
			// 将响应输出
			//$response->send(); // Laravel 里请使用：return $response;
			//return $response;
			$server = $this->app->server;
			//消息处理函数  $message相当于 $postobj
			$server->setMessageHandler(function ($message) {
			    // $message->FromUserName // 用户的 openid
			    // $message->MsgType // 消息类型：event, text....
			    // 关注事件回复
			    if ($message->MsgType == 'event') {
			    		return $this->shijian($message);
			    		//return "呵呵";
			    }else{
			    	return $this->xiaoxi($message);
			    }
			    
			});
			//$server是对象
			$response = $server->serve();
			return $response;//给用户发回复
    }
    //素材
    public function sucai($url){
    		//永久素材
    		$material = $this->app->material;
    		//上传图片
    		$result = $material->uploadImage($url); 
    		//var_dump($result);
    		$arr = json_decode($result,true);
			return $arr['media_id'];
    }

    public function yysucai(){
    		//永久素材
    		$material = $this->app->material;
    		//上传图片
    		$result = $material->uploadVoice($url); 
    		//var_dump($result);
    		$arr = json_decode($result,true);
			return $arr['media_id'];   	
    }
    //事件 $message 相等于$postobj对象
    public function shijian($message){
    		$openId=$message->FromUserName;
    		$userinfo=new User();
    		//判断用户发过来的是什么事件类型
    		if ($message->Event == 'subscribe') {
    			$quser = User::where('openid',$openId)->first();
    			//如果有此用户,则直接改变状态
			 	if ($quser) {
			 		$quser->status = 1;
			 		$quser->save();
				 	}else{
				 		//新用户关注
	   			 /*获取用户的基本信息并且入库*/
					$userService = $this->app->user;
					//获取用户信息
					$user=$userService->get($openId);//得到用户对象
					$userinfo->name=$user->nickname;//昵称
					$userinfo->openid=$openId;
					$userinfo->pubtime=time();
					//扫描的是带参数的二维码
					if ($message->EventKey) {
						$p1_openid = str_replace('qrscene_','',$message->EventKey);
						//查询上级的3个pid
						$p = DB::table('users')->where('openid',$p1_openid)->first();
						$userinfo->p1=$p->uid;
						$userinfo->p2=$p->p1;
						$userinfo->p3=$p->p2;
						}
					$userinfo->save();//保存
				}
					//二维码
					$this->qrcode($openId);
					return 'O(∩_∩)O谢谢关注!';
			 }elseif($message->Event == 'unsubscribe'){
			 	//如果取消关注则改变状态
			 	$quser = User::where('openid',$openId)->first();
			 	if ($quser) {
			 		$quser->status = 0;
			 		$quser->save();
			 	}
			 }
    }





    public function xiaoxi($message){
    	if ($message->MsgType == 'text') {
    			$text = new Text();
				if ($message->Content == 1) {
					return $text->content='你好';

				}elseif($message->Content == '音乐'){

					$url=public_path()."/1.mp3";
				    return  $text = new Voice(['media_id' => $this->yysucai($url)]);		

				}elseif($message->Content == '图片'){
					//$mediaId='http://mhq.tunnel.2bdata.com/wx/1.jpg';
					$url=public_path()."/1.jpg";
				 return  $text = new Image(['media_id' => $this->sucai($url)]);
				  //return $this->tupiansucai();
				}
    	}
    }


  	public function qrcode($openId){
   		//获取创建二维码实例
   		$qrcode = $this->app->qrcode;
   		//创建永久二维码
   		$result = $qrcode->forever($openId);
   		//ticket
   		$ticket = $result->ticket;
   		//创建二维码链接
   		$url = $qrcode->url($ticket);
   		//得到二进制图片
   		$data = file_get_contents($url);
   		file_put_contents(public_path(). '/' . $openId . '.jpg' ,$data);
   	}



   	//获取code用于第二步调用oauth2/access_token接口，获取授权后的access token
   	public function weibo(){
   		$weibo_code=$_GET['code'];
   		//使用curl请求url   从而得到令牌    第二步
   		$url='https://api.weibo.com/oauth2/access_token';//请求地址
   		//请求参数
   		$data = [
			'client_id' => '83013785', //APPKEY
			'client_secret'=>'1bb7eaea1889d2c7d7c44b33f45bb97d',
			'grant_type'=>'authorization_code',
			'code'=>$weibo_code,
			'redirect_uri'=>'http://mhq.tunnel.2bdata.com/weibo'
		];
		$ch = curl_init($url);//先初始化
		curl_setopt($ch,CURLOPT_POST,1);//post请求方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER ,1);//直接输出
		$output=curl_exec($ch); //得到返回的信息
		curl_close($ch);//关闭资源
		// var_dump($output);得到的是json形式的字符串
		// exit();
		//得到令牌 返回的是json  json_decode第二个参数为true时返回的数组
		//print_r(json_decode($output,true)['access_token']);
		$arr=json_decode($output,true);
		$token=$arr['access_token'];
		$uid=$arr['uid'];

		// 用得到的令牌和uid获得用户信息
		$userinfo =file_get_contents('https://api.weibo.com/2/users/show.json?access_token='.$token.'&uid='.$uid);
		$userinfo=json_decode($userinfo,true);
		echo '欢迎'.$userinfo['name'].'大爷,大驾光临!';
		//获取用户的信息后,显示在要登录的网站上
		

		//第一步 点击微博登录后跳转到微博登录页面(接口).第二步输入密码后跳转到此方法中,先GET得到code再用code去请求得到access_token,最后凭借token得到用户信息并返回到回调页面
   	}


   	public function denglu(){
   		return view('login');
   	}



}
