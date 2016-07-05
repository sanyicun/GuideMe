<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Hash;
use Route;
use Validator;
use Carbon\Carbon;
use Cache;

use App\Models\Merchant;
use App\Models\MerchantToken;
use App\Models\InviteCode;
use App\Models\WhiteList;
use App\Models\Restaurant;
use App\Utils\HTTP;

use App\Services\Inters\Restaurant\RestaurantService;
use App\Services\Inters\Bottom\BottomService;

use Config;
use Log;

//ERROE NUMBER START 22xx;

class ApiUserController extends Controller {

	/** 
		下发验证码
 	 * @param phone
	 * @return json
	 */
	public function sendCaptcha(Request $request, BottomService $bottomService){
		$validator = Validator::make($request->all(),
			[
				'phone'	=>	'required|digits:11',//|unique:users,username',
			],
			[
				'phone.required' => json_encode(['1','请输入手机号码']),
				'phone.digits'	 => json_encode(['1','请输入正确的手机号码']),
			]
		);
		if($validator->fails()){
			$message = $validator->messages()->first();
			return $this->response(json_decode($message)[0], json_decode($message)[1]);
		}
                
		return $bottomService->sendCaptchaFromAries($request->input('phone'));
	}


	public function resetPassword(Request $request){
		$validator = Validator::make($request->all(),
			[
				'old_password' => 'required|min:6',
				'password' => 'required|min:6',
			],
			[
				'old_password.required'		 =>json_encode(['2308', '请输入密码']),
				'old_password.min'			 =>json_encode(['2309', '密码最少6位']),
				'password.required'		 =>json_encode(['2308', '请输入密码']),
				'password.min'			 =>json_encode(['2309', '密码最少6位']),
			]
		);

		if($validator->fails()){
			$message = $validator->messages()->first();
			return $this->response(json_decode($message)[0], json_decode($message)[1]);
		}
		$token = MerchantToken::where('token', '=', $request->input('token'))->first();
		$user = Merchant::where('id', '=', $token->merchant_id)->first();
		if(!Hash::check($request->input('old_password'), $user->password)){
			return $this->response(1, '旧密码输入错误');
		}
		$password_hash = Hash::make($request->input('password'));
		$user->password = $password_hash;
		$user->save();
		return $this->response(0, '密码修改成功');
	}

	/** 
		注册新的用户   
	 * @todo  增加邀请码的表
	 */
	public function register(Request $request, BottomService $bottomService)
	{		
		$validator = Validator::make($request->all(), 
			[
				'username'			=>	'required|unique:merchant,username|between:6,12|alpha_num',
				'password'			=>	'required|min:6',
				'phone'		=>	'required|digits:11',
				'captcha'	=>  'required|digits:6',
			],
			[
				'username.required'			=>  json_encode(['1','请填写用户名']),
				'username.unique'			=>  json_encode(['1','该用户名已经被注册']),
				'username.between'			=>  json_encode(['1','请填写6-12位的用户名']),
				'username.alpha_num'		=>  json_encode(['1','用户名只允许 字母 数字']),
				'password.required'			=>	json_encode(['1','请输入密码']),
				'password.min'				=>	json_encode(['1','密码至少六位']),
				'phone.required'	=>	json_encode(['1','请输入手机号码']),
				'phone.digits'		=>	json_encode(['1','请输入正确的手机号码']),
				'captcha.required'=>	json_encode(['1','请输入验证码']),
				'captcha.digits'	=>	json_encode(['1','请输入正确的验证码']),
			]
		);

		if($validator->fails()){
			$message = $validator->messages()->first();
			return $this->response(json_decode($message)[0], json_decode($message)[1]);
		}

		$whiteList = WhiteList::where('content', $request->input('phone'))->whereRaw('type = 1 and is_deleted = 0')->get();
        if(!count($whiteList))
        {
            //验证邀请码
            $inviteCode = InviteCode::where('code', '=', $request->input('invitation_code'))->whereRaw('is_deleted = 0')->first();
            if (is_null($inviteCode)) {
                return $this->response(1, '邀请码已被删除，请重新申请');
            }
            if ($inviteCode->merchant_id) {
                return $this->response(1, '邀请码已被占用，请重新申请');
            }
        }


		$captchaJson = $bottomService->getCaptchaFromAries($request->input('phone'));
		Log::info($captchaJson);
		if (is_null($captchaJson)) {
			return $this->response(1, '系统异常，请稍候重试');
		}
		$captcha = json_decode($captchaJson, true);
		Log::info($captcha);
		if (is_null($captcha) || $captcha['code']) {
			return $this->response(1, '系统异常，请稍候重试');
		}
		if(is_null($captcha['data']) || $request->input('captcha') != $captcha['data']['code']){
			return $this->response(1, '错误的验证码 请重新输入');
		}

		//新建一个新商户 TODO
		$user = new Merchant;
        $user->username = $request->input('username');
        $user->mobile_phone = $request->input('phone');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        if (!count($whiteList)) {
        	$inviteCode->merchant_id = $user->id;
        	$inviteCode->save();
        }

		//请求登录API
		$request = Request::create('taurus/v1/user/login', 'POST', $request->all());
		//请求URL
		$response = Route::dispatch($request)->getContent();

		return $response;
	}

	/**
	   用户登录
	 * @param username
	 * @param password
	 * @return json
	 */
	public function login(Request $request, RestaurantService $restaurantService)
	{
		$validator = Validator::make($request->all(),
			[
				'username'		=>  'required|between:6,12|alpha_num|exists:merchant,username',
				'password'		=>  'required|min:6',
			],
			[
				'username.required'	=>json_encode(['1', '请填写用户名']),
				'username.between'		  	=>json_encode(['1', '请输入正确的用户名']),
				'username.alpha_num'	 	=>json_encode(['1', '请输入正确的用户名']),
				'username.exists'		 	=>json_encode(['1', '用户名不存在']),

				'password.required'		 =>json_encode(['1', '请输入密码']),
				'password.min'			 =>json_encode(['1', '请输入正确的密码']),
			]
		);
		if($validator->fails()){
			$message = $validator->messages()->first();
			return $this->response(json_decode($message)[0], json_decode($message)[1]);
		}

		$merchant = Merchant::where('username', '=', $request->input('username'))->whereRaw('is_deleted = 0')->first();
		if (is_null($merchant)) {
			return $this->response(1, '当前用户不存在，请重新注册');
		}
		if(Hash::check($request->input('password'), $merchant->password)){
			//登录成功
			if (Hash::needsRehash($merchant->password))
			{
			    $password_hash = Hash::make($request->input('password'));
			    $merchant->password = $password_hash;
			    $merchant->save();
			}

			$clientType = $request->input('client_type', Config::get('code.client_type.MERCHANT'));

			$token = MerchantToken::where('merchant_id', $merchant->id)->where('device_hash', $request->input('device_id'))->whereRaw('is_deleted = 0')->first();
			if (is_null($token)) {
				$token = new MerchantToken;
				$token->platform = $request->input('platform');
				$token->device_hash = $request->input('device_id');
				//生成token
				for ($i=0; $i < 5; $i++){

					$tokenStr = $this->storeGenerateToken($merchant->username, $merchant->id, $token->device_hash);
					if(is_null(MerchantToken::where('token', '=', $tokenStr)->whereRaw('is_deleted = 0')->first())){
						break;
					}
					if( $i == 4 ){
						return $this->response(1, '系统错误 请尝试重新登录');
					}
				};
				//更新token字段
				$token->token = $tokenStr;
				$token->merchant()->associate($merchant);

				if ($clientType == Config::get('code.client_type.MERCHANT')) {
					$token->login_client = 1;
				}
				if ($clientType == Config::get('code.client_type.POS')) {
					$token->login_client = 2;
				}
			} else {
				Log::info('已存在对应的token，重复使用token:'.$token->id.', token:'.$token->token);

			}

			if ($clientType == Config::get('code.client_type.MERCHANT')) {
				$token->login_client |= 1;
			}
			if ($clientType == Config::get('code.client_type.POS')) {
				$token->login_client |= 2;
			}

			//找到了默认店铺
			$restaurant = $token->restaurant;
			if(!is_null($restaurant) && !$restaurant->is_deleted){
				//找到了默认店铺
			} else{
				//查找其他的店铺
				$restaurant = Restaurant::where('merchant_id', $merchant->id)->whereRaw('is_deleted = 0')->first();
				if(!is_null($restaurant)){
					$token->restaurant_id = $restaurant->id;
				}
			}

			$result = null;
			if (!is_null($restaurant)) {
				$result = $restaurantService->getResult($request, $restaurant);

				if ($clientType == Config::get('code.client_type.POS')) {//取票机异地登录检测
					Log::info('取票机异地登录检测');
					$tokens = MerchantToken::where('restaurant_id', $token->restaurant_id)->where('id', '!=', (isset($token->id) ? $token->id : 0))->whereRaw('is_deleted = 0')->get();
					// Log::info($tokens);
					foreach ($tokens as $key => $value) {
						if (($value->login_client & 2) == 2) {
							Log::info('取票机已在其他设备上登录，请先退出登录再在当前设备上进行登录操作');
							return $this->response(2, '取票机已在其他设备上登录，请先退出登录再在当前设备上进行登录操作');
						}
					}
				}
			} elseif ($clientType == Config::get('code.client_type.POS')) {
				Log::info('取票机登录时，未找到当前账号对应的店铺');
				return $this->response(3, '未找到当前账号对应的店铺，请在商户端或其他渠道建立店铺后再重新登录取票机');
			}

			$token->save();

			return $this->response(0, '', ['token'=>$token->token,'id'=>$merchant->id,'restaurant'=> $result]);			
		}
		return $this->response(1, "密码错误\n如果忘记密码请找回密码");	
	}

	/**
	* 生成随机token
	* @param 用户名
	* @param 用户id
	* @param 设备id
	* @return string
	*/
	function storeGenerateToken($user_name,$user_id,$device_id){
		$timestamp = time();
		$nonce     = str_random(16);
		$tmpArr = array($user_name, $user_id, $device_id, $timestamp, $nonce);
        $tmpStr = implode($tmpArr);
        return hash("sha256", $tmpStr, false);
	}

	/**
	   用户登出
	 *
	 * @param user_id
	 * @param device_id
	 * @return json
	 */
	public function logout(Request $request)
	{

		$token = MerchantToken::where('merchant_id','=',$request->input('user_id'))->where('device_hash', '=', $request->input('device_id'))->first();
		Log::info('logout token:'.$token);
		$clientType = $request->input('client_type', Config::get('code.client_type.MERCHANT'));
		if ($clientType == Config::get('code.client_type.MERCHANT')) {
			if (($token->login_client & 1) == 1) {
				$token->login_client -= 1;
			}
			
		}
		if ($clientType == Config::get('code.client_type.POS')) {
			if (($token->login_client & 2) == 2) {
				$token->login_client -= 2;
			}
		}
		if ($token->login_client == 0) {
			$token->delete();
		}
		return $this->response(0, '登出成功');
	}

}
