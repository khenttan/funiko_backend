<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use URL;
class PostVideo extends Model
{
    use HasFactory;
    protected $table = 'post_videos';
    protected $guarded = ['id'];


    /**
     * Get the top Entrepreneurs
     */
    public function tags()
    {
        return $this->hasMany(Tag::class,'post_video_id','id');
    }
    
    /**
     * Get the top Entrepreneurs
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

       /**
     * Get the top Entrepreneurs
     */
    public function blockByMe()
    {
        return $this->belongsTo(Block::class,'user_id','block_by_id');
    }

           /**
     * Get the top Entrepreneurs
     */
    public function blockBYOther()
    {
        return $this->belongsTo(Block::class,'user_id','block_to_id');
    }

      /**
     * Get the post title.
     *
     * @param  string  $value
     * @return string
     */
    public function getVideoAttribute($value)
    {
        return URL::to('/') . '/api/video_stream/' .$value ;
    }

         /**
     * Get the post title.
     *
     * @param  string  $value
     * @return string
     */

    public function getThumbnailImageAttribute($value)
    {
        return URL::to('/') . '/uploads/image_thumbnail/' .$value ;
    }
    
    /**
     * Get the top Entrepreneurs
     */
    public function postLike()
    {
        return $this->hasMany(PostLike::class,'post_video_id','id');
    }

      /**
     * Get the top Entrepreneurs
     */
    public function postView()
    {
        return $this->hasMany(PostCount::class,'post_video_id','id');
    }



         /**
     * Get the top Entrepreneurs
     */
    public function postViewOne()
    {
        return $this->hasOne(PostCount::class,'post_video_id','id');
    }



    /**
     * Get the top Entrepreneurs
     */
    public function is_like()
    {
        return $this->hasMany(PostLike::class,'post_video_id','id')->where('user_id',auth('api')->user()->id);
    }
    
    /**
     * Get the top Entrepreneurs
     */
    public function comments()
    {
        return $this->hasMany(Comment::class,'post_video_id','id');
    }


    /**
     * Get the top Entrepreneurs
     */
    public function postShare()
    {
        return $this->hasMany(PostShare::class,'post_video_id','id');
    }
    
}
