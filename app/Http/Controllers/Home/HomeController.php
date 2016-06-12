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

class HomeController extends BaseController
{
    public function index(Request $request)
    {
    	Log::info("has request");
    	$tab=$request['tab'];
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
    }

}