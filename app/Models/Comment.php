<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Client
 * @mixin Builder
 */
class Comment extends Model
{

    protected $table = 'comments';

    protected $guarded = ['id'];

    // protected $dates = ['approved_at'];

    /**
     * Validation rules for this model
     */
    static public $rules = [
        'post_video_id'          =>   'required',
        'user_id'          =>   'required',
        'comment_text'          =>   'required|min:5|max:5000',
     ];


    //  public function threads()
    // {
    //     return $this->hasMany( CommentThread::class,'comment_id','id');
    // }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function parent(){
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }


       /**
     * Get the top Entrepreneurs
     */
    public function is_like()
    {
        return $this->hasMany(CommentLike::class,'comment_id','id')->where('user_id',auth('api')->user()->id);
    }


      /**
     * Get the top Entrepreneurs
     */
    public function commentLike()
    {
        return $this->hasMany(CommentLike::class,'comment_id','id');
    }

    // public function child(){
    //     return $this->hasMany(Comment::class, 'parent_id', 'id');
    // }

    


}
