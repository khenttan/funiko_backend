<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follower extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function follower(){
        return $this->belongsTo(User::class,'follower_id', 'id');
    }

    public function following(){
        return $this->belongsTo(User::class,'following_id', 'id');
    }

    public function isBlockByMe(){
        return $this->belongsTo(Block::class,'follower_id', 'block_to_id')->where('block_by_id',auth('api')->id());
    }

    public function isBlockToMe(){
        return $this->belongsTo(Block::class,'follower_id', 'block_by_id')->where('block_to_id',auth('api')->id());
    }

    public function isFollowingBlockByMe(){
        return $this->belongsTo(Block::class,'following_id', 'block_to_id')->where('block_by_id',auth('api')->id());
    }
    
    public function isFollowingBlockToMe(){
        return $this->belongsTo(Block::class,'following_id', 'block_by_id')->where('block_to_id',auth('api')->id());
    }

    // public function isFollowByME(){
    //     return $this->belongsTo(User::class,'follower_id', 'id');

    // }

    
    public function isFollowedByAuthUser() {
        return $this->belongsTo(self::class,'follower_id', 'following_id')
                    ->where('follower_id',auth('api')->id());
    }

    public function isFollowedByUser() {
        return $this->belongsTo(self::class,'follower_id', 'following_id');
    }

    public static function is_followed($following_id){
        return Follower::where([
            'follower_id' => auth('api')->id(),
            'following_id' => $following_id
        ])->exists() ? 1 : 0;
    }
}
