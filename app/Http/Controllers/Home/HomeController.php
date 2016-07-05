<?php

namespace App\Http\Controllers\Home;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Utils\HttpRequest;
use Config;
use Log;
use Session;
use User;

class HomeController extends BaseController
{
    public function index(Request $request)
    {
        return view('main.home');
    	$json = array(
            "id"=>1,
            "name"=>"andrew",
            "phone"=>"13522712205");
        return json_encode($json);
        /*
    	$tab=$request['tab'];
    	Log::info("has request" . $request. " has tab: " + $tab);
    	switch ($tab) {
    		case '1':
    			# code...
    			return view('home.nearby');
    			
    		case '2':
    			return view('home.order');

    		case '3':
    			return view('home.mine');
    		default:
    			# code...
    			break;
    	}
    	return view('home.home')->with('id',1);
        */
    }
    public function guides(Request $request)
    {
        /*
        $validator = Validator::make($request->all(),
            [
                'uid' => 'required',
                'page'=>'integer',
                
            ],
            [
                'search_content.required'       => json_encode(['1', '订单搜索词必须存在']),
                'last_order_id.integer'         => json_encode(['1', '必须是个整数']),
            ]
        );

        if($validator->fails()){
            $message = $validator->messages()->first();
            return $this->response(json_decode($message)[0], json_decode($message)[1]);
        }

        $uid = $request->input('uid');
        $page = $request->input('page');
        $page_record_count = 20;
        $users = User::where('id','>',$page * $page_record_count)->take($page_record_count)->get();
        if(is_null($users)){
            $result = array(
                "result"=>"1",
                "message"=>"empty result",

                );
            return json_encode($result);
        }

        $data = [];
        foreach ($users as $key => $value) {
            $data.append(new array(
                'name'=>$value->name,
                'desc'=>$value->desc,
                'location'=>$value->location,
                'photo'=>$value->photo
                ));
        }
        $result = array(
                "result"=>"1",
                "message"=>"empty result",
                "data" =>$data
                );
    */
    }

}