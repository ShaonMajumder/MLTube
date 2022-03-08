<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Videos\UpdateVideoRequest;
use App\Http\Resources\Api\V1\VideoResource;
use App\Models\History;
use App\Models\Video;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends Controller
{
    public function show(Video $video){
        $this->apiSuccess();
        $this->data['video'] = new VideoResource($video);
        return $this->apiOutput(Response::HTTP_OK, "Video Views Updated !");
    }

    /**
     * Update Views of a specific Video
     *
     * @param Request $request
     * @param Video $video
     * @param History $history
     * @return void
     */
    public function updateViews(Request $request, Video $video,History $history){
        $video->increment('views');

        $history = new History;
        if( !empty($request->user()) ){
            $history->user_id = auth()->user()->id;
        }else{
            $history->user_id = ''; //guest_session_id
        }
        
        $history->name = '';
        $history->type = 'search';
        $history->save();
        $views = $video->views;

        $this->apiSuccess();
        $this->data =  [ "current_views" => $views ];
        return $this->apiOutput(Response::HTTP_OK, "Video Views Updated !");
    }

    /**
     * Edit Video Information
     *
     * @param Request $request
     * @param Video $video
     * @return void
     */
    public function update(UpdateVideoRequest $request,  Video $video){
        $video->update($request->only( ['title','description'] ));
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, 'Video Information Updated !');
    }
}