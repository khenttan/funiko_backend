<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use URL;

class Advertisment extends Model
{
    use HasFactory;
    protected $table = 'advertisements';
    protected $guarded = ['id'];

    /**
     * Validation rules for this model
     */
    static public $rules = [
        'title'       => 'required|min:3|max:191',
        'description' => 'nullable|max:191',
        'amount'      => 'nullable|max:191',
        'action_url'  => 'nullable|max:191',
        'active_from' => 'nullable|date',
        'active_to'   => 'nullable|date',
    ];
      /**
     * Get the post title.
     *
     * @param  string  $value
     * @return string
     */
    public function getVideoAttribute($value)
    {
        return URL::to('/') . '/storage/videos/' .$value ;
    }

    public function getIsAdvertismentAttribute()
    {
        return 1;
    }

}
