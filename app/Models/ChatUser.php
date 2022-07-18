<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    use HasFactory;
    protected $table = 'chat_users';

    protected $guarded = ['id'];

    public function receiver(){

        return $this->belongsTo(User::class,'receiver_id', 'id');
    }

    public function sender(){

        return $this->belongsTo(User::class,'sender_id', 'id');
    }

    public function onlyOneChat(){
        return $this->hasOne(Chat::class,'chat_id', 'id')->latest('created_at');
    }

    public function isMuted(){
        return $this->hasMany(MuteConversation::class,'chat_id', 'id')->where('deleted_by',auth()->user()->id);
    }

}
