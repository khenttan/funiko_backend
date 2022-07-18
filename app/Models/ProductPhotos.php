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
class ProductPhotos extends AdminModel
{

    use SoftDeletes, ActiveTrait, ImageThumb;

    protected $table = 'product_photos';

    protected $guarded = ['id'];



}
