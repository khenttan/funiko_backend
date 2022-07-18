<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    protected $table = 'tags';
    protected $guarded = ['id'];

   /**
     * Get the status
     */
    public function video()
    {
        return $this->belongsTo(PostVideo::class, 'post_video_id', 'id')->where('is_delete',0);
    }

    
}
