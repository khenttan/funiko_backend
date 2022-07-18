<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Client
 * @mixin Builder
 */
class DataResponse extends AdminModel
{

    protected $table = 'data_response';

}
