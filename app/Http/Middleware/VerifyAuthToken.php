<?php namespace App\Http\Middleware;

use Closure;
use Hash;
use Log;
use Validator;

use App\Models\MerchantToken;
use App\Models\Merchant;
use App\Models\Restaurant;

use App\Models\Observers\FoodObserver;

//ERROE NUMBER START 21xx;

class VerifyAuthToken {

	/**
	 * 验证API TOKEN
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		//非https链接不回应 放行本地跳转
		if(!$request->secure() && !($request->ip() == '127.0.0.1')){
			//abort(403);
		}

		$request_path = $request->path();

		$request->merge(['tag'=> 'merchant']);
		$request->merge(['client_type'=> 0]);

		// Log::info($request);
		// Log::info($request->all());

		// Log::info($request->getContent());
		// Log::info(gzdecode($request->getContent()));

		// $pwd = Hash::make('111234');
		// Log::info('pwd = '.$pwd);
		// $observer = new FoodObserver('abcd');

		//参数校验
		$validator = Validator::make($request->all(),
			[
				'version'		=>	array('string', 'regex:/^\d.\d.\d$/'),
				'platform'		=>	'required',
				'device_id'		=>	'required',
			],
			[
				// 'version.required'	=> json_encode(['1','缺少参数version']),
				'version.string'	=> json_encode(['1','参数version类型错误']),
				'version.regex'	=> json_encode(['1','参数version格式错误']),
				'platform.required'	=> json_encode(['1','缺少参数platform']),
				'device_id.required'	=> json_encode(['1','缺少参数device_id']),
			]
		);
		if($validator->fails()){
			$message = $validator->messages()->first();
			return json_encode(['code'=>json_decode($message)[0],'msg'=>json_decode($message)[1]],JSON_UNESCAPED_UNICODE);
		}
		
		//登录url||注册url||获取验证码
		if($request_path == 'taurus/v1/user/login' || $request_path == 'taurus/v1/user/register' || $request_path == 'taurus/v1/sendcaptcha'){
			return $next($request);
		}

		//参数校验
		$validator = Validator::make($request->all(),
			[
				'token'			=>	'required',
				'device_id'		=>	'exists:merchant_token,device_hash',
			],
			[
				'token.required'		=> json_encode(['2000','设备验证失败 缺少参数token']),
				'device_id.exists'	 	=> json_encode(['2000','设备验证失败 device_id不存在']),
			]
		);

		if($validator->fails()){
			$message = $validator->messages()->first();
			return json_encode(['code'=>json_decode($message)[0],'msg'=>json_decode($message)[1]],JSON_UNESCAPED_UNICODE);
		}

		//取出对应token信息
		$token = MerchantToken::where('token', '=', $request->input('token'))->whereRaw('is_deleted = 0')->first();
		if (is_null($token)) {//token不存在
			return json_encode(['code'=>2000,'msg'=>'设备验证失败，请重新登录'],JSON_UNESCAPED_UNICODE);
		}
		
	    //找到了默认店铺
		if($token->restaurant_id){
			$restaurant = $token->restaurant;
			if (!is_null($restaurant) && !$restaurant->is_deleted) {
				$request->merge(['restaurant_id'=> $token->restaurant_id]);
			} else {
				$token->delete();
				return json_encode(['code'=>2001,'msg'=>'店铺不存在，请重新登录'],JSON_UNESCAPED_UNICODE);	
			}
		}

		//根据token信息 加入user_id
		$merchant = $token->merchant;
		if (!is_null($merchant) && !$merchant->is_deleted) {
			$request->merge(['user_id'=> $token->merchant_id]);
		} else {
			return json_encode(['code'=>2002,'msg'=>'用户不存在，请重新登录'],JSON_UNESCAPED_UNICODE);	
		}
		// Log::info($request);
		// Log::info($request->all());
		return $next($request);
	}

}
