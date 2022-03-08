<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Components\Message;
use App\Http\Requests\Api\V1\Videos\UpdateVideoRequest;
use App\Models\History;
use App\Models\Video;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    use Message;

    public function show(Video $video){
        if(request()->wantsJson()){
            return $video;
        }
        
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

    /**
     * Edit Video Information
     *
     * @param Request $request
     * @param Video $video
     * @return void
     */
    public function update(Request $request,  Video $video){
        $video->update($request->only( ['title','description'] ));
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, 'Video Information Updated !');
    }
}