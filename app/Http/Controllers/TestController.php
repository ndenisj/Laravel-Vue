<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function controllerMethod()
    {
        return view('welcome');
    }

    public function testPost()
    {
        return response()->json([
            'msg' => 'It is working well'
        ], 200);
    }
}
