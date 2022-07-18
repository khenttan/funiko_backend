<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Client
 * @mixin Builder
 */
class Like extends AdminModel
{

    protected $table = 'likes';
    
     protected $fillable = ['user_id','feed_id','is_like'];


}
