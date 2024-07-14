<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model


class Comment extends Model
{
    protected $with = ['user','votes'];
    protected $appends = ['repliesCount'];

    //use HasFactory;
    public function video(){
        return $this->belongsTo(Video::class);
    }

    public function getRepliesCountAttribute(){
        return $this->replies->count();
    }

    public function votes(){
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function replies(){
        return $this->hasMany(Comment::class, 'comment_id')->whereNotNull('comment_id');
    }
}
