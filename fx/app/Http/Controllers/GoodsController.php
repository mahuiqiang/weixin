<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Cart;
class GoodsController extends Controller
{
    public function index(){
    	//得到表里的全部东西
    	$goods = DB::table('goods')->get();
    	//赋值到模板中
    	return view('goods.index',['goods'=>$goods]);
    }	

	public function insert(){
		//修改图片路径,添加到数据库中
	$data = [
		['goods_name'=>'月季','goods_price'=>'23.8','goods_img'=>'/images/goods_1.jpg'],
		['goods_name'=>'玫瑰','goods_price'=>'45.6','goods_img'=>'/images/goods_2.jpg'],
		['goods_name'=>'桃花','goods_price'=>'30.8','goods_img'=>'/images/goods_3.jpg'],
		['goods_name'=>'妖姬','goods_price'=>'55.6','goods_img'=>'/images/goods_4.jpg']
		];
	DB::table('goods')->insert($data);
	}

	//商品详情页
	public function goods($gid){
		$good=DB::table('goods')->where('gid',$gid)->first();
		return view('goods.goods',['good'=>$good]);
	}
	//购物车类  查看购物车
	public function cart(){
		//获取购物车内容
		$goods = Cart::getContent();
		//共多少钱
		$total = Cart::getTotal();
		return view('goods.cart',['goods'=>$goods,'total'=>$total]);
	}

	//加入购物车
	public function buy($gid){
		$g=DB::table('goods')->where('gid',$gid)->first();
		Cart::add($gid,$g->goods_name,$g->goods_price,1,array());
		//直接返回到购物车
		return redirect('cart');

	}

	//清空购物车
	public function cart_clear(){
		Cart::clear();
		return redirect('/');
	}
}
