<?php
namespace App\Http\Controllers\Admin\EmailTemplate;

use App\Http\Controllers\Admin\AdminController;
use App\Models\EmailTemplate;
use App\Models\EmailAction;
use Illuminate\Http\Request;

use Blade,Config,DB,Input,Redirect,Session,View,Validator,Str;

/**
 * EmailTemplateController class
 *
 * Add your methods in the class below
 *
 * This file will render views from views/admin/emailtemplates
 */
class EmailTemplateController extends AdminController {
	
	public $model	=	'EmailTemplate';
	

	
	/**
	 * Function for display list of all email templates
	 *
	 * @param null
	 *
	 * @return view page. 
	 */

	public function listTemplate(){
		
		save_resource_url();
        return $this->view('email_template.index')->with('items', EmailTemplate::all());
		
	}// end listTemplate()

	/**
	 * Function for display page for add email template
	 *
	 * @param null
	 *
	 * @return view page. 
	*/
	public function addTemplate(){
		$actionOptions	=	EmailAction::pluck('action','action')->toArray();
		return  $this->view("email_template.create_edit",compact('actionOptions'));
	}// end addTemplate()
	
	 /**
	 * Function for display save email template
	 *
	 * @param null
	 *
	 * @return redirect page. 
	 */
	public function saveTemplate(Request $request,$Id=0){
		$validator = Validator::make(
			$request->all(),
			array(
				'name' 			=> 'required',
				'subject' 		=> 'required',
				'action' 		=> 'required',
				'body' 			=> 'required'
			),
			array(
				'name.required'	=> 'Please enter name.',
				'subject.required' 	=> 'Please enter subject.',
				'action.required' 	=> 'Please select action.',
				'body.required' 	=> 'Please enter email body.',
				
			)
		);
		if ($validator->fails()){
			return Redirect::back()
				->withErrors($validator)->withInput();	
		}else{		
			$obj			=	new EmailTemplate();
			if($Id){
				$obj		=	EmailTemplate::find($Id);
			}	
			
			$obj->name		=	ucfirst($request->name);
			$obj->subject	=	ucfirst($request->subject);
			$obj->body		=	$request->body;
			$obj->action	=	$request->action;
			if(!$Id){
				$obj->slug		=	Str::slug($obj->name, '-');
			}
			$obj->save();
			
			//$this->createEntry(Banner::class, $attributes);
			if(!$Id){
				$messge = 'New Email template has been created Successfully.';
			}else{
				$messge = 'Email template has been updated Successfully.';
				
			}
			notify()->success('Successfully',$messge);
			return redirect_to_resource();
			//Session::flash('flash_notice',  trans("messages.$this->model.added_message")); 
			//return Redirect::route("$this->model.index");
		}
	}//  end saveTemplate()

	/**
	 * Function for display page for edit email template page
	 *
	 * @param $Id as id of email template
	 *
	 * @return view page. 
	 */
	public function editTemplate($modelId,Request $request){
		$actionOptions	=	EmailAction::pluck('action','action')->toArray();
		$item			=	EmailTemplate::find($modelId);
		
		### breadcrumbs End ###
		if ($request->old() != null) {
	        $item->name = $request->old('name');
	        $item->subject = $request->old('subject');  
	        $item->body = $request->old('body');           
	    }
	    return  $this->view("email_template.create_edit",compact('actionOptions','item'));
	} // end editTemplate()

	/**
	 * Function for update email template
	 *
	 * @param $Id as id of email template
	 *
	 * @return redirect page. 
	 */
	public function updateTemplate($Id=0){
		
		$validator = Validator::make(
			Input::all(),
			array(
				'name' 			=> 'required',
				'subject' 		=> 'required',
				'body' 			=> 'required'
			),
			array(
				'name.required'	=> 'Please enter name.',
				'subject.required' 	=> 'Please enter subject.',
				'body.required' 	=> 'Please enter email body.',
			)
		);
		if ($validator->fails()){
			return Redirect::back()
				->withErrors($validator)->withInput();	
		}else{
			
			$obj			=	EmailTemplate::find($Id);
			$obj->name		=	ucfirst(Input::get('name'));
			$obj->subject	=	ucfirst(Input::get('subject'));
			$obj->body		=	Input::get('body');
			$obj->save();
			
			Session::flash('flash_notice',  trans("messages.$this->model.updated_message")); 
			return Redirect::route("$this->model.index");
		}
	} // end updateTemplate()
	
	/**
	* Function for get all  defined constant  for email template
	*
	* @param null
	*
	* @return all  constant defined for template. 
	*/
	public function getConstant(Request $request){

		if($request->ajax()){
			$actionName 	= 	$request->action;
			$options		= 	EmailAction::where('action', '=', $actionName)->pluck('option','action'); 
			$a = explode(',',$options[$actionName]);
			echo json_encode($a);
		}
		exit;
	}// end getConstant()
	
	
	/**
     * Remove the specified template from storage.
     *
     * @param User $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    */
	public function destroy($id){
		$emailTemplate = EmailTemplate::find($id);
		$this->deleteEntry($emailTemplate, request());
        return redirect_to_resource();
	}


	/**
	 * Function for delete multiple template
	 *
	 * @param null
	 *
	 * @return view page. 
	 */
	public function performMultipleAction(){
		if(Request::ajax()){
			$actionType = ((Input::get('type'))) ? Input::get('type') : '';
			if(!empty($actionType) && !empty(Input::get('ids'))){
				if($actionType	==	'delete'){
					EmailTemplate::whereIn('id', Input::get('ids'))->delete();
					Session::flash('success', trans("messages.global.action_performed_message")); 
				}
			}
		}
	}//end performMultipleAction()
	
}// end EmailTemplateController()
