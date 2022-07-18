<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Models\Follower;

class FollowerController extends Controller
{
    public function followAndUnfollow(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            $follower = Follower::where(['follower_id' =>auth('api')->user()->id,
                                        'following_id' => $request['user_id']])->first();
            if (empty($follower)) {
                $follower = new Follower();
                $follower->follower_id = auth('api')->user()->id;
                $follower->following_id = $request['user_id'];
                $follower->save();
                    $userDetails        =   \App\Models\User::where('id',$request['user_id'])->first();
                    $rep_array  =   array();
                    $action     =   'FOLLOWED';
                    $subject = auth('api')->user()->username." started following you";
                    saveNotificationActivity($rep_array,$action,$request['user_id'],$subject);
                return response()->json([
                    'status' => 1,
                    'message' => 'User followed successfully',
                    'data' => $follower
                ]);
            }
            $follower->delete();
            return no_records('User unfollowed successfully');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }
    public function removeFollwer(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            $follower = Follower::where(['follower_id' => $request['user_id'],
                                        'following_id' => auth('api')->user()->id])->first();
            $follower->delete();
            return response()->json([
                "success" => 1,
                "message" => 'User removed successfully',
            ]);
        }  catch (\Exception $exception){
            return error_response($exception);
        }
        
    }

    public function followingList(Request $request){
        try{
            $following_list = Follower::where('follower_id', $request['user_id'] ?? auth('api')->id())->with('following.isFollowings');
            $total_count = $following_list->count();
            $following_list = $following_list->withCount('isFollowingBlockByMe','isFollowingBlockToMe')->paginate(10);
            foreach($following_list as $key => $following_data){
                  if(count($following_data->following->isFollowings->toarray()) == 0  ){
                    $following_list[$key]["followed_by_auth_user"] = 0;
                  }else{
                    $following_list[$key]["followed_by_auth_user"] = 1;
                  }
            }
            if ($following_list->isNotEmpty()){
                return response()->json([
                    'status' => 1,
                    'message' => 'Following data.',
                    'total_count' => $total_count,
                    'data' => $following_list
                ]);
            }
            return no_records('No Records');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }   



    public function searchFollowing(Request $request){

        try {
            $following_list = Follower::where('follower_id', $request['user_id'] ?? auth('api')->id())->with('following.isFollowings')->whereHas('following', function ($query) use($request){
                $query->where('firstname', 'LIKE', '%'.$request['search'].'%');
                $query->orWhere('lastname', 'LIKE', '%'.$request['search'].'%');
                $query->orWhere('username', 'LIKE', '%'.$request['search'].'%');
            });
            $total_count = $following_list->count();
            $following_list = $following_list->paginate(10);
            foreach($following_list as $key => $following_data){
                if(count($following_data->following->isFollowings->toarray()) == 0  ){
                  $following_list[$key]["followed_by_auth_user"] = 0;
                }else{
                  $following_list[$key]["followed_by_auth_user"] = 1;
                }
          }
          if ($following_list->isNotEmpty()){
            return response()->json([
                'status' => 1,
                'message' => 'Following data.',
                'total_count' => $total_count,
                'data' => $following_list
            ]);
        }else{
            return response()->json([
                'status' => 1,
                'message' => 'Following data.',
                'total_count' => $total_count,
                'data' => $following_list
            ]);
        }
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }

    public function followerList(Request $request){
        try{
            $user_id = $request['user_id'] ?? auth('api')->id();
            $follower_list = Follower::with('follower.isFollowings')->where('following_id',$user_id);
            
            $total_count = $follower_list->count();
            $follower_list = $follower_list->withCount('isBlockByMe','isBlockToMe')->paginate(10);
            foreach($follower_list as $key => $following_data){
                if(count($following_data->follower->isFollowings->toarray()) == 0  ){
                  $follower_list[$key]["followed_by_auth_user"] = 0;
                }else{
                  $follower_list[$key]["followed_by_auth_user"] = 1;
                }

          }
            if ($follower_list->isNotEmpty()){
//                foreach ($follower_list as $key => $follower_data){
//                    $follower_list[$key]['is_followed'] = Follower::is_followed($follower_data->follower_id);
//                }

                return response()->json([
                    'status' => 1,
                    'message' => 'Follower data.',
                    'total_count' => $total_count,
                    'data' => $follower_list
                ]);
            }else{
            return response()->json([
                'status' => 1,
                'message' => 'Following data.',
                'total_count' => $total_count,
                'data' => $follower_list
            ]);
          }
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }
    
    public function searchFollower(Request $request){
        try {
            $follower_list = Follower::where('following_id', $request['user_id'] ?? auth('api')->id())->with('follower.isFollowings')->whereHas('follower', function ($query) use($request){
                $query->where('firstname', 'LIKE', '%'.$request['search'].'%');
                $query->orWhere('lastname', 'LIKE', '%'.$request['search'].'%');
                $query->orWhere('username', 'LIKE', '%'.$request['search'].'%');
            });
            $total_count = $follower_list->count();
            $follower_list = $follower_list->paginate(10);
            foreach($follower_list as $key => $follower_data){
                if(count($follower_data->follower->isFollowings->toarray()) == 0  ){
                  $follower_list[$key]["followed_by_auth_user"] = 0;
                }else{
                  $follower_list[$key]["followed_by_auth_user"] = 1;
                }
          }
            return response()->json([
                'status' => 1,
                'message' => 'Following data.',
                'total_count' => $total_count,
                'data' => $follower_list
            ]);
        return no_records('No Records');

        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }

    public function countOfFollowingAndFollower(Request $request){
        try{
            $user_id = $request['user_id'] ?? auth('api')->id();
            $following_list = Follower::with('following:id,full_name,user_name,image')
                ->where('follower_id', $user_id);
            $total_count_following = $following_list->count();

            $follower_list = Follower::with('follower:id,full_name,user_name,image')
                    ->withCount(['isFollowedByUser as is_followed' => function($query) use($user_id) {
                        $query->where('follower_id',$user_id);
                    }])
                    ->where('following_id', $user_id);
            $total_count_follower = $follower_list->count();


            return response()->json([
                'status' => 1,
                'message' => 'Count of Following and Follower data.',
                'total_count_following' => $total_count_following ?? 0,
                'total_count_follower' => $total_count_follower ?? 0,
            ]);
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }

    public function countOfFollower(Request $request){
        try{
            $user_id = $request['user_id'] ?? auth('api')->id();
            $follower_list = Follower::with('follower:id,full_name,user_name,image')
                ->withCount(['isFollowedByUser as is_followed' => function($query) use($user_id) {
                    $query->where('follower_id',$user_id);
                }])
                ->where('following_id', $user_id);
            $total_count = $follower_list->count();
            $follower_list = $follower_list->paginate(10);
            if ($follower_list->isNotEmpty()){
                return response()->json([
                    'status' => 1,
                    'message' => 'Count Of Follower data.',
                    'total_count' => $total_count,
                ]);
            }
            return no_records('No Records');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }

    // public function followedChallenge(Request $request){
    //     try{
    //         $following_list = Follower::where('follower_id', $request['user_id'] ?? auth('api')->id())->get();
    //         $following_id = [];

    //         if ($following_list->isNotEmpty()){
    //             foreach ($following_list as $data){
    //                 $following_id[] = $data->following_id;
    //             }
    //         }
    //         $challenge = ManageChallenge::with('user:id,full_name,image', 'category:id,name')
    //                                         ->withCount('like')
    //                                         ->whereIn('user_id', $following_id);
    //         if (!empty($request->search)){
    //             $challenge = $challenge->where('title', 'LIKE', '%'.$request['search'].'%');
    //         }
    //         $challenge = $challenge->paginate(10);

    //         if ($challenge->isNotEmpty()){
    //             return response()->json([
    //                 'status' => 1,
    //                 'message' => 'Following data.',
    //                 'cover_image_path' => URL::to('/') . '/public/storage/uploads/manage_challenge/cover_image/',
    //                 'thumbnail_path' => URL::to('/') . '/public/storage/uploads/manage_challenge/thumbnail/',
    //                 'video_path' => URL::to('/') . '/public/storage/uploads/manage_challenge/video/',
    //                 'user_image_path' => URL::to('/') . '/public/storage/uploads/profile-image/',
    //                 'data' => $challenge
    //             ]);  
    //         }
    //         return no_records('No Records');
    //     }
    //     catch (\Exception $exception){
    //         return error_response($exception);
    //     }
    // }

    public function invitePeople(Request $request){
     try{
            $follower = Follower::with('following:id,full_name,image,user_name')
                                  ->where('follower_id', auth('api')->id());

            if (!empty($request->search)){
                $follower = $follower->whereHas('following', function ($query) use($request){
                    $query->where('full_name', 'LIKE', '%'.$request['search'].'%');
                    $query->orWhere('user_name', 'LIKE', '%'.$request['search'].'%');
                });
            }
            $follower = $follower->paginate(10);

            if ($follower->isNotEmpty()){
                return response()->json([
                    'status' => 1,
                    'message' => 'List Of Invite People.',
                    'image_path' => URL::to('/') . 'uploads/profile-image/',
                    'data' => $follower
                ]);
            }
            return no_records('No Records');
     }
     catch (\Exception $exception){
         return error_response($exception);
     }

    }

}
