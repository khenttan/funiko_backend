<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\LoginValidationHelper;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use App\Models\ProductCategory;
use App\Models\State;
use App\Models\UserInterest;
use App\Models\FAQ;
use App\Models\Content;
use App\Models\Follower;
use App\Models\Block;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use HTML, Config, Blade, Cookie, DB, File, Hash, Redirect,Response, Session, URL, Validator,JWTAuth;
use Illuminate\Routing\Controller as BaseController;

class AuthApiController extends Controller {

    //    /**
    //  * Create a new AuthController instance.
    //  *
    //  * @return void
    //  */
    // public function __construct() {
    //     $this->middleware('auth:api', ['except' => ['login', 'signUp','otpVarification']]);
    // }

    /**
    * Function for user registration
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function signUp(Request $request){
        	//dd($formData);
		$from		=	'api';
		$model		=	'User';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	LoginValidationHelper::userSignUp($request->all(),$attributes);

		if($response['status']== 0){
			$response['errors']	=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	1;
			$response['message']	=	'Registraion successful';
		}
		return $response;
        
    }

    public function UserData(){
        $data = User::where('id',auth('api')->user()->id)->with('interest.interestCategory','city','country','state')->withCount('myFollowing','Myfollwer','totalPost')->first();
        return response()->json([
            'user_data' => $data,
            "status" => 1,  
         ]);
    }

    /**
     * Function for get validate otp 
     * @param $formData as formdata
     * @return response
     */
    public function otpVarification(Request $request) {
    
       if(isset($request->cellphone)){
        $credentials = User::where('id', auth('api')->user()->id)->update([
            'is_mobile_verified' => 1,
            'cellphone'          => $request->cellphone
           ]);
           return response()->json([
            "status" => 1,
            "message" => "Otp validation successfull,Mobile number has been changed",
            ]);
       }else{
        $credentials = User::where('id', $request['user_id'])->update([
            'is_mobile_verified' => 1
           ]);    
       }
       return response()->json([
        "status" => 1,
        "message" => "Otp validation successfull,Please login",
        ]);
    }


	/**
    * Function for user login
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'cellphone'     => 'required|exists:users',
            'password'      => 'required|string|min:6|max:15',
            'dial_code'     => 'required',
            'country_code'  => 'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        try {
            $otp = mt_rand(1111, 9999);
            $credentials = User::where('cellphone', $request['cellphone'])->where('dial_code', $request['dial_code'])->where('country_code',$request['country_code'])
                                ->first();
            if ($credentials) {
                if($credentials->status != "0"){
                    if (Hash::check($request['password'], $credentials['password'])) {
                        if (!$token = auth('api')->fromUser($credentials)) {
                            return response()->json(['status' => 0, 'message' => 'Unauthorized']);
                        }
                        if ($credentials->is_mobile_verified  === 1) {
    
                            if(isset($request['notification_token']) && !empty($request['notification_token'])){
                                // $notificationTokens  =   \App\Models\NotificationTokens::where('user_id', $credentials->id)
                                //                          ->where('notification_token',$request['notification_token'])
                                //                          ->first();
                                //  if (isset($notificationTokens) && !empty($notificationTokens)) {
                                //      $notificationTokens->notification_token   =   $request['notification_token'];
                                //  }else{
                                //      $notificationTokens                       =   new \App\Models\NotificationTokens;
                                //      $notificationTokens->notification_token   =   $request['notification_token'];
                                //      $notificationTokens->user_id              =    $credentials->id;
                                //  }
                                //  $notificationTokens->save();

                                $notificationTokens = \App\Models\NotificationTokens::updateOrCreate(
                                    ['user_id' => $credentials->id],
                                    ['notification_token' =>$request['notification_token']]
                                );
                             }
                            return response()->json([
                                    'status' => 1,
                                    'message' => 'Successfully login.',
                                    'user_data' => $credentials,
                                    'profile_status' => $credentials->profile_complele,
                                ] + $this->respondWithToken($token));
                        }else{
                            return response()->json([
                                'status'    => 2,
                                'message'   => trans("Mobile Verification is Pending"),
                                'otp'       => $otp,
                                'userData' => $credentials,
                                'otp_type'  => 2
                            ]);
                        }
                      
                    }
                    return response()->json([
                        'status' => 0,
                        'message' => 'Password does not match.',
                        'data' => []
                    ]);
                }else{
                    return response()->json([
                        'status' => 0,
                        'message' => 'User is deactivated please contact to admin.',
                        'data' => []
                    ]);
                }
              
            }
            return response()->json([
                'status' => 0,
                'message' => 'Mobile number is invalid.',
                'data' => []
            ]);
        }
        catch (\Exception $exception){
            // return error_response($exception);
        }
    }

    protected function respondWithToken($token)
    {
        return [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
    
    public function profileStepOne(Request $request){
        if($request->hasFile('image')){
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10048',
            
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                    "success" => 0,
                    "message" => $error,
                ]);
            }
        }
        try{
            $user = User::where('id', auth('api')->user()->id)->first();
            if (!empty($user)){
                $user->bio = $request['bio'];
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $destinationPath = public_path('uploads/profile-image/');
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename =  time(). '.' . $extension;
                    $file->move($destinationPath, $filename);
                    $user->image = $filename;
                }
                $user->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'List',
                    'image_path' => URL::to('/') . 'uploads/profile-image/',
                    'data' => $user
                ]);
            }
            // return no_records('No Records');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
        
    }


       /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function getCountry(){  
		$response       		=   array();
        $attribute['type']		=	'api';
        $response['data']=Country::select('id','name')->get();
        $response['status']='success';
        if(empty($response['data']))
		{
			$response['status']=0;
			return $response['error']='no countries found';
		}        	
        //pr($formData);
        $response['status']=1;
        return $response;
    }// end getCountry()
	

        /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function getStates(Request $request){  
   
        $response       		=   array();
        $attribute['type']		=	'api';
        $response['data']=State::where("country_id",$request->country_id)
                    ->get(["id","name"]);
		  $response['status']='success';
        if(empty($response['data']))
		{
			$response['status']=0;
			return $response['error']='no states found';
		}        	
        //pr($formData);
        $response['status']=1;
        return $response;
    }// end getCountry()
    

 
    /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function getCity(Request $request){  
		$response       		=   array();
        $attribute['type']		=	'api';
        $response['data']=City::where("state_id",$request->state_id)
                    ->get(["id","name"]);
		  $response['status']='success';
        if(empty($response['data']))
		{
			$response['status']='error';
			return $response['error']='no states found';
		}        	
        //pr($formData);
        return $response;
    }// end getCountry()
	
    /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function userAddress(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'country_id'     => 'required',
                'state_id'      => 'required',
                'city_id'     => 'required',
            ]);
            if ($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json([
                    "status" => 0,
                    "message" => $error,
                ]);
            }


            $user = User::where('id', auth('api')->user()->id)->first();

            if (!empty($user)){
                $user->country =$request->country_id ?? Null;
                $user->state = $request->state_id ?? Null ;
                $user->city = $request->city_id ?? Null ;
                $user->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'List',
                    'data' => $user
                ]);
            }
            // return no_records('No Records');
        }
        catch (\Exception $exception){
            // return error_response($exception);
        }

    }
    /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function getInterest(){
        $response       		=   array();
        $response['data']=ProductCategory::select('id','name')->get();
        if(empty($response['data']))
		{
			$response['status']=0;
			return $response['error']='no interst found';
		}        	
        $response['status']=1;
        return $response;
    }
      /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function userInterest(Request $request){
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->profile_complele = 1;
        $user->save();

        foreach($request->selectedIds as $ids){
          $create  = UserInterest::create([
                'user_id' => auth('api')->user()->id,
                'interest_id' => $ids,
            ]);
        }
        return response()->json([
            'status' => 1,
            'user_data' =>$user,
            'message' => 'data successfully store',
        ]);
     
    }


    public function discoverUser(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            // User::where('id','!=',auth('api')->user()->id)->where()
        }
        catch (\Exception $exception){
                return error_response($exception);
        }

    }
    /**
    * Function for fetching faq
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function faqData(){
        $items = FAQ::orderBy('list_order')->get();
        return response()->json([
            'status'        => 1,
            'user_profile'  =>  0,
            'data'          => $items
        ]);

    }
   /**
    * Function for update profile 
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    public function updateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'bio'                       =>  'required',
            'is_liked'                  =>  'required',
            'gender'                    =>  'required',
            'firstname'                  =>  'required|max:30',
            'lastname'                  =>  'required|max:30',
            'country'                   =>  'required',
            'state'                     =>  'required',
            'city'                      =>  'required',
            'is_private'                  =>  'required',
            'username'                  => 'required|'. Rule::unique('users')->ignore(user()->id),
        ]);


        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {

       $name  =  explode(" ", $request->fullname);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $destinationPath = 'uploads/profile-image/';
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename =  time(). '.' . $extension;
            $file->move($destinationPath, $filename);
            $user = User::where('id', auth('api')->user()->id)->update([
                'bio' => $request->bio,
                'firstname' => $request->firstname ?? '',
                'lastname'  => $request->lastname  ?? '',
                'is_liked'  => $request->is_liked,
                'gender'    => $request->gender,
                'image'     => $filename,
                'username'  => $request->username,
                'country'   =>  $request->country,
                'state'     =>  $request->state,
                'city'      => $request->city,
                'is_private'=>  $request->is_private
            ]);

        }else{
            $user = User::where('id', auth('api')->user()->id)->update([
                'bio'           => $request->bio,
                'firstname'     => $request->firstname ?? '',
                'lastname'      => $request->lastname ?? '',
                'is_liked'      => $request->is_liked,
                'gender'        => $request->gender,
                'username'      => $request->username,
                'country'       =>  $request->country,
                'state'         =>  $request->state,
                'city'          => $request->city,
                'is_private'    =>  $request->is_private
            ]);
        }
        $deleteuser = UserInterest::where('user_id', auth('api')->user()->id)->delete();
        if(is_array($request->interest)){
            foreach($request->interest as $ids){
                $create  = UserInterest::create([
                    'user_id' => auth('api')->user()->id,
                    'interest_id' => $ids,
                ]);
            }
        }else{
            $interest_data = json_decode($request->interest);
            foreach($interest_data as $ids){
                $create  = UserInterest::create([
                    'user_id' => auth('api')->user()->id,
                    'interest_id' => $ids,
                ]);
            }
        }

         $username = User::where('id', auth('api')->user()->id)->first();
          return response()->json([
              'status' => 1,
              'user_data' =>$username,
              'message' => 'Profile updated successfully',
          ]);
        }
        catch (\Exception $exception){
                return error_response($exception);
        }

    }
        /**
        * Function for change password       
        *
        * @param $formData as formData
        *
        * @return reponse.
        */
    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'current_password'          =>  'required',
            'password'                  =>  'required',
            'confirm_password'          =>  'same:password',
        
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        $currentPassword    =   $request->current_password;
        $newPassword        =    $request->password;
        $newPassword        =   Hash::make($newPassword);
        $userInfo           =   User::where('id', auth('api')->user()->id)->first();
        if (Hash::check($currentPassword, $userInfo->password)) {
            $userInfo->update(array('password' => $newPassword));
            return response()->json([
                'status' => 1,
                'message' => 'Your password has been changed successfully now you can login with your new password.',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Current password does not match',
            ]);
        }
    }
    
    public function UserInfoData(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        // User::where('id','user_id')->with('following','follwer','totalpost')
        // $data =  Comment::with('user')->where('post_video_id',$request['post_id'])->where('parent_id',0)
        // ->withCount(['parent','is_like','commentLike'])->paginate(10);

        $user_data  = User::where('id',$request['user_id'])->withCount('myFollowing','Myfollwer','totalPost')->with('city','country','state')->first();
        $is_follow = Follower::where('follower_id',auth('api')->user()->id)->where('following_id',$request['user_id'])->first();
        $is_block = Block::where('block_by_id',auth('api')->user()->id)->where('block_to_id',$request['user_id'])->first();
        if (empty($is_follow)) {
            $is_follow = 0;
        }else{
            $is_follow = 1;
        }
        if (empty($is_block)) {
            $is_block = 0;
        }else{
            $is_block = 1;
        }
        
        $user_data['is_follow'] = $is_follow;
        $user_data['is_block'] = $is_block;
 
        return response()->json([
            'status'    => 1,
            'user_data' => $user_data
        ]);

    }


    public function forgetPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'phone_number'			=>			'required|numeric|digits_between:7,15',	
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        $mobile			=		isset($request['phone_number']) ? $request['phone_number'] : '';
        $userDetail		=		User::where('cellphone', $mobile)->first();
        if (!empty($userDetail)) {
            if ($userDetail->status == config('globalConstant.activate')) {
                if ($userDetail->is_deleted!=config('globalConstant.is_deleted')) {
					$otp_mobile 					=   '5678';//LoginValidationHelper::generateNumericOTP(4)
                    $data['forgot_mobile_otp']		=	$otp_mobile;
                    $mobileArray=array(
						'forgot_mobile_otp'						=>	$otp_mobile,
						'forgot_mobile_time' 					=>	Carbon::now()->timestamp,
						'forgot_mobile_resend_time' 			=>	Carbon::now()->addMinutes(config('globalConstant.mobileTime'))->timestamp,
					);
						//OTP WILL BE DELIVERED TO MOBILE
					User::where('cellphone', $mobile)->update($mobileArray);
                    $data['phone']					=	$userDetail->cellphone;
					$data['email']		    		=	$userDetail->email;
					$data['firstname']				=	ucwords($userDetail->firstname);
                    $data['user_id']				=	ucwords($userDetail->id);
                    return response()->json([
                        "status" => 1,
                        "message" => "Otp is sent on your registered mobile no. successfully",
                        "data"    => $data
                     ]);
                
                }
                return response()->json([
                    "status" => 0,
                    "message" => 'Your account is deleted',
                ]);
            }else{
                return response()->json([
                    "status" => 0,
                    "message" => 'Your account is deactivated',
                ]);
            }

        }
        return response()->json([
            "status" => 0,
            "message" => 'The Mobile number is not registered with us.',
        ]);
    }

    public function resetPassword(Request $request){
   
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|string|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/|min:6|max:15',
        ],[
            'password.regex' => 'Password should have at least one Uppercase, one lowercase, one numeric and one special character and not allow white space.'
        ]);

        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        try{
            $user = User::where('id', $request->user_id)->first();
            if($user){
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Your password has been changed successfully!',
                ]);
            }
        }
        catch (\Exception $exception){
            return error_response($exception);
        }

    }

    public function changeMobileNumber(Request $request) {

        $validator = Validator::make($request->all(), [
            'cellphone'				=>  'required|numeric|digits_between:7,15|unique:users,cellphone',
            'dial_code'             => 'required',
            'country_code'          => 'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'OTP send to your new mobile number',
        ]);
    }

    public function varificationMobile(Request $request) {
        $validator = Validator::make($request->all(), [
            'cellphone'				=>  'required|numeric|digits_between:7,15|unique:users,cellphone',
            'dial_code'             => 'required',
            'country_code'          => 'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        $user = User::where('id',auth('api')->user()->id)->update([
            'cellphone'             => $request['cellphone'],
            'dial_code'             =>  $request['dial_code'],
            'country_code'          => $request['country_code'],
        ]);
        return response()->json([
            'status' => 1,
            'message' => 'Successfully change your mobile number!',
        ]);

    }
    
    public function termAndService() {
      $data =   Content::where('id',2)->select('content')->get();
      return response()->json([
        'status' => 1,
        'data' => $data,
        ]);

    }

    public function infoContent() {
        $array = [10,11,12];
       $content =  Content::whereIn('id',$array)->get();
       return response()->json([
        'status' => 1,
        'data' => $content,
        'image_url' => URL::to('/') . '/uploads/images',
        ]);
    }


    public function notificatoinSetting(Request $request) {
        $validator = Validator::make($request->all(), [
            'status'             => 'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        $user = User::where('id', auth('api')->user()->id)->update([
            'push_notification'  => $request->status
        ]);
        return response()->json([
            'status' => 1,
            'message'=> "Notification setting changed successfully"
            ]);
    }

    public function socialLogin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'social_type'     => 'required',
                'socialLoginID'      => 'required',
            ]);
            if ($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json([
                    "status" => 0,
                    "message" => $error,
                ]);
            }
            //for google login 
            if($request->social_type == "0"){
                $user_store_data =  User::where('google_token', $request->socialLoginID)->first();
                    
                if ($user_store_data != "") {
                    if ($user_store_data->status != "0") {  
                        if (!$token = auth('api')->fromUser($user_store_data)) {
                            return response()->json(['status' => 0, 'message' => 'Unauthorized']);
                        }
                        $user_store_data = $user_store_data->toArray();
                        if ($user_store_data['cellphone'] != null && $user_store_data['username'] != null) {
                            if($user_store_data['is_mobile_verified'] != "0"){
                                return response()->json([
                                    'status'         => 1,
                                    'user_profile'   => 3,
                                    'profile_status' => $user_store_data['profile_complele'],
                                    'data'           => $user_store_data
                                    ] + $this->respondWithToken($token));
                            }else{
                                return response()->json([
                                    'status'        => 1,
                                    'user_profile'  => 2,
                                    'data'          => $user_store_data
                                ]);
                            }
                        } else {
                            return response()->json([
                            'status'        => 1,
                            'user_profile'  =>  1,
                            ]);
                        }
                    }
                    return response()->json([
                        'status' => 0,
                        'message' => 'User is deactivated please contact to admin.',
                    ]);
                } else {
                    return response()->json([
                        'status'        => 1,
                        'user_profile'  =>  0,
                    ]);
                }
                
            }
            //facebook login social
            if($request->social_type == "1"){
                $user_store_data =  User::where('facebook_token', $request->socialLoginID)->first();


                if ($user_store_data != "") {
                    if ($user_store_data->status != "0") {
                        if (!$token = auth('api')->fromUser($user_store_data)) {
                            return response()->json(['status' => 0, 'message' => 'Unauthorized']);
                        }
                        $user_store_data = $user_store_data->toArray();
                        if ($user_store_data['cellphone'] != null && $user_store_data['username'] != null) {
                            if ($user_store_data['is_mobile_verified'] != "0") {
                                return response()->json([
                                'status'        => 1,
                                'user_profile'  => 3,
                                'profile_status' =>$user_store_data['profile_complele'],
                                'data'          => $user_store_data
                                ] + $this->respondWithToken($token));
                            } else {
                                return response()->json([
                                'status'        => 1,
                                'user_profile'  => 2,
                                'data'          => $user_store_data
                            ]);
                            }
                        } else {
                            return response()->json([
                        'status'        => 1,
                        'user_profile'  =>  0,
                        'data'          => $user_store_data
                        ]);
                        }
                    }
                    return response()->json([
                        'status' => 0,
                        'message' => 'User is deactivated please contact to admin.',
                    ]);
                } else {
                    return response()->json([
                        'status'        => 1,
                        'user_profile'  =>  0,
                        'user_found' => 0,
                    ]);
                }
                
            }

             //apple login social
             if($request->social_type == "2"){
                $user_store_data =  User::where('apple_token', $request->socialLoginID)->first();
                if ($user_store_data != "") {
                    if ($user_store_data->status != "0") {
                        if (!$token = auth('api')->fromUser($user_store_data)) {
                            return response()->json(['status' => 0, 'message' => 'Unauthorized']);
                        }
                        $user_store_data = $user_store_data->toArray();
                        if ($user_store_data['cellphone'] != null && $user_store_data['username'] != null) {
                            if ($user_store_data['is_mobile_verified'] != "0") {
                                return response()->json([
                                'status'        => 1,
                                'user_profile'  => 3,
                                'profile_status' =>$user_store_data['profile_complele'],
                                'data'          => $user_store_data
                                ] + $this->respondWithToken($token));
                            } else {
                                return response()->json([
                                'status'        => 1,
                                'user_profile'  => 2,
                                'data'          => $user_store_data
                            ]);
                            }
                        } else {
                            return response()->json([
                        'status'        => 1,
                        'user_profile'  =>  0,
                        'data'          => $user_store_data
                        ]);
                        }
                    }
                    return response()->json([
                        'status' => 0,
                        'message' => 'User is deactivated please contact to admin.',
                    ]);
                } else {
                 
                    return response()->json([
                        'status'        => 1,
                        'user_profile'  =>  0,
                        'user_found' => 0,
                    ]);
                }
            }
        }catch (\Exception $exception){
            return $exception;
        }
      
    }


    public function UpdateSocial(Request $request) {
        $validator = Validator::make($request->all(), [
            'social_type'           => 'required',
        	'firstname'				=>	'required|max:30',
			'lastname'				=>	'required|max:30',
			'gender'				=>	'required',
			'cellphone'				=>  'required|numeric|digits_between:7,15',
            'username'				=>  'required|unique:users,username|max:30',
			'dial_code'				=>  'required',
			'country_code'			=>  'required',
            'socialLoginID'         =>  'required',
        ]);
        if ($validator->fails()){
            $response['errors'] = $validator->errors()->toArray();
            return $response;
        }
        if($request->social_type == "0"){

            $user_data = User::updateOrCreate(
                ['google_token'  => $request->socialLoginID],
                [
                    'firstname'     =>  $request->firstname,
                    'lastname'      =>  $request->lastname,
                    'gender'        =>  $request->gender,
                    'cellphone'     =>  $request->cellphone,
                    'username'      =>  $request->username,
                    'dial_code'     =>  $request->dial_code,
                    'country_code'  =>  $request->country_code,
                    'is_mobile_verified' => 0,
                    // 'mobile_otp'    => LoginValidationHelper::generateNumericOTP(4),
                    'mobile_otp'    => 5678,
                ]
            );
            $response 		= 	array('status' => 1, 
             'userData' => $user_data,
             'message'  => 'OTP is sent to your registered mobile number'
            );
		    return $response;
        }
        if($request->social_type == "1"){

            $user_data = User::updateOrCreate(
                ['facebook_token'  => $request->socialLoginID],
                [
                    'firstname'     =>  $request->firstname,
                    'lastname'      =>  $request->lastname,
                    'gender'        =>  $request->gender,
                    'cellphone'     =>  $request->cellphone,
                    'username'      =>  $request->username,
                    'dial_code'     =>  $request->dial_code,
                    'country_code'  =>  $request->country_code,
                    'is_mobile_verified' => 0,

                    // 'mobile_otp'    => LoginValidationHelper::generateNumericOTP(4),
                    'mobile_otp'    => 5678,
                ]
            );
            $response 		= 	array('status' => 1, 
             'userData' => $user_data,
             'message'  => 'OTP is sent to your registered mobile number'
            );
		    return $response;
        }
        if($request->social_type == "2"){

            $user_data = User::updateOrCreate(
                ['apple_token'  => $request->socialLoginID],
                [
                    'firstname'     =>  $request->firstname,
                    'lastname'      =>  $request->lastname,
                    'gender'        =>  $request->gender,
                    'cellphone'     =>  $request->cellphone,
                    'username'      =>  $request->username,
                    'dial_code'     =>  $request->dial_code,
                    'country_code'  =>  $request->country_code,
                    'is_mobile_verified' => 0,
                    // 'mobile_otp'    => LoginValidationHelper::generateNumericOTP(4),
                    'mobile_otp'    => 5678,
                ]
            );
            $response 		= 	array('status' => 1, 
             'userData' => $user_data,
             'message'  => 'OTP is sent to your registered mobile number'
            );
		    return $response;
        }

    }
    public function socialOtpVerification(Request $request) {
        $validator = Validator::make($request->all(), [
            'social_type'           => 'required',
			'cellphone'				=>  'required|numeric|digits_between:7,15',
            'otp'                   =>  'required'
        ]);
        $credentials = User::where('cellphone', $request->cellphone)->update([
            'is_mobile_verified' => 1
           ]);    

        $user_store_data =  User::where('cellphone', $request->cellphone)->first();
        if (!$token = auth('api')->fromUser($user_store_data)) {
             return response()->json(['status' => 0, 'message' => 'Unauthorized']);
          }

        return response()->json([
        'status'  => 1,
        'profile_status' => $user_store_data->profile_complele,
         'data'          => $user_store_data,
         'message'  => "OTP verification successfull",
         ] + $this->respondWithToken($token));
   
    }
    
    public function eventData() {
        $url = 'https://serpapi.com/search.json?q=nearby+events+in+usa&google_domain=google.com&gl=us&hl=en&api_key=ccfc0ea4974210c18caea3b8c3c8e064f5c4c926b5578d47fbd6572c481dc42d';
        $response = file_get_contents($url);
        return  $response ;
    }

    
    

    

}
