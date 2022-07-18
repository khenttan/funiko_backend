<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Traits\ImageThumb;
use App\Models\Traits\ActiveTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Stores
 * @mixin Model
 */
class Stores extends AdminModel
{
    use SoftDeletes, ActiveTrait, ImageThumb;

    protected $table = 'seller_store';

    protected $guarded = ['id'];

    public static $LARGE_SIZE = [1920, 600];

    public static $THUMB_SIZE = [640, 200];

    public static $IMAGE_SIZE = ['o' => [1920, 600], 'tn' => [640, 200]];

    /**
     * Validation rules for this model
     */
    static public $rules = [
        'shop_name'             =>       'required|min:3|max:191',
        'shop_description'      =>       'required|max:191',
        'shop_address'          =>       'required|max:500',
        'shop_mobile'           =>       'nullable|max:191',
        'shop_photo'            =>       'nullable|max:6000|mimes:jpg,jpeg,png,bmp',
    ];


}
