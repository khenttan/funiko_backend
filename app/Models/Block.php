<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;
    protected $table = 'blocks';
    protected $guarded = ['id'];

    public function blockList(){
        return $this->belongsTo(User::class,'block_to_id', 'id');
    }


}
