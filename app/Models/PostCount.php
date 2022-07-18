<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCount extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'post_counts';
    
    public function videos(){
        return $this->belongsTo(PostVideo::class, 'post_video_id', 'id')->where('is_delete',0);
    }
}
