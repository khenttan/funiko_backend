<?php

namespace App\Models;

use App\Models\Traits\ImageThumb;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Photo
 * @mixin \Eloquent
 */
class Video extends AdminModel
{
    use SoftDeletes, ImageThumb;

    protected $table = 'videos';

    protected $guarded = ['id'];

    public static $LARGE_SIZE = [1024, 593];

    public static $THUMB_SIZE = [800, 450];

    static public $rules = [
        'name'           => 'nullable',
        'link'           => 'nullable',
        'filename'       => 'nullable',
        'content'        => 'nullable',
        'photo'          => 'nullable|max:6000|mimes:jpg,jpeg,png,bmp',
        'file'           => 'nullable|max:5000|mimes:mpeg,ogg,mp4,avi',
        'is_cover'       => 'nullable',
       
    ];

    protected $appends = ['thumb','url'];

    public function videoable()
    {
        return $this->morphTo();
    }

    /**
     * Get the url to the video
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->urlForName($this->filename);
    }

    /**
     * Get the url for the file name (specify thumb, default, original)
     * @param $name
     * @return string
     */
    public function urlForName($name)
    {
        return config('app.url') . 'uploads/videos/' . $name;
    }

    /**
     * Get all the rows as an array (ready for dropdowns)
     *
     * @return array
     */
    public static function getAllList()
    {
    	return self::with('videoable')->orderBy('name')->get()->pluck('name', 'id')->toArray();
    }

       /**
     * Get the thumb path (append -tn at the end)
     * @return mixed
     */
    public function getThumbAttribute()
    {
        return $this->appendBeforeExtension(self::$thumbAppend);
    }

    /**
     * Get the thumb path (append -tn at the end)
     * @return mixed
     * original is reserved (original modal data)
     */
    public function getOriginalFilenameAttribute()
    {
        return $this->appendBeforeExtension(self::$originalAppend);
    }
     /**
     * Apends a string before the extension
     * @param $append
     * @return mixed
     */
    private function appendBeforeExtension($append)
    {
        return substr_replace($this->filename, $append, strpos($this->filename, '.'), 0);
    }



}
