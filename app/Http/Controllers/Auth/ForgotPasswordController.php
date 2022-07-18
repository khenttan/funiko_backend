<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

use App\Helpers\CustomHelper;

use Auth,View,Hash,Validator,Redirect;

class ForgotPasswordController extends AuthController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * validate string
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function resetPassword($validateString = ''){
       
        if(Auth::check()){
            return Redirect::route('account.myProfile');
        }
        if($validateString!="" && $validateString!=null){

            $userDetail =   \App\Models\User::where('forgot_password_validate_string',$validateString)->first();
            if(!empty($userDetail)){
        
                return view('auth.passwords.reset',compact('validateString'));
            }else{
                Toastr::error('Sorry you are using wrong link:)','error');
                return Redirect::route('login');
            }
        }else{
            Toastr::error('Sorry you are using wrong link:)','error');
            return Redirect::route('login');
        }
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail( Request $request )
    {     
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.

        $email      =   $request->email??'';
        $userDetail =   \App\Models\User::where('email',$email)->first();
        
        if(!empty($userDetail)){
          
            if($userDetail->isAdmin()){
                $forgot_password_validate_string    =   md5($userDetail->email);
                \App\Models\User::where('email',$email)->update(array('forgot_password_validate_string'=>$forgot_password_validate_string));
                
                 //mail sent to user
                $to             =   $userDetail->email;
                $to_name        =   ucwords($userDetail->full_name);
                $validateString =   $forgot_password_validate_string;
                $full_name      =   $to_name;
                $route_url      =   route('user.resetpassword', $validateString);
                $varify_link    =   $route_url;
                $action         =   "forgot_password";
              
                //forgot password mail to user
                $rep_Array = array($full_name, $varify_link, $route_url);
                CustomHelper::callSendMail($to,$to_name,$rep_Array,$action);

                toastr()->info('Forgot verification link has been sent to '.$email.' . please click on that link and change your password:)','info');
                return redirect()->back();

            }else{
                toastr()->error('This feature is only for admin.', 'error');
                return redirect()->back();
            }
        }else{
            toastr()->error('Your email ('.$email.') is not found in our system','error');
            return redirect()->back();
        }
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $request->wantsJson()
                    ? new JsonResponse(['message' => trans($response)], 200)
                    : back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)]);
    }



    
    /**
     * validate string
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function resetPasswordUpdate($validateString = '',Request $request){

        $formData   =   $request->all();
        
        /* define validatation messages */
        $message    =   array(
            'password.required'                 =>  trans('Please enter password.'),
            'password.regex'                    =>  trans('front_messages.password.regex_message'),
            'password_confirmation.required'    =>  trans('Please enter confirm password.'),
            'password_confirmation.same'        =>  trans('Confirm password should be same as password.'),
        );
        
        /* define validation */
        $validate = array(
            //'new_password'                  =>  'required|regex:'.PASSWORD_REGX,
            'password'                  =>  'required',
            'password_confirmation'     =>  'required|same:password',
        );

        $validator                          =   Validator::make($formData, $validate, $message);
        
        // Check Validation
        if ($validator->fails()){ 
            return Redirect::back()->withErrors($validator)->withInput();
        }
        
        $newPassword        =   $formData['password'];
        $newPassword        =   Hash::make($newPassword);
        $userInfo           =   \App\Models\User::where('forgot_password_validate_string',$validateString)->first();
        if(isset($userInfo) && !empty($userInfo)){
            \App\Models\User::where('forgot_password_validate_string',$validateString)->update(array(
                'password'                          =>  $newPassword,
                'forgot_password_validate_string'   =>  ''
            ));
            /*$response = array('status'=>"success",'data'=>['userRoleId'=> $userInfo->user_role_id,'email'=>$userInfo->email,'full_name'=>ucwords($userInfo->full_name)]);*/


            /*if(!empty($response['data']['email'])){
                $to             =   $response['data']['email'];
                $to_name        =   $response['data']['full_name'];
                $action         =   "reset_password";
                $rep_Array      =   array($to_name); 
                $this->callSendMail($to,$to_name,$rep_Array,$action);
            }*/

            //mail email and password to new registered user
            $to             =   $userInfo->email;
            $to_name        =   ucwords($userInfo->name);
            $full_name      =   $to_name;
            $action         =   "reset_password";
            $rep_Array      =   array($full_name); 
            CustomHelper::callSendMail($to,$to_name,$rep_Array,$action);

            Toastr::success('Your password has been reset successfully.:)','success');
            return Redirect::route('login');
        }else{
            Toastr::error('Your link has been expired or you are using wrong link.:)','error');
            return Redirect::route('login');
        }
    }
}
