<?php

namespace App\Models;

use App\Models\ShippingAddress;
use App\Models\Traits\UserAdmin;
use App\Models\Traits\UserRoles;
use App\Models\Traits\UserHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use URL;
use App\Models\Follower;


class User extends Authenticatable implements MustVerifyEmail,JWTSubject
{
    use HasFactory, Notifiable, UserHelper, UserRoles, UserAdmin;
    //use HasFactory, Notifiable, SoftDeletes, UserHelper, UserRoles, UserAdmin;

    //protected $appends = ['fullname'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'firstname',
        'lastname',
        'username',
        'address',
        'categories',
        'gender',
        'user_type',
        'status',
        'cellphone',
        'image',
        'born_at',
        'logged_in_as',
        'security_level',
        'session_token',
        'logged_in_at',
        'disabled_at',
        'email_otp',
        'mobile_otp',
        'verify_email_time',
        'verify_email_resend_time',
        'verify_mobile_time',
        'verify_mobile_resend_time',
        'dial_code',
        'country_code',
        'is_online',
        'phone',
        'google_token',
        'facebook_token',
        'apple_token',
        'is_email_verified',
        'profile_complele',
        'push_notification',
        'is_mobile_verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_by',
        'deleted_at',
        'logged_in_at',
        'disabled_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at'        => 'datetime',
        'logged_in_at'      => 'datetime',
        'activated_at'      => 'datetime',
    ];

    static public $messages = [];

    /**
     * Validation rules for this model
     */
    static public $rules = [
        'fullname' => ['required', 'string', 'max:191'],
        'username'  => ['required', 'string', 'max:191','unique:users'],
        //'gender'    => 'required|in:male,female',
        'cellphone' => ['nullable', 'numeric', 'max:12'],
        'email'     => ['required', 'string', 'email', 'max:191', 'unique:users'],
        'password'  => ['required', 'string', 'min:4', 'confirmed'],
        //'token'     => 'required|exists:user_invites,token',
        //'photo'     => 'required|max:6000|mimes:jpg,jpeg,png,bmp',
    ];

    /**
     * Validation rules for this model
     */
    static public $rulesClient = [
        'fullname'      =>   ['required', 'string', 'max:191'],
        'username'      =>   ['required', 'string', 'max:191','unique:users'],
        //'gender'    => 'required|in:male,female',
        'user_type'     =>    ['required'],
        'cellphone'     => ['nullable', 'numeric', 'max:191'],
        'email'         => ['required', 'string', 'email', 'max:191', 'unique:users'],
        'password'      => ['required', 'string', 'min:4', 'confirmed'],
        //'roles'     => ['required', 'array'],
    ];

    /**
     * Validation rules for this model
     */
    static public $rulesProfile = [
        'fullname'      =>   'required',
        'gender'        =>   'required|in:male,female',
        'cellphone'     =>   'nullable|numeric|min:9',
        'password'      =>   'nullable|min:4|confirmed',
        'photo'         =>   'required|max:6000|mimes:jpg,jpeg,png,bmp',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Get the shippingAddress
     */
    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'user_id', 'id')->whereNull('transaction_id');
    }

    /**
     * Get the top Entrepreneurs
     */
    public function feeds()
    {
        return $this->hasMany(Feed::class, 'user_id', 'id');
    }

    /**
     * Get the top Entrepreneurs
     */
    public function interest()
    {
        return $this->hasMany(UserInterest::class, 'user_id', 'id');
    }

    /**
     * Get the top Entrepreneurs
     */
    public function isFollowings()
    {
        return $this->hasMany(Follower::class, 'following_id', 'id')->where('follower_id',auth('api')->user()->id);
        
    }

     /**
     * Get the top Entrepreneurs
     */
    public function isFollower()
    {
        return $this->hasMany(Follower::class, 'follower_id', 'id')->where('following_id',auth('api')->user()->id);
        
    }


    /**
     * Get the top Entrepreneurs
     */
    public function myFollowing()
    {
        return $this->hasMany(Follower::class, 'follower_id', 'id');
        
    }

    /**
     * Get the top Entrepreneurs
     */
    public function Myfollwer()
    {
        return $this->hasMany(Follower::class, 'following_id', 'id');
        
    }

      /**
     * Get the top Entrepreneurs
     */
    public function totalPost()
    {
        return $this->hasMany(PostVideo::class, 'user_id', 'id')->where('is_delete',0);
        
    }
    
    
    //      /**
    //  * Get the top Entrepreneurs
    //  */
    // public function blockByMe()
    // {
    //     return $this->hasMany(Block::class,'block_by_id','id');
    // }

    //        /**
    //  * Get the top Entrepreneurs
    //  */
    // public function blockBYOther()
    // {
    //     return $this->hasMany(Block::class,'block_to_id','id');
    // }



    /**
     * Get the top Entrepreneurs
     */
    public function otherUserFollowing()
    {
        $data = Follower::where('follower_id',auth('api')->user()->id)->select('following_id')->get()->toArray();
        $ids =  array_column($data, 'following_id');
        return $this->hasMany(Follower::class, 'follower_id', 'id')->where('following_id','!=',auth('api')->user()->id)->whereNotIn('following_id', $ids);
        
    }


    public function userBlockList(){
        return $this->hasMany(Block::class,'block_by_id', 'id')->where('block_by_id',auth('api')->user()->id);
    }

    public function getFullNameAttribute() {
        return ucfirst($this->firstname) . ' ' . ucfirst($this->lastname);
        }
 
    /**
     * Get the post title.
     *
     * @param  string  $value
     * @return string
     */
    public function getImageAttribute($value)
    {
        if($value != null){
            return URL::to('/') . '/uploads/profile-image/' .$value ;
        }else{
            return $value ;
        }

    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }



}
