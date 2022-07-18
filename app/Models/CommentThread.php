<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Client
 * @mixin Builder
 */
class CommentThread extends AdminModel
{
    use SoftDeletes;

    protected $table = 'comment_threads';

    protected $guarded = ['id'];

    // protected $dates = ['approved_at'];

    /**
     * Validation rules for this model
     */
    static public $rules = [
        'comment_id'       =>   'required',
        'feed_id'          =>   'required',
        'user_id'          =>   'required',
        'comment'          =>   'required|min:5|max:5000',
     ];



}
