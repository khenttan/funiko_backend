<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'post_likes';

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function videos(){
        return $this->belongsTo(PostVideo::class, 'post_video_id', 'id')->where('is_delete',0);
    }
}



