<?php

namespace App\Models;

use Bpocallaghan\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductCondition
 * @mixin \Eloquent
 */
class SubFeature extends AdminModel
{
    use SoftDeletes;

    protected $table = 'attributes';

    protected $guarded = ['id'];


    /**
     * Validation rules for this model
     */
    static public $rules = [
        'name'      => 'required|min:3|max:191|unique:attributes,name,NULL,id,deleted_at,NULL',
        'slug'      => 'nullable',
    ];


}
