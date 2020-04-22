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
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png'
        ]);
        $picName = time() . '.' . $request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }
    public function delete_image(Request $request)
    {
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName);
        return 'done';
    }
    public function deleteFileFromServer($fileName)
    {
        $filePath = public_path() . '/uploads/' . $fileName;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return;
    }
}
