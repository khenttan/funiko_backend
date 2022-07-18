<?php
namespace App\libraries;

use Illuminate\Http\UploadedFile;

use Intervention\Image\Facades\Image;

use App\Models\User;

use App\Models\Banner;

use App\Model\Product;

use App\Model\SmsText;

use App\Model\SmsLog;

use App\Model\Attribute;

use App\Model\Media;

use App\Models\Country;

use App\Models\State;

use App\Models\City;


use App\Models\ProductCategory;

use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Request;

use HTML,Str,Config,DB,Session,Mail,mongoDate;


/**

 * Custom Helper

 *

 * Add your methods in the class below

 */

class CustomHelper { 


	/*

     * Function to save notification

     *

     * @param $tyep as

     *

     * @return null

     * */

    public static function getUserDetails($userId=''){

    	if(isset($userId) && $userId ==	''){

			return array('status'=>"error",'data'=>[]);

		}

		$userData =	User::where('id',$userId)->get()->toArray();
		if($userData){
            foreach ($userData as $key	=>	$value) {
              //  json_decode()
                $address    =       json_decode($value['address']);
                $categories    =       json_decode($value['categories']);
                $country    =       self::get_country_name($address[0]);
                $state      =       self::get_state_name($address[1]);
                $city       =       self::get_city_name($address[2]);
                $category   =        self::get_category_name($categories[0]);
                $subcategory =       self::get_category_name($categories[1]);   


               // dd($category,$subcategory);
                if (isset($value['image']) && !empty($value['image']) && file_exists( config('globalConstant.shop_photo_path').$value['image'])) {
                        $userData[$key]['image']		=	 config('globalConstant.image_path').$value['image'];
                        $userData[$key]['country']      =   $country??'';
                        $userData[$key]['state']        =   $state??'';
                        $userData[$key]['city']         =   $city??'';
                        $userData[$key]['category']     =   $category??'';
                        $userData[$key]['subcategory']  =   $subcategory??'';
                }
                else{
                    $value['image']		= '';	 
                }
            }
        }

		if(!empty($userData)){
			$response['status']	=	"success";
			$response['user_data']	=	$userData[0];
			return $response;
		}else{
            return array('status'=>"error",'data'=>[]);
		}

	}//end saveAdminNotificationActivity()


    /**
     * CustomHelper::get_country_name()
     * @Description Function  to get country name
     * @param $country_id
     * @return $country_name
     * */
    public static function get_country_name($country_id = null) {
        $country = Country::where('id', $country_id)->select('id','name')->first();
       
        return $country;
    }//get_country_name()
    
    /**
     * CustomHelper::get_state_name()
     * @Description Function  to get state name
     * @param $state_id
     * @return $state_name
     * */
    public static function get_state_name($state_id = null){
        $state = State::where('id', $state_id)->select('id','name')->first();
        return $state;
    }//get_state_name()
    
    /**
     * CustomHelper::get_city_name()
     * @Description Function  to get city name
     * @param $city_id
     * @return $city_name
     * */
    public static function get_city_name($city_id = null){
        $city = City::where('id', $city_id)->select('id','name')->first();
        return $city;
    }//get_city_name()

    /**
     * CustomHelper::get_category_name()
     * @Description Function  to get category name
     * @param $category_id
     * @return $category_name
     * */
    public static function get_category_name($category_id = null){
        $category = ProductCategory::where('id', $category_id)->select('id','name')->first();
        return $category;
    }//end get_category_name()

    /**
     * CustomHelper::get_category_name()
     * @Description Function  to get category name
     * @param $category_id
     * @return $category_name
     * */
    public static function getFullName($user_id = null){
        $name = User::where('id', $user_id)->select('firstname','lastname')->first();
        return $name['firstname'].$name['lastname'];
    }//end get_category_name()
    
    public static function getUserImage($user_id = null){
        $name = User::where('id', $user_id)->select('image')->first();
        return $name['image'];
    }




} // end CustomHelper

