<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function index(Video $video){
        $this->apiSuccess();
        $this->data = $video->comments()->paginate(5);
        return $this->apiOutput(Response::HTTP_OK, "Comment from Video Populated !");
        
    }
    public function show(Comment $comment){
        $this->apiSuccess();
        $this->data = $comment->replies()->paginate(10);
        return $this->apiOutput(Response::HTTP_OK, "Replies of Comment Populated !");
    }
    public function store(Request $request, Video $video){
        return $request->user()->comments()->create([
            'body' => $request->body,
            'video_id' => $video->id,
            'comment_id' => $request->comment_id
        ])->fresh();
    }
}
