<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use URL;

class Group extends Model
{
    use HasFactory;
    protected $table = 'groups';

    protected $guarded = ['id'];


     /**
     * Get the post title.
     *
     * @param  string  $value
     * @return string
     */
    public function getGroupIconAttribute($value)
    {
        if($value != null){
            return URL::to('/').'/storage/uploads/group_icons/' .$value ;
        }else{
            return $value ;
        }

    }

    public function admin(){
        return $this->belongsTo(User::class,'created_by', 'id');
    }

    public function GroupMember(){
        return $this->hasMany(GroupMember::class,'group_id', 'id');
    }

    public function acceptedGroupMember(){
        return $this->hasMany(GroupMember::class,'group_id', 'id')->where('is_accept',1);
    }

    // public function CountGroupMember(){
    //     return $this->hasMany(GroupMember::class,'group_id', 'id')->count();
    // }
    public function onlyOneGroupChat(){
        return $this->hasOne(Chat::class,'group_id', 'id')->latest('created_at');
    }

    public function userChat(){
        return $this->hasMany(Chat::class,'group_id', 'id')->latest('created_at');
    }


}
