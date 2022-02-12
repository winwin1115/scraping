<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Urls;

class UrlsController extends Controller
{
    public function home()
    {
        return redirect()->route('data.urls');
    }

    public function index()
    {
        $product_info = Urls::all();
        return view('admin.urls')->with(['product_info' => $product_info]);
    }

    public function addProduct(Request $request)
    {
        $site_type = $request->site_type;
        $site_url = $request->site_url;

        $urls = new Urls;
        $urls->site_type = $site_type;
        $urls->site_url = $site_url;

        if($urls->save())
            return response()->json(['status' => '200']);
        else
            return response()->json(['status' => '500']);
    }
}
