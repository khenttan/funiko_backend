<?php

namespace App\libraries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use HTML, Config, Auth, Blade, Cookie, DB, File, Hash, Redirect, Response, Session, URL, Validator;
use App\Helpers\CustomHelper;
use App\Models\User;
use Carbon\Carbon;
use JWTAuth;;
use Illuminate\Validation\Rule;

use Image;
/**
 * LoginValidationHelper Helper
 *
 * Add your methods in the class below
 */
class LoginValidationHelper
{

	
	/**
	 * ValidationHelper::getUserSignupValidation()
	 * @Description Function  for validation on Buyer Signup form  
	 * @Used at Front HomeController
	 * @param null
	 * @return $validation message and validation
	 * */
	public static function getUserSignupValidation($formData, $attribute)
	{
		$from						=	$attribute['from'];
		/* define validatation messages */
		$message = array(
			'firstname.required' 			=>	trans('Please enter firstname.'),
			'lastname.required' 			=>	trans('Please enter firstname.'),
			
			'dial_code.required' 			=>	trans('Please enter Dial Code.'),
			'country_code.required' 		=>	trans('Please enter  Country code.'),
			'cellphone.required' 			=>	trans('Please enter cellphone'),
			'password.required' 			=>	trans('Please enter password'),
			'password.regex' 				=>	trans('Password should contain 6 Characters, One Uppercase, One Lowercase, One Number and one special case'),
			'confirm_password.same'			=>	trans('Password and Confirm Password should be same.'),
		   	);

		//API USER REGISTER RULES
		if (isset($from) && ($from != 'apiupdate')) {
			/* define validation */
			$validate = array(
				'firstname'				=>	'required|max:30',
				'lastname'				=>	'required|max:30',
				'gender'				=>	'required',
				
				'cellphone'				=>  'required|numeric|digits_between:7,15|unique:users,cellphone',
				'username'				=>  'required|unique:users,username',
				'dial_code'				=>  'required',
				'country_code'			=>  'required',
			);
			if($from=='admin'){
				$validate['user_type']	=	'required';
			}
		}

		if (isset($attribute['userId']) && !empty($attribute['userId'])) {
		
			if (isset($from) && ($from == 'admin' || $from == 'apiupdate')) {
				
				$validate['state']          	= 		['required'];
				$validate['country']        	= 		['required'];
				$validate['city']           	= 		['required'];
				$validate['category_id']    	= 		['required'];
				$validate['subcategory_id'] 	= 		['required'];
				$validate['company']        	= 		['required'];

				if (isset($formData['password'])) {
					$validate['password'] 			=	'regex:' . "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/";
					$validate['confirm_password'] 	=	'same:password';
				}
				if (isset($formData['cellphone'])) {
					$validate['cellphone'] 			=	'required|numeric|digits_between:7,15';
				}
			}
		} else {
			//$validate['email'] 					=	'required|email|unique:users,email';
			$validate['password'] 				=	'required|regex:' . "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/";
			$validate['confirm_password'] 		=	'same:password';
		}
		return array($message, $validate);
	} //end getUserSignupValidation()


	/**
	 * LoginValidationHelper:: userSignUp()
	 * @function for signUp in site
	 * @Used in overAll System
	 * @param $form data as form data
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function userSignUp($formData = array(), $attribute = array())
	{

		$response = $data			=	array();
		$from						=	$attribute['from']??'';
		$type						=	$attribute['type']??'';
		
		list($message, $validate) 	= 	LoginValidationHelper::getUserSignupValidation($formData, $attribute);
		//dd('ass');
		$validator 					= 	Validator::make($formData, $validate, $message);

		// Check Validation
		
		if ($validator->fails()) {
			$response = array('status' => 0, 'validator' => $validator);
		
			return $response;
		}
		//dd($formData,$attribute);
		
		//Update Data For Admin and Client
		if (isset($type) && ($from == 'admin' || $from == 'apiupdate' ) && ($type == 'update' || $type == 'edit') ) {
			if (isset($attribute['userId']) && !empty($attribute['userId'])) {
				
				$obj								=		User::find($attribute['userId']);
				$obj->firstname						=		$formData['firstname']??'';
				$obj->lastname						=		$formData['lastname']??'';
				

				$obj->company						=		$formData['company']??'';
				$category=[
	            	$formData['category_id']??'',$formData['subcategory_id']??''
	            ];

	            $address=[
	            	$formData['country']??'',$formData['state']??'',$formData['city']??''
	            ];
	  			

	  			$obj->categories							=		json_encode($category)??'';
	            $obj->address								=		json_encode($address)??'';


				//$obj->email							=		$formData['email']??'';
				$password									= 		$formData['password']??'';
				
          		if (!empty($formData['image'])) {
		            $photo = self::uploadProfilePicture($formData['image']);
		            $obj->image = $photo;
		        }

		      
				if ($password && !empty($password)) {
					$obj->password 							= 		Hash::make($password)??'';
				}

				if($obj->cellphone==$formData['cellphone']  && $from=='apiupdate')
				{
					$obj->cellphone							=		$formData['cellphone']??'';	
				}
				if($obj->cellphone!=$formData['cellphone']  && $from=='apiupdate'){
					$obj->temp_cellphone					=		$formData['cellphone']??'';
				}
				if($obj->email!=$formData['email']  && $from=='apiupdate'){
					$obj->temp_email						=		$formData['email']??'';
				}


				if($obj->email==$formData['email']  && $from=='apiupdate')
				{
					$obj->email								=		$formData['email']??'';	
				}

				if($from == 'admin'){
					$obj->cellphone							=		$formData['cellphone']??'';	
					$obj->email								=		$formData['email']??'';
					$obj->dial_code							=		trim($formData['dial_code'], "+")??'';
					$obj->country_code						=		$formData['country_code']??'IN';
				}
				if($from == 'apiupdate'){
					//address update
				}
				$obj->save();

			 }
			 if($from=='apiupdate'){
				if($obj->cellphone!=$formData['cellphone'] ){
					$updateData['cellphone_update_status']=0;
				}
				else{
					$updateData['cellphone_update_status']=1;	
				}
				if($obj->email!=$formData['email'] ){
					$updateData['email_update_status']=0;
				}
				else{
					$updateData['email_update_status']=1;	
				}
				$response 		= 	array('status' => 1, 'data' => $data,"updateData"=>$updateData);
				return $response;	
			}

			$response 		= 	array('status' => 1, 'data' => $data);
			return $response;
		}

		//Insert into Users Table 
        if (isset($from) && ($from != 'apiupdate')) {
           
            $insert['firstname']					=		$formData['firstname']??'';
            $insert['lastname']						=		$formData['lastname']??'';
          	$insert['username']						=		$formData['username']??'';
          
            // $insert['categories']					=		json_encode($category)??'';
            // $insert['address']						=		json_encode($address)??'';
            $insert['gender']						=		$formData['gender']??'';
            $password								= 		$formData['password']??'';
            $insert['password'] 					= 		Hash::make($password)??'';
            $insert['status']   					= 		config('globalConstant.activate')??'';//active or inactive
            $insert['cellphone']					=		$formData['cellphone']??'';
            $insert['dial_code']					=		trim($formData['dial_code'], "+")??'';
			$insert['country_code']					=		$formData['country_code']??'';
			$insert['phone']						=		$insert['dial_code'].$formData['cellphone'];
			if($from == 'api'){
				$insert['mobile_otp']					=       LoginValidationHelper::generateNumericOTP(4);
				$insert['verify_mobile_time']			=		Carbon::now()->timestamp;
				$insert['verify_mobile_resend_time']	=		Carbon::now()->addMinutes(config('globalConstant.mobileTime'))->timestamp;
				;
			}
			if($from == 'admin'){
				// $insert['user_type']					=   config('globalConstant.activate')??'';	
				// $insert['is_email_verified']			=	config('globalConstant.mob_verify')??'';
				$insert['is_mobile_verified']			=	config('globalConstant.email_verify')??'';
			}
            $user = User::create($insert);

		}
		$user=User::find($user->id);
		$mobileData		=	[
			'is_mobile_verified' 	=> 	$user->is_mobile_verified,
			'mobile'				=>	$user->cellphone,
		];
		$response 		= 	array('status' => 1,  'userData' => $user);
		return $response;

	} //end userSignUp()



	/**
	 * ValidationHelper::getLoginValidation()
	 * @Description Function  for validation on login form
	 * @param null
	 * @return $validation message and validation
	 * */
	public static function getLoginValidation($formData)
	{
		/* define validatation messages */
		$message = array(
			'cellphone.required' 	=> trans("Email is required."),
			'password.required' => trans("Password is required."),
		);
		/* define validatation messages */
		
		/* define validation */
		$validate = array(
			'cellphone' => 'required',
			'password' => 'required',
		);

		return array($message, $validate);
		/* define validation */
	} //ends getLoginValidation()


	/**
	 * ValidationHelper::getLoginValidation()
	 * @Description Function  for validation on login form
	 * @param null
	 * @return $validation message and validation
	 * */
	public static function getStep1Validation($formData)
	{
		$message = array(
			'email.required' 	=> trans("Email is required."),
			'email.email' 		=> trans("Invalid email address."),
		);
		/* define validatation messages */

			
		/* define validation */
		$validate = array(
			'email' => 'required|email|unique:users,email',
			'cellphone'				=>  'required|numeric|digits_between:7,15|unique:users,cellphone',

		);

		return array($message, $validate);
		/* define validation */
	} //ends getLoginValidation()


	/**
	 * LoginValidationHelper:: Login()
	 * @function for login in site
	 * @Used in overAll System
	 * @param $form data as credential
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function regSteps($formData = array(), $attribute = array())
	{
		$userdata	=	array();
		//$type		=	$attribute['type'];
		$response	=	array();
		$status		=	"failure";
		$message	=	'';

		//dd('ss');
		list($message, $validate) 	= 	LoginValidationHelper::getStep1Validation($formData);
		$validator 					= 	Validator::make($formData, $validate, $message);		
		// Check Validation
		if ($validator->fails()) {
			$response = array('status' => "error", 'validator' => $validator);
			return $response;
		}

		$response['status']		=	'success';
		$response['message']	=	'step 1 passed successfully';
		return $response;
	} //end login()
























	/**
	 * LoginValidationHelper:: Login()
	 * @function for login in site
	 * @Used in overAll System
	 * @param $form data as credential
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function login($formData = array(), $attribute = array())
	{
		$userdata	=	array();
		$type		=	$attribute['type'];
		$response	=	array();
		$status		=	"failure";
		$message	=	'';

		list($message, $validate) 	= 	LoginValidationHelper::getLoginValidation($formData);
		$validator 					= 	Validator::make($formData, $validate, $message);		
		// Check Validation
		if ($validator->fails()) {
			$response = array('status' => "error", 'validator' => $validator);
			return $response;
		}

		$message	=	'';

		$userdata = array(
			'cellphone' 	=> 		$formData['cellphone'],
			'password' 		=> 		$formData['password'],
		);
		if (Auth::attempt($userdata)) {
			
			if (Auth::check()) {
				$firstname  	=	Auth::user()->firstname;
				//BLOCKED
				if (Auth::check() && Auth::user()->is_blocked == config('globalConstant.block')) {
					Auth::logout();
					if ($type == 'api') {
						$message	=	trans("Your account is blocked");
					} else {
						return Redirect::back()->withInput();
					}
				}
				//INACTIVE
				if (Auth::check() && Auth::user()->status ==  config('globalConstant.deactivate') ) {
					Auth::logout();
					if ($type == 'api') {
						$message	=	trans("Your account is deactivated");
					} else {
						return Redirect::back()->withInput();
					}
				}
				//DELETED
				if (Auth::check() && Auth::user()->is_deleted == config('globalConstant.is_deleted')) {
					Auth::logout();
					if ($type == 'api') {
						$message	=	trans("Your account is deleted");
					} else {
						return Redirect::back()->withInput();
					}
				}
				if (Auth::check()	&& ((Auth::user()->is_mobile_verified!=config('globalConstant.mob_verify')))	 ) {
					$mob	=	Auth::user()->is_mobile_verified;
					if ($type == 'api') {
						

						//CASE2 mobile is not verified
						if($mob==config('globalConstant.mob_not_verify')){
							
							$update['mobile_otp']=$mobile_otp		=       '5678';//LoginValidationHelper::generateNumericOTP(4);
							$update['verify_mobile_time']			=		Carbon::now()->timestamp;
							$update['verify_mobile_resend_time']	=		Carbon::now()->addMinutes(config('globalConstant.passwordTime'))->timestamp;
							;
							User::where('id', Auth::user()->id)->update($update);
							// Send OTP sms to user
							$mobile_no 	= Auth::user()->cellphone;
							if (!empty($mobile_no)) {
								/* WILL SEND  OTP ONLY FOR  MOBILE NON VERIFIED*/
								
							}
							$status									=	'pending';	
							$response['is_mobile_verified']			=	config('globalConstant.mob_not_verify');
							$response['cellphone']					=	$mobile_no;
							$response['otp_mobile']					=	$mobile_otp;
							$message	=	trans("Mobile Verification is Pending");
							Auth::logout();
						}//CASE2 ends

						
					}
				}
				if (Auth::user() &&  (Auth::user()->is_mobile_verified==config('globalConstant.mob_verify')) ) {
					$msg					=  	ucfirst(Auth::user()->firstname) . ", " . trans("Login Succesful");
					//$token 				= JWTAuth::attempt($userdata); 
					$userData				=	Auth::user()->toArray();
					if(empty($userData['image'])&&empty($userData['bio'])){
						$response['step1']=0;	
					}
					else{
						$response['step1']=1;	
					}
					if(empty($userData['address'])){
						$response['step2']=0;	
					}
					else{
						$response['step2']=1;
					}
					if(empty($userData['interests'])){
						$response['step3']=0;	
					}
					else{
						$response['step3']=1;
					}
					$response['user_data']	=	$userData;
					$status					=	"success";//LoginValidationHelper::generateNumericOTP(4);
				}
				
			} else {
				if ($type == 'api') {
					$message	=	trans("you are not authorized to access this location");
				} else {
					return Redirect::back()->withInput();
				}
			}
		} else {
			if ($type == 'api') {
				$message	=	trans("Incorrect Credentials");
			} else {
				return Redirect::back()->withInput();
			}
		}

		$response['status']		=	$status;
		$response['message']	=	$message;
		return $response;
	} //end login()















	/**
	 * ValidationHelper::getForgotPasswordValidation()
	 * @Description Function  for validation on Forgot Password form  
	 * @Used at Front HomeController
	 * @param null
	 * @return $validation message and validation
	 *
	 */

	public static function getForgotPasswordValidation($formData, $attribute)
	{
		
		/* define validatation messages */
		$message = array(
			'email.required' 	=>		trans("Email is required"),
			'email.email' 		=>		trans("Invalid email address."),
			'method_name'		=>		trans("Please specify an option to send email or mobile verification."),
		);
		if(isset($formData['method_verify'])&&$formData['method_verify']=='email'){
			$validate = array(
				'email' 		=>		 'required',
				'email' 		=>		 'required|email',
			);
		}
		else if(isset($formData['method_verify'])&&$formData['method_verify']=='mobile'){
			$validate = array(
			'cellphone'			=>			'required|numeric|digits_between:7,15',	
			);
		}

		/* return validation with werror messages */
		return array($message, $validate);
	} //end getForgotPasswordValidation()

	/**
	 * LoginValidationHelper:: userForgetPassword()
	 * @function for user Forget Password
	 * @Used in overAll System
	 * @param $form data as form data
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function userForgetPassword($formData = array(), $attribute = array())
	{
		//dd($formData);
		$response					=		array();
		$from						=		$attribute['from'];
		if(empty($formData['method_verify'])){
			$data['message']		=		trans('Please Specify Method Name');
			$data['msg_type']		=		'error';
			$response 				= 		array('status' => "error", 'data' => $data);
			return $response;
		}
		if(!empty($formData['method_verify']) && $formData['method_verify']!='email' && $formData['method_verify']!='mobile'){
			$data['message']		=		trans('Please Provide Valid Method Name');
			$data['msg_type']		=		'error';
			$response 				= 		array('status' => "error", 	'data' => $data);
			return $response;
		}

		list($message, $validate) 	= 		LoginValidationHelper::getForgotPasswordValidation($formData, $attribute);
		$validator 					= 		Validator::make($formData, $validate, $message);
		//pr($formData);die;
		if ($validator->fails()) {

			$response = array('status' => "error", 'validator' => $validator);
			return $response;
		}
	

		if($formData['method_verify']=='email'){
			$email			=		isset($formData['email']) ? $formData['email'] : '';
			$userDetail		=		User::where('email', $email)->first();	
		}
		else{
			$mobile			=		isset($formData['cellphone']) ? $formData['cellphone'] : '';
			$userDetail		=		User::where('cellphone', $mobile)->first();
			//OTP WILL BE DELIVERED TO MOBILE
		}
		if (!empty($userDetail)) {
			if ($userDetail->status == config('globalConstant.activate')) {
				
				if ($userDetail->is_deleted!=config('globalConstant.is_deleted' ) ){
					
					$otp_email 						=   '1234';//LoginValidationHelper::generateNumericOTP(4)
					$otp_mobile 					=   '5678';//LoginValidationHelper::generateNumericOTP(4)
					
					$emailArray=array(
						'forgot_email_otp'						=>	$otp_email,
						'forgot_email_time' 					=>	Carbon::now()->timestamp,
						'forgot_email_resend_time' 				=>	Carbon::now()->addMinutes(config('globalConstant.emailTime'))->timestamp,
					);
					$mobileArray=array(
						'forgot_mobile_otp'						=>	$otp_mobile,
						'forgot_mobile_time' 					=>	Carbon::now()->timestamp,
						'forgot_mobile_resend_time' 			=>	Carbon::now()->addMinutes(config('globalConstant.mobileTime'))->timestamp,
					);
					if($formData['method_verify']=='email'){
						$data['forgot_email_otp']		=	$otp_email;
						//MAIL WILL BE SENT CONTAINING OTP AND STRING
						$email          =   $userDetail['email']??'';
						$otp       		=   $otp_email??'';
						$name           =   $userDetail['firstname']??'';
						$to             =   $email;
						$to_name        =   ucwords($name)??'';
						$full_name      =   $to_name??'';
						$action         =   "verify_otp";
						$rep_Array      =   array($otp,$full_name);
						CustomHelper::callSendMail($to, $to_name, $rep_Array, $action);	
						User::where('email', $email)->update($emailArray);
					}
					else{
						$data['forgot_mobile_otp']		=	$otp_mobile;
						//OTP WILL BE DELIVERED TO MOBILE
						User::where('cellphone', $mobile)->update($mobileArray);
					}

					

					$data['phone']					=	$userDetail->cellphone;
					$data['email']		    		=	$userDetail->email;
					$data['firstname']				=	ucwords($userDetail->firstname);
				
					$response = array('status' => "success", 'data' => $data);
					return $response;
				} else {
					$data['message']	=	trans('Account is deleted.');
					$data['msg_type']	=	'error';
					$response 			= 	array('status' => "error", 'data' => $data);
					return $response;
				}
			} else {
				$data['message']	=	trans('Your account is deactivated');
				$data['msg_type']	=	'error';
				$response 			= 	array('status' => "error", 'data' => $data);
				return $response;
			}
		} else {
			$data['message']	=	trans('These credentials') . ' ' . trans('does not exists. Please provide valid credentials.');
			$data['msg_type']	=	'success';
			$response 			= 	array('status' => "error", 'data' => $data);
			return $response;
		}
	} //end userForgetPassword()	
	
	/**
	 * ValidationHelper::getOtpValidation()
	 * @Description Function  for validation on otpNumbers   
	 * @Used at Front HomeController
	 * @param null
	 * @return $validation message and validation
	 *
	 */
	public static function getOtpValidation($formData, $attribute)
	{
		/* define validatation messages */
		$message = array(
			'otp_verify.required' 		=>	trans('Please enter a valid OTP'),
			'otp_verify.numeric' 		=>	trans('OTP should be numeric'),
			'otp_verify.digits' 		=>	trans('OTP should be numeric'),
			'otp_verify.check_user_otp' =>	trans('OTP is invalid'),
		);

		/* define validation */
		$validate = array(
			'otp_verify' => 'required|numeric|digits:4',
		);

		/* return validation with werror messages */
		return array($message, $validate);
	} //end getOtpValidation()

	/**
	 * ValidationHelper::getOtpVerificationValidation()
	 * @Description Function  for validation on Otp Verification
	 * @Used at Front HomeController
	 * @param null
	 * @return $validation message and validation
	 *
	 */
	public static function getOtpVerificationValidation($formData, $attribute)
	{
		/* define validatation messages */
		$message = array(
			'otp_verify.required' 		=>		trans('OTP is required'),
			'otp_verify.numeric' 		=>		trans('OTP is numeric'),
			'otp_verify.digits' 		=>		trans('It should be in 4 digits max'),
			'otp_verify.check_otp' 		=>		trans('It should be checked'),
			'email.required' 			=> 		trans("Email is required"),
			'email.email' 				=> 		trans("Invalid email address."),
			'cellphone.required' 		=> 		trans("Cellphone is required"),
			'cellphone.numeric' 		=> 		trans("Invalid cellphone."),

		);

		/* define validation */
		if(isset($formData['method_verify'])&&$formData['method_verify']=='email'){
			$validate = array(
				'email' => 'required',
				'email' => 'required|email',
				'otp_verify' => 'required|numeric|digits:4',
			);
		}
		else if(isset($formData['method_verify'])&&$formData['method_verify']=='mobile'){
			$validate = array(
				'otp_verify' => 'required|numeric|digits:4',
				'cellphone'				=>  'required|numeric|digits_between:7,15',	
			);
		}


		/* return validation with werror messages */
		return array($message, $validate);
	} //end getOtpVerificationValidation()








	/**
	 * LoginValidationHelper:: userVerifyOtp()
	 * @function for resend Verification Link 
	 * @Used in overAll System
	 * @param $form data as form data
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function userVerifyOtp($formData = array(), $attribute = array())
	{
		//dd('ass');
		if (!empty($formData)) {
			if(empty($formData['method_verify'])){
				$data['message']	=	trans('Please Specify Method Name');
				$data['msg_type']	=	'error';
				$response 			= 	array('status' => "error", 'data' => $data);
				return $response;
			}
			if(!empty($formData['method_verify']) && $formData['method_verify']!='email' && $formData['method_verify']!='mobile'){

				$data['message']	=	trans('Please Provide Valid Method Name');
				$data['msg_type']	=	'error';
				$response 			= 	array('status' => "error", 	'data' => $data);
				return $response;
			}
			
			$pageSlug		=	$attribute['pageSlug'];
			$from			=	$attribute['from'];
			$model			=	$attribute['model'];

			//Check validation
			if (!empty($pageSlug)) {
				//Forgot OTP VERIFY
				list($message, $validate) = LoginValidationHelper::getOtpVerificationValidation($formData, $attribute);
			} else {
				list($message, $validate) = LoginValidationHelper::getOtpValidation($formData, $attribute);
			}
			$validator 					 = Validator::make($formData, $validate, $message);
			if ($validator->fails()) {
				$response = array('status' => "error", 'validator' => $validator);
				return $response;
			}
			

			if (!empty($pageSlug)) { // Forgot Time
				// if($formData['method_verify']=='email'){
				// 	$email			=	isset($formData['email']) ? $formData['email'] : '';
				// 	$userDetails 	=	User::where('email', $email)->select('forgot_email_otp as email_otp', 'forgot_email_resend_time as verify_email_resend_time', 'id')->first();
				// }
				// else{
					$mobile			=	isset($formData['cellphone']) ? $formData['cellphone'] : '';
					$userDetails 	=	User::where('cellphone', $mobile)->select('forgot_mobile_otp as mobile_otp', 'forgot_mobile_resend_time as verify_mobile_resend_time', 'id')->first();
				// }
	
			} else {
				//dd('here');
				// if(!isset($formData['user_id'])&&$formData['method_verify']=='email'){
				// 	$email			=	isset($formData['email']) ? $formData['email'] : '';
				// 	$userDetails 	=	User::where('email', $email)->select('firstname','email_otp', 'verify_email_resend_time', 'id')->first();
				// }
				if(!isset($formData['user_id'])&&$formData['method_verify']=='mobile'){
					$mobile			=	isset($formData['cellphone']) ? $formData['cellphone'] : '';
					$userDetails 	=	User::where('cellphone', $mobile)->select('firstname','mobile_otp', 'verify_mobile_resend_time', 'id')->first();
				
				}
				// if(isset($formData['user_id']) && $formData['method_verify']=='email'){
				// 	$email			=	isset($formData['email']) ? $formData['email'] : '';
				// 	$userDetails 	=	User::where('id',$formData['user_id'])->select('firstname','email_otp', 'verify_email_resend_time', 'id')->first();
				// }
				if(isset($formData['user_id'])&&$formData['method_verify']=='mobile'){
					
					$mobile			=	isset($formData['cellphone']) ? $formData['cellphone'] : '';
					$userDetails 	=	User::where('id',$formData['user_id'])->select('firstname','mobile_otp', 'verify_mobile_resend_time', 'id','email')->first();
					//1634829557
				}


			}
			if(empty($userDetails)){

				$data['message']	=	trans('Wrong email or cellphone provided');
				$data['msg_type']	=	'error';
				$response 			=	array('status' => "error", 'data' => $data);
				return $response;	
			}
			// if($formData['method_verify']=='email' && !isset($formData['user_id'])){
			// 	if ($userDetails['email_otp'] == $formData['otp_verify']) {
			// 		$userid 	=	$userDetails['id'];
			// 		if ($userDetails['verify_email_resend_time'] < time()) {
			// 			$data['message']	=	trans('OTP is expired');
			// 			$data['msg_type']	=	'error';
			// 			$response 			=	array('status' => "error", 'data' => $data);
			// 			return $response;
			// 		} else {
			// 			if (!empty($pageSlug)) {
			// 				User::where('id', $userid)->update(array(
			// 					//'forgot_email_otp'						=>	NULL,
			// 					'forgot_email_time' 					=>	NULL,
			// 					'forgot_email_resend_time' 				=>	NULL,
			// 				));
	
			// 				$response 			=	array('status' => "success", 'data' => ['userid' => $userid, 'userData' =>User::find($userid) , 'user_email' => $email, 'otp' => $formData['otp_verify']]);
			// 				return $response;
			// 			} else {
							
			// 				User::where('id', $userid)->update(array('is_email_verified' => config('globalConstant.email_verify')));
			// 				$response 			=	array('status' => "success", 'data' => ['userid' => $userid,  'userData' =>User::find($userid) ,'userDetails' => $userDetails]);
			// 				return $response;
			// 			}
			// 		}
			// 	} else {
			// 		$data['message']	=	trans('Invalid Email OTP');
			// 		$data['msg_type']	=	'error';
			// 		$data['route']		=	'verify-otp';
			// 		$response 			=	array('status' => "error", 'data' => $data);
			// 		return $response;
			// 	}
			// }
			if($formData['method_verify']=='mobile' && !isset($formData['user_id'])){
				if ($userDetails['mobile_otp'] == $formData['otp_verify']) {
					$userid 	=	$userDetails['id'];
					if ($userDetails['verify_mobile_resend_time'] < time()) {
						$data['message']	=	trans('Mobile OTP has been expired');
						$data['msg_type']	=	'error';
						$response 			=	array('status' => "error", 'data' => $data);
						return $response;
					} else {
						if (!empty($pageSlug)) {
							User::where('id', $userid)->update(array(
								//'forgot_mobile_otp'						=>	NULL,
								'forgot_mobile_time' 					=>	NULL,
								'forgot_mobile_resend_time' 			=>	NULL,
							));
	
							$response 			=	array('status' => "success", 'data' => ['userid' => $userid, 'user_mobile' => $mobile, 'otp' => $formData['otp_verify']]);
							return $response;
						} else {
							User::where('id', $userid)->update(array('is_mobile_verified' =>  config('globalConstant.mob_verify') ));
							$response 			=	array('status' => "success", 'data' => ['userid' => $userid, 'userDetails' => $userDetails]);
							return $response;
						}
					}
				} else {
					$data['message']	=	trans('Mobile OTP is invalid');
					$data['msg_type']	=	'error';
					$data['route']		=	'verify-otp';
					$response 			=	array('status' => "error", 'data' => $data);
					return $response;
				}

			}
			if($formData['method_verify']=='mobile' && isset($formData['user_id'])){

				$update['cellphone']					=		$formData['cellphone']??'';
			    $update['dial_code']					=		trim($formData['dial_code'], "+")??'';
				$update['country_code']					=		$formData['country_code']??'';
				$update['phone']						=		$formData['dial_code'].$formData['cellphone'];
				$update['is_mobile_verified']			=		config('globalConstant.mob_verify');
				$update['temp_cellphone']				=		NULL;
							

				if ($userDetails['mobile_otp'] == $formData['otp_verify']) {
					$userid 	=	$userDetails['id'];
					//1634829416
					//1634829830
				//	dd($userDetails['verify_mobile_resend_time'] , time());
					if ($userDetails['verify_mobile_resend_time'] < time()) {
						$data['message']	=	trans('Mobile OTP has been expired');
						$data['msg_type']	=	'error';
						$response 			=	array('status' => "error", 'data' => $data);
						return $response;
					} else {
						
							User::where('id', $userid)->update($update);
							$response 			=	array('status' => "success", 'data' => ['userid' => $userid, 'userDetails' => $userDetails]);
							return $response;
						
					}
				} else {
					$data['message']	=	trans('Mobile OTP is invalid');
					$data['msg_type']	=	'error';
					$data['route']		=	'verify-otp';
					$response 			=	array('status' => "error", 'data' => $data);
					return $response;
				}

			}

			// if($formData['method_verify']=='email' && isset($formData['user_id'])){
			// 	if ($userDetails['email_otp'] == $formData['otp_verify']) {
			// 		$userid 	=	$userDetails['id'];
			// 		if ($userDetails['verify_email_resend_time'] < time()) {
			// 			$data['message']	=	trans('OTP is expired');
			// 			$data['msg_type']	=	'error';
			// 			$response 			=	array('status' => "error", 'data' => $data);
			// 			return $response;
			// 		} else {
			// 				$update['email']					=		$formData['email']??'';
			// 			    $update['is_email_verified']		=		config('globalConstant.email_verify');
			// 			    $update['temp_email']				=		NULL;
							
			// 				User::where('id', $userid)->update($update);
			// 				$response 			=	array('status' => "success", 'data' => ['userid' => $userid,  'userData' =>User::find($userid) ,'userDetails' => $userDetails]);
			// 				return $response;
						
			// 		}
			// 	} else {
			// 		$data['message']	=	trans('Invalid Email OTP');
			// 		$data['msg_type']	=	'error';
			// 		$data['route']		=	'verify-otp';
			// 		$response 			=	array('status' => "error", 'data' => $data);
			// 		return $response;
			// 	}
			// }






			
		} else {
			$data['message']	=	trans("Please provide all the details");
			$data['msg_type']	=	'error';
			$response 			=	array('status' => "error", 'data' => $data);
			return $response;
		}
	} //end userVerifyOtp













	/**
	 * ValidationHelper::getResetPasswordValidation()
	 * @Description Function  for validation on Reset Password form  
	 * @Used at Front HomeController
	 * @param null
	 * @return $validation message and validation
	 * 
	 */
	public static function getResetPasswordValidation($formData, $attribute)
	{
		/* define validatation messages */
		$message = array(
			'password.required' 				=> 	trans('Please enter password'),
			'password.regex' 					=> 	trans('Must Contain 8 Characters, One Uppercase, One Lowercase, One Number and one special case Character.'),
			'confirm_password.required' 		=> 	trans('Please enter confirm-password'),
			'confirm_password.same' 			=> 	trans('Password and Confirm Password should be same'),
		);

		/* define validation */
		$validate = array(
			//'password'					=>	'required|regex:'.PASSWORD_REGX,
			'password'					=>	'required',
			'confirm_password' 			=>	'required|same:password',
		);

		/* return validation with werror messages */
		return array($message, $validate);
	} //end getResetPasswordValidation()





	/**
	 * LoginValidationHelper:: userResetPasswordValidationAndSave()
	 * @function for user Reset Password Validation And Save
	 * @Used in overAll System
	 * @param $form data as form data
	 * @param $attribute as attribute array
	 * @return response array
	 */

	public static function userResetPasswordValidationAndSave($formData = array(), $attribute = array())
	{
		$response					=	array();
		$from						=	$attribute['from'];
		list($message, $validate) 	= 	LoginValidationHelper::getResetPasswordValidation($formData, $attribute);
		$validator 					= 	Validator::make($formData, $validate, $message);
		if(empty($formData['method_verify'])){
			$data['message']	=	trans('Please Specify Method Name');
			$data['msg_type']	=	'error';
			$response 			= 	array('status' => "error", 'data' => $data);
			return $response;
		}
		if(!empty($formData['method_verify']) && $formData['method_verify']!='email' && $formData['method_verify']!='mobile'){
			$data['message']	=	trans('Please Provide Valid Method Name');
			$data['msg_type']	=	'error';
			$response 			= 	array('status' => "error", 	'data' => $data);
			return $response;
		}
		// Check Validation
		if ($validator->fails()) {
			$response = array('status' => "error", 'validator' => $validator);
			return $response;
		}
		$newPassword		=	$formData['password'];
		$newPassword		=	bcrypt($newPassword);
		$otpNumber			=	$formData['otp_number'];
		if($formData['method_verify']=='email'){
			$email			=	isset($formData['email']) ? strtolower($formData['email'] ): '';
		    $userInfo 		=	User::where('email', $email)->where('forgot_email_otp', $otpNumber)->first();

		}else{
			$mobile			=	isset($formData['cellphone']) ? $formData['cellphone'] : '';
			$userInfo 		=	User::where('cellphone', $mobile)->where('forgot_mobile_otp', $otpNumber)->first();
		}
		if (isset($userInfo) && !empty($userInfo)) {
			if($formData['method_verify']=='email'){
				
				User::where('email', $email)->where('forgot_email_otp', $otpNumber)->update(array(
					'password'							=>	$newPassword,
					'forgot_email_otp'					=>	NULL
				));
			}
			else{
				User::where('cellphone', $mobile)->where('forgot_mobile_otp', $otpNumber)->update(array(
					'password'							=>	$newPassword,
					'forgot_mobile_otp'					=>	NULL
				));
			}

			$response = array('status' => "success", 'data' => [ 'email' => $userInfo->email, 'full_name' => ucwords($userInfo->firstname)]);
			return $response;
		} else {
			$response = array('status' => "error");
			return $response;
		}
	} //end userResetPasswordValidationAndSave()



	/**
	 * LoginValidationHelper:: userResendOtp()
	 * @function for resend otp 
	 * @Used in overAll System
	 * @param $form data as form data
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function userResendOtpProfile($formData = array(), $attribute = array())
	{
		
			if($formData['method_verify']=='email'){
				$email			=	isset($formData['email']) ? strtolower($formData['email'] ): '';
				$userInfo 		= User::find($formData['user_id']);
			}else{
				$mobile			=	isset($formData['mobile']) ? $formData['mobile'] : '';
				$userInfo 		=	User::find($formData['user_id']);
			}
			if (!empty($userInfo)) {
				$email_otp			=       '1234';//LoginValidationHelper::generateNumericOTP(4);
				$mobile_otp			=       '5678';//LoginValidationHelper::generateNumericOTP(4)
				
				if($formData["method_verify"]=="mobile"){
						$userInfo->verify_mobile_time 					=	Carbon::now()->timestamp;
						$userInfo->verify_mobile_resend_time 			=	Carbon::now()->addMinutes(config('globalConstant.mobileTime'))->timestamp;
						$userInfo->mobile_otp 							= 	$mobile_otp;
					
				}
				else{
						$userInfo->verify_email_time 					=	Carbon::now()->timestamp;
						$userInfo->verify_email_resend_time 			=	Carbon::now()->addMinutes(config('globalConstant.emailTime'))->timestamp;
						$userInfo->email_otp 							= 	$email_otp;
					
				}
				$userInfo->save();
				//dd($userInfo->verify_mobile_resend_time);
				/*Re Send OTP sms to user */
				if (!empty($userInfo->cellphone)) {
					$mobile_no 	= $userInfo->cellphone;
					//CustomHelper::_SendOtp('resend_otp', $mobile_no, $otp);
				}

				//Re Send OTP mail to user
				if (!empty($userInfo->email)) {
					//MAIL WILL BE SENT CONTAINING OTP AND STRING
					$email          =   $userInfo->email??'';
					$otp       		=   $email_otp??'';
					$name           =   $userInfo->firstname??'';
					$to             =   $email;
					$to_name        =   ucwords($name)??'';
					$full_name      =   $to_name??'';
					$action         =   "verify_otp";
					$rep_Array      =   array($otp,$full_name);
					CustomHelper::callSendMail($to, $to_name, $rep_Array, $action);	
					//User::where('email', $email)->update($emailArray);
				}
				$response 		=	array('status' => "success",'info'=>$userInfo);
				return $response;
			}
		
	} //end userResendOtp


	/**
	 * LoginValidationHelper:: userResendOtp()
	 * @function for resend otp 
	 * @Used in overAll System
	 * @param $form data as form data
	 * @param $attribute as attribute array
	 * @return response array
	 */
	public static function userResendOtp($formData = array(), $attribute = array())
	{
		if (!empty($formData['data'])) {
			
			//dd($formData);
			// if($formData['method_verify']=='email'){
			// 	$email			=	isset($formData['email']) ? strtolower($formData['email'] ): '';
			// 	$userInfo 		= 	User::where('email', $formData['email'])->first();
			// }else{
				$mobile			=	isset($formData['mobile']) ? $formData['mobile'] : '';
				$userInfo 		=	User::where('cellphone', $mobile)->first();
			
			//}
			if (!empty($userInfo)) {
				
				$email_otp			=       '1234';//LoginValidationHelper::generateNumericOTP(4);
				$mobile_otp			=       '5678';//LoginValidationHelper::generateNumericOTP(4)
				
				if($formData["method_verify"]=="mobile"){
					if (isset($formData['api_type']) && !empty($formData['api_type'])) {
						$userInfo->verify_mobile_time 					=	Carbon::now()->timestamp;
						$userInfo->verify_mobile_resend_time 			=	Carbon::now()->addMinutes(config('globalConstant.mobileTime'))->timestamp;
						$userInfo->mobile_otp 							= 	$mobile_otp;
					} else {
						$userInfo->forgot_mobile_time 					=	Carbon::now()->timestamp;
						$userInfo->forgot_mobile_resend_time 			=	Carbon::now()->addMinutes(config('globalConstant.mobileTime'))->timestamp;
						$userInfo->forgot_mobile_otp 					= 	$mobile_otp;
					}
				}
				else{
					if (isset($formData['api_type']) && !empty($formData['api_type'])) {
						$userInfo->verify_email_time 					=	Carbon::now()->timestamp;
						$userInfo->verify_email_resend_time 			=	Carbon::now()->addMinutes(config('globalConstant.emailTime'))->timestamp;
						$userInfo->email_otp 							= 	$email_otp;
					} else {
						$userInfo->forgot_email_time 					=	Carbon::now()->timestamp;
						$userInfo->forgot_email_resend_time 			=	Carbon::now()->addMinutes(config('globalConstant.emailTime'))->timestamp;
						$userInfo->forgot_email_otp 					= 	$email_otp;
					}
				}
				$userInfo->save();
				
				/*Re Send OTP sms to user */
				if (!empty($userInfo->cellphone)) {
					$mobile_no 	= $userInfo->cellphone;
					//CustomHelper::_SendOtp('resend_otp', $mobile_no, $otp);
				}

				//Re Send OTP mail to user
				if (!empty($userInfo->email)) {
					//MAIL WILL BE SENT CONTAINING OTP AND STRING
					$email          =   $userInfo->email??'';
					$otp       		=   $email_otp??'';
					$name           =   $userInfo->firstname??'';
					$to             =   $email;
					$to_name        =   ucwords($name)??'';
					$full_name      =   $to_name??'';
					$action         =   "verify_otp";
					$rep_Array      =   array($otp,$full_name);
					CustomHelper::callSendMail($to, $to_name, $rep_Array, $action);	
					//User::where('email', $email)->update($emailArray);
				}
				$response 		=	array('status' => "success");
				return $response;
			}
		}
		$response 	=	array('status' => "error");
		return $response;
	} //end userResendOtp
	
	/**
	 * LoginValidationHelper:: generateNumericOTP()
	 * @function for generating n digit otp
	 * @Used here for OTP purpose
	 * @param $n as credential
	 * @return response otp
	 */
	public static function generateNumericOTP($n)
	{
	
		// Taking a generator string that consists of 
		// all the numeric digits 
		$generator = "1357902468";
		
		// Iterating for n-times and pick a single character 
		// from generator and append it to $result 
		
		// Login for generating a random character from generator 
		//     ---generate a random number 
		//     ---take modulus of same with length of generator (say i) 
		//     ---append the character at place (i) from generator to result 
		
		$result = "";
		
		for ($i = 1; $i <= $n; $i++) {
			$result = substr($generator, (rand() % (strlen($generator))), $n);
		}
		
		// Returning the result 
		return $result;
	}//ends generateNumericOTP()


	



	/**
     * Upload the profile picture image
     *
     * @param        $file
     * @return string|void
     */
    public static function uploadProfilePicture($file)
    {
        $name = token();
        $extension = $file->guessClientExtension();

        $filename = $name . '.' . $extension;
        $imageTmp = Image::make($file->getRealPath());

        if (!$imageTmp) {
            return notify()->error('Oops', 'Something went wrong', 'warning shake animated');
        }

        $path = upload_path_images();

        // save the image
        $imageTmp->fit(250, 250)->save($path . $filename);

        return $filename;
    }



}// end LoginValidationHelper
