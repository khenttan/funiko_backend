<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    
    use HasFactory;

    protected $table = 'user_interests';
    protected $guarded = ['id'];


    /**
     * Get the top Entrepreneurs
     */
    public function interestCategory()
    {
        return $this->belongsTo(ProductCategory::class,'interest_id','id');
    }
    

}
