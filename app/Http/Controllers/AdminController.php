<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function createTag(Request $request)
    {
        // validate 
        $this->validate($request, [
            'tagName' => 'required'
        ]);
        return Tag::create([
            'tagName' => $request->tagName,
        ]);
    }

    public function getTags()
    {
        return Tag::orderBy('id', 'desc')->get();
    }
}
