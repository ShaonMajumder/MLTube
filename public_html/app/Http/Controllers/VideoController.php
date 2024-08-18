<?php

namespace App\Http\Controllers;

use App\Http\Requests\Videos\UpdateVideoRequest;
use App\Models\History;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    public function show(Video $video){
        if(request()->wantsJson()){
            return $video;
        }

        $tags = null;
        $related_videos = null;
        if($video->ml_tags){
            $data = json_decode($video->ml_tags, true);
            $tags = implode(', ', array_keys($data));
            $related_videos = $this->getRelatedVideos($video);
        }
        
        //dd($video->comments->first()->replies);
        return view('video', compact('video', 'tags', 'related_videos'));
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

    public function getRelatedVideos(Video $video)
    {
        // Decode the ml_tags JSON column to get the tags as an array
        $tags = array_keys(json_decode($video->ml_tags, true));

        // Find related videos by searching for any of these tags
        return Video::where('id', '!=', $video->id)
                    ->where(function($query) use ($tags) {
                        foreach ($tags as $tag) {
                            $query->orWhere(DB::raw("JSON_EXTRACT(ml_tags, '$.\"$tag\"')"), '!=', 'null');
                        }
                    })
                    ->get();
    }
}