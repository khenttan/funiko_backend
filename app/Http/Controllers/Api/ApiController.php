<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Request;
use App\Libraries\CustomHelper;
use App\Libraries\LoginValidationHelper;
use App\Libraries\FeedSetupHelper;
use App\Models\Page;
use App\Models\User;
use App\Models\StoreFeature;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Feed;
use App\Models\DataResponse;
use App\Models\Video;
use Illuminate\Pagination\Paginator;
use App\Models\SubFeature;
use App\Models\ProductFeature;
use Validator,Auth,Config,Hash;
use App\Models\ProductCategory;
class ApiController extends BaseController {
																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																	
	/**
    * Function for api data
    *
    * @param null
    *
    * @return json reponse.
    */
	public function api_output($data = []) {
        if(isset($data['errors']) && !empty($data['errors'])){
            $data['message']	=	'';
           
			$data['message'] = $data['errors'];
            unset($data['errors']);
        }

        if(Request::has('debug') && (Request::post('debug') || Request::post('debug'))) {
            return response()->json($data);
        }
        echo base64_encode(utf8_encode(json_encode($data)));
        die;
    }

    /**
    * Function for api data
    *
    * @param null
    *
    * @return json reponse.
    */
    public function index() { 

        $response       =   [];
        $message        =   trans('messages.api.invalid_request');
        $input_data     =   Request::all();      
        $decordedData   =   false;

        if(isset($input_data)) {

            $decordedData   =   json_decode(base64_decode($input_data['req']),true);

        } else {
            return $this->api_output([
                 'status'=> false,
                 'message' => "Invalid Api."
             ]);
        }


        if(!$decordedData && Request::has('debug') && (Request::post('debug') || Request::post('debug'))) {
            $decordedData   =   json_decode($input_data['req'],true);
        }

        if(is_array($decordedData) && isset($decordedData['method_name']) && $decordedData['method_name'] != '') {
            $requestData    =   $decordedData;
        } else {
           return $this->api_output([
                 'method_name'=> $decordedData['method_name'],
                 'status'=> false,
                 'message' => "Invalid Api."
            ]);
        }

        try {

            $response = $this-> {'_' . $decordedData['method_name']}(isset($decordedData['data'])?$decordedData['data']:[]);
            $dataResponseObj				=	new DataResponse;
            $dataResponseObj->method_name	=	$requestData['method_name'];
            $dataResponseObj->input_data	=	isset($requestData['data'])?json_encode($requestData['data']):'';
            $dataResponseObj->main_req		=	json_encode($requestData);
            $dataResponseObj->response		=	json_encode($response);
            $dataResponseObj->save();
            /*$dataResponseObj				=	new DataResponse;
            $dataResponseObj->method_name	=	$requestData['method_name'];
            $dataResponseObj->input_data	=	isset($requestData['data'])?json_encode($requestData['data']):'';
            $dataResponseObj->response		=	json_encode($response);
            $dataResponseObj->save();*/
            return $this->api_output($response);
        } catch(\Exception $e) {
            return $this->api_output([
                'status'=> false,
                'message' => "Invalid Api.",
                'method' => $requestData['method_name'],
                'actual_message' => str_ireplace("_{$requestData['method_name']}",$requestData['method_name'],$e->getMessage())
            ]);
        }

    }// end index()
  
  
  
	/**
    * Function for user login
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    private function _login($formData){  
		$response       		=   array();
        $attribute['type']		=	'api';
        //pr($formData);
        $response				=	LoginValidationHelper::login($formData,$attribute);
        if($response['status']=="error"){
			$response['errors']	=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
        return $response;
    }// end login()
	
    /**
    * Function for fetching Countries
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    private function _getCountry($formData){  
		$response       		=   array();
        $attribute['type']		=	'api';
        $response['data']=Country::select('id','name')->get();
        $response['status']='success';
        if(empty($response['data']))
		{
			$response['status']='error';
			return $response['error']='no countries found';
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
    private function _getStates($formData){  
		$response       		=   array();
        $attribute['type']		=	'api';
        $response['data']=State::where("country_id",$formData['country_id'])
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
    private function _getCities($formData){  
		$response       		=   array();
        $attribute['type']		=	'api';
        $response['data']=City::where("state_id",$formData['state_id'])
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
    * Function for user registration
    *
    * @param $formData as formData
    *
    * @return reponse.
    */
    private function _userSignUp($formData){  
		//dd($formData);
		$from		=	'api';
		$model		=	'User';
		$attributes	=	array('model'=>$model,'from'=>$from);

	
		$response	=	LoginValidationHelper::userSignUp($formData,$attributes);

		if($response['status']=="error"){
			$response['errors']	=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Registraion successful';
		}
		return $response;
    }// end userSignUp()


	/**
	 * Function for user forgot password
	 *
	 * @param $formData as formData
	 *
	 * @return reponse.
	 */
	private function _forgotPassword($formData)
	{

		$response   = array();
		$errorArray = array();
		$from		=	'api';
		$model		=	'User';
		$attribute	=	array('model' => $model, 'from' => $from);
		$response	=	LoginValidationHelper::userForgetPassword($formData, $attribute);
		if ($response['status'] == "error") {
			if (isset($response['validator']) && !empty($response['validator'])) {
				$response['errors']	=	$response['validator']->errors()->toArray();
				unset($response['validator']);
			} else {
				$response['errors']	=	$response['data']['message'];
			}
		} else {
			if($formData['method_verify']=='email'){
                $response['message']	=	trans('Otp is sent on your registered email successfully');    
            }
            else{
                $response['message']	=	trans('Otp is sent on your registered mobile no. successfully');    
            }
		}
		return $response;
	} // end _forgotPassword()

	/**
	 * Function for user otp verify
	 *
	 * @param $formData as formData
	 *
	 * @return reponse.
	 */

	private function _verifyOtp($formData)
	{
		$response       			=	array();
		$from						=	'api';
		$model						=	'User';
		$pageSlug					=	'';
		$attribute					=	array('model' => $model, 'from' => $from, 'pageSlug' => $pageSlug);
		$response					=	LoginValidationHelper::userVerifyOtp($formData, $attribute);
		
        if ($response['status'] == "error") {
			if (isset($response['validator']) && !empty($response['validator'])) {
				$response['errors']	=	$response['validator']->errors()->toArray();
				unset($response['validator']);
			} else {
				$response['errors']	=	$response['data']['message'];
				unset($response['data']);
			}
		} else {
            if($formData['method_verify']=='email'){   
                $response['message']	=	trans('Email Address is verified successfully');    
            }
            else{
                $response['message']	=	trans('Mobile Number is verified successfully');    
            }
		}
		return $response;
	} // end _verifyOtp()

	/**
	 * Function for user otp verify
	 *
	 * @param $formData as formData
	 *
	 * @return reponse.
	 */

	private function _regSteps($formData)
	{
		$response       			=	array();
		$from						=	'api';
		$model						=	'User';
		$pageSlug					=	'';
		$attribute					=	array('model' => $model, 'from' => $from, 'pageSlug' => $pageSlug);
		$response					=	LoginValidationHelper::regSteps($formData, $attribute);
			
        if ($response['status'] == "error") {
			if (isset($response['validator']) && !empty($response['validator'])) {
				$response['errors']	=	$response['validator']->errors()->toArray();
				unset($response['validator']);
			} else {
				$response['errors']	=	$response['data']['message'];
				unset($response['data']);
			}
		} 
		return $response;
	} // end _verifyOtp()

    

	 /**
     * Function for save change passwaord page
     * @param null
     * @return null
    */
    public function _changePassword($formData) {
        $response                   =   array();
        
        /* define validatation messages */
        $message = array(
            'current_password.required'         =>  trans('Please enter your current password.'),
            'password.required'                 =>  trans('Please enter your password.'),
            'confirm_password.same'             =>  trans('Password does not match'),
        );
        /* define validation */
        $validate = array(
            'current_password'          =>  'required',
            'password'                  =>  'required',
            'confirm_password'          =>  'same:password',
        );
        $validator          =   Validator::make($formData, $validate, $message);

        if ($validator->fails()){ 
            $response = array('status'=>"error",'message'=>$validator->errors()->toArray());
            return $response;
        }       
        $currentPassword    =   $formData['current_password'];
        $newPassword        =   $formData['password'];
        $newPassword        =   Hash::make($newPassword);
        $userId             =   $formData['user_id'];
        $userInfo           =   User::where('id',$userId)->first();


        if(isset($userInfo) && !empty($userInfo)){                    
           // dd($currentPassword, $userInfo->password);
            if (Hash::check($currentPassword, $userInfo->password)) { 
                $userInfo->update(array('password' => $newPassword));
                $response = array(
                    'status'    =>  "success",
                    'message'   =>  'Your password has been changed successfully now you can login with your new password.'
                );
                return $response;
            } else {
                return $response = array('status'=>"error",'message'=>'Password does not match');
            }
        }else{
            return $response = array('status'=>"error",'message'=>'Something went wrong.');
        }
    }//end changePassword()


	/**
	 * Function for user otp verify
	 *
	 * @param $formData as formData
	 *
	 * @return reponse.
	 */
	private function _verifyForgotOtp($formData)
	{
		$response       			=	array();
		$from						=	'api';
		$model						=	'User';
		$pageSlug					=	'forgot_password';
		$attribute					=	array('model' => $model, 'from' => $from, 'pageSlug' => $pageSlug);
		$response					=	LoginValidationHelper::userVerifyOtp($formData, $attribute);
		if ($response['status'] == "error") {
			if (isset($response['validator']) && !empty($response['validator'])) {
				$response['errors']	=	$response['validator']->errors()->toArray();
				unset($response['validator']);
			} else {
				$response['errors']	=	$response['data']['message'];
				unset($response['data']);
			}
		} else {
            if($formData['method_verify']=='email'){
                $response['message']	=	trans('Your OTP sent on your Email Address is verified successfully');    
            }
            else{
                $response['message']	=	trans('Your OTP sent on your Mobile Number is verified successfully');    
            }
			//$response['message']	=	trans("front_messages.otp_success_message");
		}
		return $response;
	} // end _verifyForgotOtp()

	/**
	 * Function is used for save reset password
	 *
	 * @param $formData as form data
	 *
	 * @return view page. 
	 */

	public function _resetPasswordSave($formData)
	{
		$from		=	'api';
		$model		=	'User';
		$attribute	=	array('model' => $model, 'from' => $from);
		$response	=	LoginValidationHelper::userResetPasswordValidationAndSave($formData, $attribute);
		if ($response['status'] == "error") {
			if (isset($response['validator']) && !empty($response['validator'])) {
				$response['errors']	=	$response['validator']->errors()->toArray();
				unset($response['validator']);
			} else {
				unset($response['data']);
				$response['errors']	=	trans('Sorry you are using wrong otp.');
			}
		} else {
			//reset password mail to user
			if (!empty($response['data']['email'])) {
            //reset password mail to user
			}
			unset($response['data']);
			$response['message']	=	trans('Your password has been reset successfully!.');
		}
		return $response;
	} // end _resetPasswordSave()	

	/**
	 * function for resend otp in register
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _resendOtp($formData)
	{
		$formData['data']		=	$formData['otp_verify_invisible_slug']??'';
		if($formData['method_verify']=='email'){
			$formData['email']=$formData['data'];
		}
		else{
			$formData['mobile']=$formData['data'];
		}

		$from						=	'api';
		$model						=	'User';
		$attribute					=	array('model' => $model, 'from' => $from);
        if(isset($formData['user_id'])){
        	$response					=	LoginValidationHelper::userResendOtpProfile($formData, $attribute);	
        }
        else{
        	$response					=	LoginValidationHelper::userResendOtp($formData, $attribute);	
        }
        
		if ($response['status'] == "error") {
			$response['errors']		=	trans('Sorry you are using wrong link');
		} else {
			$response['message']	=	trans("Otp has been sent.");
		}
		return $response;
	} //end _resendOtp()

	
	/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _createLike($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);

		$response	=	FeedSetupHelper::createLikeDislike($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
			
		}
		return $response;
	} //end _createShop()


	/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _createComment($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::createComment($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
		}
		return $response;
	} //end _createShop()


		/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _deleteComment($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::deleteComment($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 

			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Comment has been deleted successfully.';
		}
		return $response;
	} //end _createShop()

		/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _deleteThread($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::deleteThread($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Comment has been deleted successfully.';

		}
		return $response;
	} //end _createShop()


/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _createThread($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::createThread($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
		}
		return $response;
	} //end _createShop()



	/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _getComments($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);

		$response	=	FeedSetupHelper::getComments($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
		}
		return $response;
	} //end _createShop()

	/**
	 * function for creation of like
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _getThreads($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		
		$response	=	FeedSetupHelper::getThreads($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
		}
		return $response;
	} //end _createShop()




	/**
	 * function for creation of shop by seller
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _createFeed($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		if(Request::hasFile('media')){
			$file=	Request::file('media');
			$formData['media']	=	$file;
		}
		$response	=	FeedSetupHelper::createFeed($formData,$attributes);
	
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else if($response['status']=="file_error"){
			$response['status']		=	'error';
			$response['message']	=	'Your file format is invalid.';
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Your feed is successfully created.';
		}
		return $response;
	} //end _createShop()




	/**
	 * function for creation of shop by seller
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _editFeed($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		if(Request::hasFile('media')){
			$file=	Request::file('media');
			$formData['media']	=	$file;
		}
		$response	=	FeedSetupHelper::editFeed($formData,$attributes);
			
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else if($response['status']=="file_error"){
			$response['status']		=	'error';
			$response['message']	=	'Your file format is invalid.';
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Your feed is successfully created.';
		}
		return $response;
	} //end _createShop()

	/**
	 * function for deletion of feed
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _deleteFeed($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::deleteFeed($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 

			unset($response['validator']);
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Feed has been deleted successfully.';
		}
		return $response;
	} //end _createShop()


	/**
	 * function for creation of shop by seller
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _getFeedsListing($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::getFeedsListing($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else if($response['status']=="file_error"){
			$response['status']		=	'error';
			$response['message']	=	'Your file format is invalid.';
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Your feed is successfully created.';
		}
		return $response;
	} //end _createShop()
	/**
	 * function for creation of shop by seller
	 *
	 * @param $formData as form data
	 *
	 * @return mail
	 */
	public function _getUserFeedsListing($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		$response	=	FeedSetupHelper::getUserFeedsListing($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		else if($response['status']=="file_error"){
			$response['status']		=	'error';
			$response['message']	=	'Your file format is invalid.';
		}
		else{
			$response['status']		=	'success';
			$response['message']	=	'Your feed is successfully created.';
		}
		return $response;
	} //end _createShop()
	

	

	/**
	 * Function for get profile
	 * @param null
	 * @return null
	 */
	public function _getProfileDetails($formData)
	{
		
		$userId  	=	$formData['user_id'];
		$message  	=	'';
		if (isset($userId) && $userId ==	'') {
			return array('status' => "error", 'message' => 'No record found');
		}
		$userDetails 	=	CustomHelper::getUserDetails($userId);
		if ($userDetails['status'] 	==	"error") {
			return array('status' => "error", 'message' => 'No record found');
		}
		return $userDetails;
	} // end getProfileDetails()

	/**
	 * Function for get profile
	 * @param null
	 * @return null
	 */
	public function _getTop($formData)
	{
		$from		=	'api';
		$model		=	'Feed';
		$attributes	=	array('model'=>$model,'from'=>$from);
		
		$response	=	FeedSetupHelper::getTop($formData,$attributes);
		
		if($response['status']=="error"){
			$response['errors']		=	$response['validator']->errors()->toArray(); 
			unset($response['validator']);
		}
		
		else{
			$response['status']		=	'success';
			
		}
		return $response;


		

		
	} // end getProfileDetails()

	/**
	 * Function for edit profile
	 * @param null
	 * @return response
	 */
	public function _editUpdateProfile($formData)
	{
		$from		=	'apiupdate';
		$model		=	'User';
		$type		=	'edit';
		$attributes	=	array('model' => $model, 'from' => $from, 'type' => $type, 'userId' => $formData['user_id']);
		

		if(Request::hasFile('image')){
			$file    =	Request::file('image');
			$formData['image']	=	$file;
		}

		$response	=	LoginValidationHelper::userSignUp($formData, $attributes);
		
		if ($response['status'] == "error") {
			$response['errors']	=	$response['validator']->errors()->toArray();
			unset($response['validator']);
		} else {
			$response['status']		=	"success";
			$response['message']	=	trans("Profile Data updated succeefully.");
			//$response['data']		=	User::where('id',$formData['user_id'])->first()->toArray();
			
			$userDetails 			=	CustomHelper::getUserDetails($formData['user_id']);
			if ($userDetails['status'] 	==	"error") {
				$response['data']	=	[];
			}
			$response['data']	=	$userDetails;

			/*if(!isset($response['data']['image']) || empty($response['data']['image']) || !file_exists(USER_PROFILE_IMAGE_ROOT_PATH . $response['data']['image'])){
				$response['data']['image']	=	'';
			}

			$response['data']['image_url']		=	WEBSITE_IMG_FILE_URL . '?&cropratio=1:1&zc=2&height=130px&width=130px&image=' . USER_IMG_ONTHEFLY_PATH;
			$response['data']['no_image_url']		=	WEBSITE_IMG_FILE_URL . '?&cropratio=1:1&zc=2&height=130px&width=130px&image=' . WEBSITE_IMG_URL . 'admin/no-user-image.png';*/
		}
		return $response;
	} //end editProfile()


	/**
	 * Function for get Features
	 * @param null
	 * @return null
	 */
	public function _getFeatures($formData)
	{
		$featuresDetails		=	StoreFeature::getAllList();
		if(empty($featuresDetails)){
			$response['status']		=	"error";
			$response['features']	=	"no features exist";	
		}
		else{
			$response['status']		=	"success";
			$response['features']	=	$featuresDetails;
		}
		return $response;
	 } // end getFeatures()


	/**
	 * Function for getSubFeatures
	 * @param null
	 * @return null
	 */
	public function _getSubFeatures($formData)
	{
		$id						=	$formData['id']??'';
		$subFeatures 			= 	StoreFeature::with('subfeatures')->find($id);
		if(!empty($formData['search_value']) && !empty($formData['features_id'])){
			//dd($formData['search_value'],$formData['features_id']);
			$result = 	SubFeature::where('name', 'LIKE', '%'. $formData['search_value'] . '%')->where('type_id',$formData['features_id'])->where('deleted_at',NULL)->select('name','id')->get();
			if(count($result)){
				$response['status']		=	"success";
				$response['features']	=	$result;
			}
			else
			{
				$response['status']		=	"error";
				$response['features']	=	"no subfeatures exist";	
			}
		}


		if(empty($subFeatures)){
			$response['status']		=	"error";
			$response['features']	=	"no subfeatures exist";	
		}
		else{
			$response['status']		=	"success";
			$response['features']	=	$subFeatures;
		}
		return $response;
	 } // end getSubFeatures()

	 /**
	 * Function for get Categories
	 * @param null
	 * @return null
	 */
	public function _getCategories($formData)
	{
		$categoriesDetails		=	ProductCategory::getAllList();
		if(empty($categoriesDetails)){
			$response['status']		=	"error";
			$response['categories']	=	"no categories exist";	
		}
		else{
			$response['status']		=	"success";
			$response['categories']	=	$categoriesDetails;
		}
		return $response;
	 } // end getCategories()


	/**
	 * Function for getMainCategories
	 * @param null
	 * @return null
	 */
	public function _getMainCategories($formData)
	{
		$categoriesDetails		=	ProductCategory::getMainList();
		if(empty($categoriesDetails)){
			$response['status']		=	"error";
			$response['categories']	=	"no categories exist";	
		}
		else{
			$response['status']		=	"success";
			$response['categories']	=	$categoriesDetails;
		}
		return $response;
	 } // end getMainCategories()



	/**
	 * Function for getMainCategories
	 * @param null
	 * @return null
	 */
	public function _getSubcategories($formData)
	{		if(empty($categoriesDetails)){
			$response['status']		=	"error";
			$response['subcategories']	=	"no categories exist";	
		}
		else{
			$response['status']		=	"success";
			$response['subcategories']	=	$categoriesDetails;
		}
		return $response;
	 } // end getMainCategories()


	 /**
     * Function for get Banner List
     * @param $formData as formdata
     * @return response
     */
    public function _getBannerList($formData) {

        $records            =   \App\Models\Banner::get()->toArray();

		if(count($records) > 0){
            foreach ($records as $key => $activity) {
                if(isset($activity['image']) && !empty($activity['image']) && file_exists(config('globalConstant.shop_photo_path').$activity['image'])){
                    $records[$key]['image']     =     config('globalConstant.image_path').$activity['image'];
                }else{
                    $records[$key]['image']     =   "";
                }
            }
        }
       
        $response['data']       =   $records;
        $response['status']     =   'success';
        $response['message']    =   '';
        return $response;
    }//end getBannerList()



	 /**
     * Function for get validate otp 
     * @param $formData as formdata
     * @return response
     */
    public function _otpVarification($formData) {
    	return $formData;

    }
    





}	