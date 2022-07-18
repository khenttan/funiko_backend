<?php

namespace App\Models;

use Bpocallaghan\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductCondition
 * @mixin \Eloquent
 */
class StoreFeature extends AdminModel
{
    use SoftDeletes;

    protected $table = 'attribute_type';

    protected $guarded = ['id'];
    protected $visible = ['id', 'name','subfeatures'];
    /**
     * Validation rules for this model
     */
    static public $rules = [
        'name'      =>      'required|unique:attribute_type,name,NULL,id,deleted_at,NULL|min:3|max:191',
    ];


    public function subfeatures()
    {
        return $this->hasMany('App\Models\ProductFeature','type_id','id');
    }

    /**
     * Get all the rows as an array (ready for dropdowns)
     *
     * @param bool $addAll
     * @return array
     */
    public static function getAllList($addAll = false)
    {
        $items = self::select('name', 'id')->get();
        if ($addAll) {
            $items->push((object) ['id' => 'all', 'name' => 'All']);
        }
        //$items = $items->sortBy('name')->pluck('name', 'id')->toArray();

        return $items;
    }

}
