<?php
namespace App\Helpers;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use App\Models\EmailTemplate;
use App\Models\EmailAction;
use App\Models\Otp;
use Redirect,Session,Mail,DB,Auth;
use App\Models\Role;
use Twilio\Rest\Client;
/**
 * CustomHelper Helper
 *
 * Add your methods in the class below
*/
class CustomHelper {

 	/** 
	 * Function to website limit text
	 *
	 * @param string $text    as string
	 * @param string $limit   as limit text
	 * @return text
	*/
    public static function limit_text($text, $limit) {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]);
        }
        return $text;
    }
    

    /** 
	 * Function to send email form website
	 *
	 * @param string $to             as reciver email address
	 * @param string $toName      	 as full name of receiver
	 * @param array $rep_Array       as array of constant values
	 * @param string $action  		 as action name
	 * @param array $attributes   	 as passed attributes if any like(subject,from,fromName,files,path,attachmentName), default blank
	 * @return void
	*/
	public static function callSendMail($to, $toName, $rep_Array, $action, $attributes = array()) {
		$emailActions	= EmailAction::where('action','=',$action)->get()->toArray();
        $emailTemplates	= EmailTemplate::where('action','=',$action)->get(array('name','subject','action','body'))->toArray();
		$cons 			= explode(',',$emailActions[0]['option']);
		$constants 		= array();
		
		foreach($cons as $key => $val){
			$constants[] = '{'.$val.'}';
		} 
		
		//replace constant by values
		$messageBody	= str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
		//set attributes if any
		$subject 		= (isset($attributes['subject']) && !empty($attributes['subject'])) ? $attributes['subject'] : $emailTemplates[0]['subject'];
		$from			=	(isset($attributes['from']) && !empty($attributes['from'])) ? $attributes['from'] : Session::get('titan.settings.email');
		$fromName		=	(isset($attributes['fromName']) && !empty($attributes['fromName'])) ? $attributes['fromName'] : 'Recycle';
		
		$files			=	(isset($attributes['files']) && !empty($attributes['files'])) ? $attributes['files'] : false;
		$path			=	(isset($attributes['path']) && !empty($attributes['path'])) ? $attributes['path'] : '';
		$attachmentName	=	(isset($attributes['attachmentName']) && !empty($attributes['attachmentName'])) ? $attributes['attachmentName'] : '';
				
		self::sendMail($to, $toName, $subject, $messageBody, $from, $fromName,   $files, $path, $attachmentName); 
    }//end callSendMail()
    
	/** 
	 * Function to send email form website
	 *
	 * @param string $to             as reciver email address
	 * @param string $toName      	 as full name of receiver
	 * @param string $subject      	 as subject
	 * @param array $messageBody  	 as message body
	 * @param string $from   		 as sender email address
	 * @param string $fromName  	 as full name of sender
	 * @param boolen $files   		 as true if mail have any file attachment, default false
	 * @param string $path   		 as file path
	 * @param string $attachmentName as attached file Name
	 *
	 * @return void
	*/
	public static function sendMail($to, $toName, $subject, $messageBody, $from = '', $fromName = '', $files = false, $path='', $attachmentName='') {
		$data				=	array();
		$data['to']			=	$to;
		$data['fullName']	=	$toName;
		$data['fromName']	=	!empty($fromName) ? $fromName : Session::get('titan.settings.name');
		$data['from']		=	!empty($from) ? $from : 'testbydev.an@gmail.com';
		$data['subject']	=	$subject;

		$data['filepath']		=	$path;
		$data['attachmentName']	=	$attachmentName;
	

		if($files===false){
			$mail = Mail::send('emails.email_template', array('messageBody' => $messageBody), function($message) use ($data) {
                $message->to($data['to'], $data['fullName'])->from($data['from'], $data['fromName'])->subject($data['subject']);
            });
			
		}else{
			if($attachmentName!=''){
				Mail::send('emails.email_template', array('messageBody'=> $messageBody), function($message) use ($data){
					$message->to($data['to'], $data['fullName'])->from($data['from'], $data['fromName'])->subject($data['subject'])->attach($data['filepath'],array('as'=>$data['attachmentName']));
				});
			}else{
				Mail::send('emails.email_template', array('messageBody'=> $messageBody), function($message) use ($data){
					$message->to($data['to'], $data['fullName'])->from($data['from'], $data['fromName'])->subject($data['subject'])->attach($data['filepath']);
				});
			}
		}


		$insertData = array(
			'email_to'	 => $data['to'],
			'email_from' => $data['from'],
			'subject'	 => $data['subject'],
			'message'	 =>	$messageBody,
		);
		if( count(Mail::failures()) > 0 ) {
			$insertData['mail_sent']	=	'fail';
		}else{
			$insertData['mail_sent']	=	'pass';
		}
		DB::table('email_logs')->insert($insertData); 
	}//end sendMail()


	/** 
	 * Function to send login function response 
	 *
	 * @param string $to             as reciver email address
	 * @param string $toName      	 as full name of receiver
	 * @param array $rep_Array       as array of constant values
	 * @param string $action  		 as action name
	 * @param array $attributes   	 as passed attributes if any like(subject,from,fromName,files,path,attachmentName), default blank
	 * @return void
	*/

	public static function loginResponse() {
		$response 	=	array('flag'=>true,'msg'=>"");
		if(Auth::check() && Auth::user()->email_verified_at != NULL){
			Auth::logout();
			$response 	=	array('flag'=>false,'msg'=>"This user is not email verified.");
			return $response;
		}
		return $response;
    }
    
	public static function mobileEmailVerficaton() {
		$response 	=	array('url'=>true,'msg'=>"");
		$arr    	= 	Otp::where('user_id',user()->id)->first();

		if(isset($arr['is_verified_by_phone']) && $arr['is_verified_by_phone'] != 1){
			$otp = CustomHelper::getOtp();
			if(Otp::insert(['user_id'=> user()->id,'otp_phone'=>$otp])){
				CustomHelper::twilio($otp,user()->cellphone);
				$mask_phone = CustomHelper::maskPhoneEmail('phone',user()->cellphone);
				$url = 'phone_otp/'.base64_encode(user()->id);
				$msg = 'Otp sent on '.$mask_phone.' phone number.';
				Auth::logout();
				return $response = array('url'=>$url ,'msg'=>$msg);
			}
			// $response 	=	array('flag'=>false,'msg'=>"This user is not approved by admin.");
			// return $response;
		}elseif(isset($arr['is_verified_by_email']) && $arr['is_verified_by_email'] != 1){
			$otp_code = CustomHelper::getOtp();
			$otp                =   Otp::where("user_id",user()->id)->update(['otp_email'=>$otp_code]);                
			$to                 =   user()->email;
			$to_name            =   ucwords(user()->name);
			$full_name          =   user()->name;
			$otp                =   $otp_code;
			$action             =   "verify_otp";
			$rep_Array          =   array($otp,$full_name,$to); 
			CustomHelper::callSendMail($to,$to_name,$rep_Array,$action); // sending email to user 
			$url = 'email_otp/'.base64_encode(user()->id);
			$msg = 'Otp sent on '.$to;
			Auth::logout();
			return $response = array('url'=>$url ,'msg'=>$msg);
		}else{
			return true;
		}
    }

    public static function objectToArray($branch_manager){
    	foreach ($branch_manager as $key => $value) {
    		prd($value);
    	}
    }
   


    public static function sendVerifficationMail($email,$from=""){
		$userDetail =   \App\User::where('email',$email)->first();

	    if(!empty($userDetail)){
	        if($userDetail->is_approved == (int) config('globalconstants.APPROVED')){
	            $forgot_password_validate_string    =   md5($userDetail->email);
	            \App\User::where('email',$email)->update(array('forgot_password_validate_string'=>$forgot_password_validate_string));
	            
	            //mail sent to user
	            $to             =   $userDetail->email;
	            $to_name        =   ucwords($userDetail->name);
	            $validateString =   $forgot_password_validate_string;
	            $full_name      =   $to_name;
	            $route_url      =   route('user.resetpassword', $validateString);
	            $varify_link    =   $route_url;
	            $action         =   "forgot_password";
	          
	            //forgot password mail to user
	            $rep_Array = array($to_name, $varify_link, $route_url);
	            CustomHelper::callSendMail($to,$to_name,$rep_Array,$action);

	            if(isset($from) && !empty($from)){
	            	return ["status"=>'success','message'=>'Forgot verification link has been sent to '.$email.' . please click on that link and change your password:)'];
	            }else{
	            	Toastr::info('Forgot verification link has been sent to '.$email.' . please click on that link and change your password:)','info');
	            	return redirect()->back();
	            }
	        }else{
	        	 if(isset($from) && !empty($from)){
	            	return ["status"=>'error','message'=>'Your account is not approved by admin when your account is approved by admin then you can perform this action:)'];
	            }else{
	            	 Toastr::info('Your account is not approved by admin when your account is approved by admin then you can perform this action:)','info');
	            	return redirect()->back();
	            }
	           
	        }
	    }else{
	    	return ["status"=>'error','message'=>'Your account is not approved by admin when your account is approved by admin then you can perform this action:)'];
	    }
    }
	/** 
	 * Function to get user Status by id 
	 *
	 * @param User id           
	 * 
	*/

    public static function getStatusById($id){
		$data = \App\Models\User::select('status')->where('id',$id)->first();
    
        if(!empty($data)){
            return $data;
        }
        else{
            return false;
        }
    }

    /** 
	 * Function to send login function response 
	 *
	 * @param string $services all data of services           
	 * 
	*/

    public static function unserializeData($services){
    	$arr = [];    	
    	foreach (unserialize($services) as $key => $value) {
    		$arr[] = $value;
    	}
    	//prd($arr);
    	return $arr;
    }

     /**
     * Upload the banner image, create a thumb as well
     *
     * @param        $file
     * @param string $path
     * @param array  $size
     * @return string|void
     */
    public static function uploadImage(
        UploadedFile $file, $path = '', $size = ['o' => [], 'tn' => []]
    ) {
        $name = token();

        $extension = $file->getClientOriginalExtension();
		$data = getimagesize($file);
        $width = $data[0];
        $height = $data[1];


        $filename = $name . '.' . $extension;
        $filenameThumb = $name . '-tn.' . $extension;
        $imageTmp = Image::make($file->getRealPath());
        if (!$imageTmp) {
            return notify()->error('Oops', 'Something went wrong', 'warning shake animated');
        }
        if(empty($path)){
        	$path = upload_path_images();
        }

        // original
        $imageTmp->save($path . $name . '-o.' . $extension);

        // save the image
          $image = $imageTmp->fit($width, $height)->save($path . $filename);


        $image->fit($width, $height)->save($path . $filenameThumb);

        return $filename;
    }


	
/**
	 * Datatable configuration
	 *
	 * @param req		As	Request Data
	 * @param res		As 	Response Data
	 * @param options	As Object of data have multiple values
	 *
	 * @return json
	 */
 function configDatatable($request,$formData=null){
		$resultDraw		= 	($request->draw)	? $request->draw : 1;
		$sortIndex	 	= 	($request->order && $request->order[0]['column'] != '') 	? 	$request->order[0]['column']		: '' ;
		$sortOrder	 	= 	($request->order && $request->order[0]['dir'] && ($request->order[0]['dir'] == 'asc')) ? 'ASC' :'DESC';
		
		/* Searching  */
		$conditions 		=	[];
		$searchData 		=	($request->columns) ? $request->columns :[];
		if(count($searchData) > 0){
			foreach ($searchData as $index => $record) {
				$fieldName 		= (isset($record['name']) ? $record['name'] : (isset($record['data']) ? $record['data'] : ''));
				$searchValue	= (isset($record['search']) && !empty($record['search']['value'])) ? trim($record['search']['value']) : '';
				$fieldType		= (isset($record['field_type'])) ? $record['field_type'] : '';
				if($searchValue && $fieldName){
					
					if(is_numeric($searchValue)){
						array_push($conditions, [$fieldName , '=',$searchValue]);
					}else{
						$valData    =   '%'.$searchValue.'%';
						array_push($conditions, [$fieldName , 'like',$valData]);
					}
				}
			}
		}

		/* Sorting */
		$sortConditions = [];
		if($sortIndex !=''){
			if($searchData[$sortIndex]){
				$dataVal				=	(isset($searchData[$sortIndex]['data']) ? $searchData[$sortIndex]['data'] : '');
				$orderFieldName 		=   (isset($searchData[$sortIndex]['name']) ? $searchData[$sortIndex]['name'] : $dataVal);
				$proptyType				=	'data';
				if(isset($searchData[$sortIndex]['name']) && $searchData[$sortIndex]['name']){
					$proptyType				=	'name';
				}
				if(isset($searchData[$sortIndex][$proptyType]) && !empty($searchData[$sortIndex][$proptyType])){
					$sortConditions[$searchData[$sortIndex][$proptyType]] = $sortOrder;
				}
			}
		}else{
			$sortConditions['id'] = $sortOrder;
		}
		
		return [
			'sort_conditions' 	=> $sortConditions,
			'conditions' 		=> $conditions,
			'result_draw' 		=> $resultDraw
		];
	
}//End configDatatable()


}	
	// public static function getUserStats($from,$to){
 //        $userStats=\App\User::whereBetween('created_at', [$from, $to])->get();
 //     	return $userStats->toArray();   
	// }


	

