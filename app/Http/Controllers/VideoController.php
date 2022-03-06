<?php

namespace App\Http\Controllers;

use App\Http\Requests\Videos\UpdateVideoRequest;
use App\Models\History;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show(Video $video){
        if(request()->wantsJson()){
            return $video;
        }
        
        //dd($video->comments->first()->replies);
        return view('video', compact('video'));
    }

    public function updateViews(Video $video,History $history){
        $video->increment('views');

        $history = new History;
        if( auth()->check() ){
            $history->user_id = auth()->user()->id;
        }else{
            $history->user_id = ''; //guest_session_id
        }
        
        $history->name = '';
        $history->type = 'search';
        $history->save();


        return response()->json([]);
    }

    public function update(UpdateVideoRequest $request,  Video $video){
        $video->update($request->only( ['title','description'] ));
        return redirect()->back();
    }
}