<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostVideo;
use App\Models\Group;
use App\Http\Controllers\Controller;

class DashboardController extends AdminController
{
	public function index()
	{
		if(request()->isMethod('get')){
			$items = User::with('city')->orderBy('created_at', 'desc')->get();
			$user_count = User::count();    
			$post_count = PostVideo::where('is_delete',0)->count();    
			$group_count = Group::count();    
			$users_data = User::select('id','username')->withCount('Myfollwer')->orderBy('myfollwer_count', 'DESC')->limit(10)->get()->toArray(); 
			$post_videos = PostVideo::with('postViewOne','user:id,username')->where('is_delete',0)->limit(10)->get()->sortByDesc('postViewOne.post_count')->toArray();  
		}else{

			if(!isset(request()->gender)){
				$from = date(request()->active_from);
				$to = date(request()->active_to);
				$items = User::with('city')->whereBetween('created_at', [$from, $to])->orderBy('created_at', 'desc')->get();
				$user_count = User::whereBetween('created_at', [$from, $to])->count();    
				$post_count = PostVideo::where('is_delete',0)->whereBetween('created_at', [$from, $to])->count();    
				$group_count = Group::whereBetween('created_at', [$from, $to])->count();    
				$users_data = User::select('id','username')->withCount('Myfollwer')->orderBy('myfollwer_count', 'DESC')->limit(10)->get()->toArray(); 
				$post_videos = PostVideo::with('postViewOne','user:id,username')->where('is_delete',0)->limit(10)->get()->sortByDesc('postViewOne.post_count')->toArray();
				return $this->view('dashboard',compact('user_count','post_count','group_count','users_data','post_videos'))->with('old_inputs', request()->all());

			
			}else{
				$from = date(request()->active_from);
				$to = date(request()->active_to);

				$gender = request()->gender;
				$items = User::with('city')->whereBetween('created_at', [$from, $to])->orderBy('created_at', 'desc')->where('gender',request()->gender)->get();
				$user_count = User::whereBetween('created_at', [$from, $to])->where('gender',request()->gender)->count();    
				$post_count = PostVideo::where('is_delete',0)->whereBetween('created_at', [$from, $to])->whereHas('user', function($q) use ($gender)
				{
					$q->where('gender',$gender);
				})->count();    
				$group_count = Group::whereBetween('created_at', [$from, $to])->count();    
				$users_data = User::select('id','username')->withCount('Myfollwer')->orderBy('myfollwer_count', 'DESC')->where('gender',request()->gender)->limit(10)->get()->toArray(); 
				$post_videos = PostVideo::with('postViewOne','user:id,username')->where('is_delete',0)->limit(10)->get()->sortByDesc('postViewOne.post_count')->toArray();
				return $this->view('dashboard',compact('user_count','post_count','group_count','users_data','post_videos'))->with('old_inputs', request()->all());

			}
	
		}
		return $this->view('dashboard',compact('user_count','post_count','group_count','users_data','post_videos'));

    
		
	
	
	}
}
