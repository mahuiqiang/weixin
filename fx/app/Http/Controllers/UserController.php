<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use EasyWeChat\Foundation\Application;
use Session;
class UserController extends Controller
{
    public $app = null;
    //在__construct中实例化Application,并赋给$app属性,便于在所有方法中引用
    public function __construct(){
    	$options = [
		'debug' => true,
		'app_id' => 'wxc4cc6691ce898bce',
		'secret' => '4f2e0b4f32e1952298b39e2c4b025043',
		'token' => 'mhq',

		// 'aes_key' => null, // 可选
		
		'log' => [
		'level' => 'debug',
		'file' => 'D:\xampp\htdocs\weixin\fx\easywechat.log', // 绝对路径！ ！ ！ ！
		],

			//用户回调页面 oauth2.0 协议
			'oauth' => [
			'scopes' => ['snsapi_userinfo'],
			'callback' => '/login',//回调url  没有登录的话跳转到login页面
			],
		];
		$this->app = new Application($options);
	}
	
	//网页授权  用户中心页面
	public function center(Request $req){
		//判断如果没有用户登录跳转登录页面
		if (!$req->session()->has('wechat_user')) {
			$oauth = $this->app->oauth;
			return $oauth->redirect();//跳转到设置的callback中
		}
		return 'hello,你好啊,欢迎登录!';
	}



	public function login(){
			$oauth = $this->app->oauth;
			$user = $oauth->user();
			//设置session 把用户信息存到session
			Session::put('wechat_user',$user);
			return redirect('center');
			//return "妈的";
	} 

	public function logout(){
		Session::forget('wechat_user');
	}

}
