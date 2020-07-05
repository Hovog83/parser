<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Praduct;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cat = Category::get();
        $praduct = Praduct::with('brand');
        if(!empty($request->cat)){
            $praduct->where("cat_id",$request->cat);
        }
        $praduct = $praduct->paginate(15);
        return view('index',compact('cat','praduct'));
    }
    
}
