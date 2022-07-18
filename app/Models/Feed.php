<?php

namespace App\Models;

use App\Models\Traits\Documentable;
use App\Models\Traits\Photoable;
use App\Models\Traits\Videoable;
use Bpocallaghan\Sluggable\HasSlug;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feed extends AdminModel
{

    use SoftDeletes, HasSlug, Videoable;
    
    protected $table = 'feeds';

    protected $guarded = ['id'];


    /**
     * Validation custom messages for this model
     */
    static public $rules = [
        'user_id'               =>    'required',
        'title'                 =>    'required|min:2|max:191',
        'email'                 =>    'required|min:2|max:191|email',
        'description'           =>    'required|min:2|max:1000',
        'slug'                  =>    'nullable',
    ];

    public function productImages()
    {
        return $this->hasMany( Photo::class,'photoable_id','id');
    }

    public function productVideos()
    {
        return $this->hasMany( Video::class,'videoable_id','id');
    }

    public function likes()
    {
        return $this->hasMany(  Like::class,'feed_id','id');
    }

    public function comments()
    {
        return $this->hasMany( Comment::class,'feed_id','id');
    }

    


    /**
     * Get all the rows as an array (ready for dropdowns)
     *
     * @return array
     */
    public static function getAllList(): array
    {
        return self::orderBy('id')->select('id','user_id','title','description','link')->get()->toArray();
    }
}










