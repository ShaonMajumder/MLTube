<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Video $video){
        return $video->comments()->paginate(5);
    }
    public function show(Comment $comment){
        return $comment->replies()->paginate(10);
    }
    public function store(Request $request, Video $video){
        return auth()->user()->comments()->create([
            'body' => $request->body,
            'video_id' => $video->id,
            'comment_id' => $request->comment_id
        ])->fresh();
        //user()
    }
}
