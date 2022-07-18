<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table = 'group_members';

    protected $guarded = ['id'];


    public function groups(){
        return $this->belongsTo(Group::class,'group_id', 'id');
    }


    public function member(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function isMuted(){
        return $this->hasMany(MuteConversation::class,'group_id', 'group_id')->where('deleted_by',auth()->user()->id);
    }


    

}
