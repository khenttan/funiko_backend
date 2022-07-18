<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use URL;

class Chat extends Model
{
    use HasFactory;
    protected $table = 'chats';
    protected $guarded = ['id'];
   public function likeChat(){
        return $this->hasMany(ChatLike::class,'chat_id','id');
    }    

    public function sendingBy(){
        return $this->belongsTo(User::class,'sender_id','id');
    }    

    public function likeByMe(){
        return $this->hasMany(ChatLike::class,'chat_id','id')->where('user_id',auth()->user()->id);
    }    


    //  /**
    //  * Get the post title.
    //  *
    //  * @param  string  $value
    //  * @return string
    //  */

    // public function getThumbnailImageAttribute($value)
    // {
    //     if($value != null){
    //         return URL::to('/') . '/uploads/image_thumbnail/' .$value ;
    //     }
    // }
}
