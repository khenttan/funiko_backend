<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Role;
use App\Models\User;
use App\Models\PostVideo;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Admin\AdminController;
use App\Helpers\CustomHelper;
use Redirect;
use App\Libraries\LoginValidationHelper;
use Illuminate\Http\Request;

class ClientsController extends AdminController
{
    /**
     * Display a listing of client.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        save_resource_url();

        $items = User::with('city')->orderBy('created_at', 'desc')->get();

        return $this->view('accounts.clients.index')->with('items', $items);
    }

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::getAllLists();

        return $this->view('accounts.clients.create_edit')->with('roles', $roles);
    }

    /**
     * Store a newly created client in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        $formData   =   request()->all(); 
        $from       =   'admin';
        $type       =   'add';
        $model      =   'User';
        $attributes =   array('type'=>$type,'model'=>$model,'from'=>$from,'userId'=>'');
        $response   =   LoginValidationHelper::userSignUp($formData,$attributes);

        if($response['status']=="error"){
            return Redirect::back()->withErrors($response['validator'])->withInput();
        }else{
            notify()->success('Successfully',trans("User Registered SuccessFully"));
            return redirect_to_resource();
        }
    }

    /**
     * Display the specified client.
     *
     * @param User $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $client = User::where('id',$id)->with(['city','state','country','interest.interestCategory','Myfollwer.follower:id,username,image','myFollowing.following:id,username,image','totalPost'])->with(array('totalPost' => function($query) {
            $query->withCount(['comments','postLike','postShare'])->with('tags','postView')->where('is_delete',0);
        }))->first();
        $apps = $client->totalPost()->paginate(9);
        $client =  $client->toArray();
        // dd($client);
        return $this->view('accounts.clients.show')->with('item',$client)->with('apps',$apps);
    }

    /**
     * Show the form for editing the specified client.
     *
     * @param User $client
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $client)
    {
        $roles = Role::getAllLists();

        return $this->view('accounts.clients.create_edit')
                    ->with('roles', $roles)
                    ->with('item', $client);
    }

    

    /**
     * Update the specified client in storage.
     *
     * @param User $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(User $client)
    {
        // $rules = User::$rulesClient;
        // $rules['email'] = [
        //     'required',
        //     'string',
        //     'email',
        //     'max:191',
        //     Rule::unique('users')->ignore($client->id)
        // ];
        // $rules['username'] = [
        //     'required',
        //     'string',
        //     'max:191',
        //     Rule::unique('users')->ignore($client->id)
        // ];
        // $rules['dial_code']  = ['required'];
        // $rules['country_code']  = ['required'];

        // $rules['password'] = ['nullable', 'string', 'min:4', 'confirmed'];
        // $attributes = request()->validate($rules, User::$messages);

        // // $roles = $attributes['roles'];
        // // unset($attributes['roles']);
        // if (strlen($attributes['password']) < 4) {
        //     unset($attributes['password']);
        // } else {
        //     $attributes['password'] = bcrypt($attributes['password']);
        // }
        // $client = $this->updateEntry($client, $attributes);
        
        // return redirect_to_resource();
        $formData   =   request()->all();
        $from       =   'admin';
        $type       =   'update';
        $model      =   'User';
        $attributes =   array('type'=>$type,'model'=>$model,'from'=>$from,'userId'=>$client->id);
        $response   =   LoginValidationHelper::userSignUp($formData,$attributes);

        if($response['status']=="error"){
            return Redirect::back()->withErrors($response['validator'])->withInput();
        }else{
            
          /*  //mail email and password to new registered user
            $to             =   $formData['email'];
            $to_name        =   ucwords($formData['first_name'].' '.$formData['last_name']);
            $full_name      =   $to_name;
            $password       =   $formData['password'];
            $route_url      =   URL::to('login');
            $click_link     =   $route_url;
            $action         =   "user_registration";
            $rep_Array      =   array($full_name,$to,$password,$click_link,$route_url); 
            $this->callSendMail($to,$to_name,$rep_Array,$action);*/
            notify()->success('Successfully',trans("User Details updated successfully!"));
            return redirect_to_resource();
        }
    }

    /**
     * Update the status
     * @param Orders $Orders
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus($id)
    {
        $user=User::where('id',$id)->first();
        if($user->status==config('globalConstant.activate')){
            $user->status=config('globalConstant.deactivate');
        }
        else{
            $user->status=config('globalConstant.activate');
        } 
        $user->save();
        notify()->success('Successfully','Account status updated successfully!');
        return json_response();
    }

    /**
     * Remove the specified client from storage.
     *
     * @param User $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(User $client)
    {
        if($client->id){
            User::where('id',$client->id)->delete();
            notify()->success('Successfully','User deleted Successfully.');
        }
        else{
            notify()->error('error','Something went wrong.');
        }
        return redirect()->back();
    }

    /**
     * Remove the specified client from storage.
     *
     * @param User $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function clientVideoDelete($id)
    {
        PostVideo::where('id',$id)->update(['is_delete'=> 1]);
        notify()->success('Successfully','Video Deleted Successfully.');
        return redirect()->back();
    }

      /**
     * Remove the specified client from storage.
     *
     * @param User $client
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function clientVideoStatus($id,$status)
    {
        PostVideo::where('id',$id)->update(['is_visible'=>$status]);
        if($status == "0"){
            $name = "Deactivate";
        }else{
            $name = "Activate";

        }
        notify()->success('Successfully','Video '. $name .' Successfully.');
        return redirect()->back();
    }
    
}
