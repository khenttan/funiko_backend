<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportList extends Model
{
    use HasFactory;
    protected $table = 'report_lists';
    protected $guarded = ['id'];
    /**
     * Validation rules for this model
     */
    static public $rules = [
        'title'    => 'required|min:3|max:191',
    ];
}
