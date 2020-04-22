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

    public function edit_tag(Request $request)
    {
        // validate 
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required',
        ]);
        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName,
        ]);
    }

    public function delete_tag(Request $request)
    {
        // validate
        $this->validate($request, [
            'id' => 'required'
        ]);
        return Tag::where('id', $request->id)->delete();
    }

    public function getTags()
    {
        return Tag::orderBy('id', 'desc')->get();
    }

    //
    public function upload(Request $request)
    {
        $picName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }
}
