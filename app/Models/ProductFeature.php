<?php

namespace App\Models;

use Bpocallaghan\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductCondition
 * @mixin \Eloquent
 */
class ProductFeature extends AdminModel
{
    use SoftDeletes, HasSlug;

    protected $table = 'attributes';

    protected $guarded = ['id'];

    protected $visible = ['id', 'name'];
    /**
     * Validation rules for this model
     */
    static public $rules = [
        'name'      => 'required|min:3|max:191',
        'slug'      => 'nullable',
    ];

	/**
	 * Get the Product many to many
	 */
	public function products()
	{
		return $this->belongsToMany(Product::class);
	}

    /**
     * Get all the rows as an array (ready for dropdowns)
     *
     * @param bool $addAll
     * @return array
     */
    public static function getAllList($addAll = false)
    {
        $items = self::all();
        if ($addAll) {
            $items->push((object) ['id' => 'all', 'name' => 'All']);
        }

        $items = $items->sortBy('name')->pluck('name', 'id')->toArray();

        return $items;
    }
}
