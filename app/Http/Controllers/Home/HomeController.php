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
    	return view('home.home')->with('id',1);
    }

}