<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLike extends Model
{
    use HasFactory;

    use HasFactory;

    
    protected $table = 'chat_likes';

    protected $guarded = ['id'];

    //  public function isLikeByMe(){
    //     return $this->belongsTo(User::class,'user_id','id');
    // }    

}
