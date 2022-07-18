<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatUser;
use App\Models\User;
use App\Models\Chat;
use App\Models\ChatLike;
use App\Models\Follower;
use Thumbnail;
use App\Models\MuteConversation;
use App\Models\Group;
use App\Models\Notifications;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class MessageController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allMsg(Request $request)
    {
        try {
        if (isset($request['user_id'])) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json([
                    "success" => 0,
                    "message" => $error,
                ]);
            }
            $user_id = $request['user_id'];
            $sender_id = auth()->user()->id;
            // print_r($sender_id.",");
            // print_r($user_id);

            $data = ChatUser::orWhere(function ($query) use($user_id,$sender_id) {
                $query->where('sender_id', $user_id)
                    ->where('receiver_id',$sender_id);
            })->orWhere(function ($query) use($user_id,$sender_id) {
                $query->where('sender_id', $sender_id)
                    ->where('receiver_id', $user_id);
            })->first();

            $chat_id  = $request['chat_id'];

            if (empty($data)) {
                $user_chat = ChatUser::create([
                    'sender_id'     => $sender_id,
                    'receiver_id'   => $request['user_id'],
                ]);
                $chat_id  = $user_chat->id;
                $result = User::select('firstname', 'lastname', 'image', 'id')->where('id', $request['user_id'])->first();
                $result['chat_id'] = $chat_id ;
                $result['sender_id'] =$sender_id ;
                $result['receiver_id'] =  $user_id;
                $status = 1;
            } else {
                $chat_id  = $data['id'];
                $update_read =   Chat::where('receiver_id', $sender_id)->where('chat_id', $chat_id)->update([
                'is_read' => 1
                
            ]);
                $result = User::select('firstname', 'lastname', 'image', 'id')->where('id', $request['user_id'])->first();

                $result['chat_data'] =  Chat::select('chat_id', 'id as _id', 'sender_id', 'receiver_id', 'msg_type', 'msg', 'created_at', 'is_read','thumbnail_image')->where('chat_id', $data->id)->where('del_chat_user_id', '!=', auth()->user()->id)->withCount(['likeChat'])->orderBy('created_at', 'DESC')->paginate(30);
                $result['chat_id'] = $chat_id ;
                $result['sender_id'] =$data['sender_id'];
                $result['receiver_id'] = $data['receiver_id'];

                $status = 1;
            }
        }else{
            $group_id  = $request['group_id'];
            $result['chat_data'] =  Chat::where('group_id',$group_id)->withCount(['likeChat','likeByMe'])->with('sendingBy:id,image')->orderBy('created_at', 'DESC')->paginate(30);
            $result['group_id'] = $group_id ;
            $status = 1;
        }
        return response()->json([
            "status"                => $status,
            "data"                  => $result,
            'chat_file_path'        => URL::to('/') . '/storage/uploads/chat_upload',
            'thumbnail_image_path'  => URL::to('/'). '/uploads/image_thumbnail/',

        ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }

      /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'msg_type' => 'required',
                'msg' => 'required',
            ]);
            if ($validator->fails()){
                $error = $validator->errors()->first();
                return response()->json([
                    "success" => 0,
                    "message" => $error,
                ]);
            }

            if ($request->hasFile('msg')) {
                $validator = Validator::make($request->all(), [
                    'msg' => 'required|max:51200',
                ]);
                if ($validator->fails()){
                    $error = $validator->errors()->first();
                    return response()->json([
                        "success" => 0,
                        "message" => $error,
                    ]);
                }

                $file = $request->file('msg');
                if ($file) {
                    $destinationPath = public_path('storage/uploads/chat_upload');
                    $extension = $request->file('msg')->getClientOriginalExtension();
                    $filename =  time(). '.' . $extension;
                    $file->move($destinationPath, $filename);
                    $request['file'] = $filename;
                }
                
                if($request['msg_type'] == 3){
                    $extension_type   = $file->getClientMimeType();
                    $thumbnail_path   =  public_path('/uploads/image_thumbnail/'); 
                    $video_path       = $destinationPath.'/'.$filename;
                    $thumbnail_image  = auth('api')->user()->id.".".time().".jpg";
                    $thumbnail_status = Thumbnail::getThumbnail($video_path,$thumbnail_path,$thumbnail_image,1);
                    if (!$thumbnail_status) {
                        return response()->json([
                            "status" => 0,
                            "message" => "Thumbnail image not upload",
                        ]);
                    }
                }
            }
            
            // dd($chat_data);
            if(isset($request['chat_id'])){
                $chat_id  = $request['chat_id'];
    
                $message = Chat::create([
                    'sender_id'       => auth()->user()->id,
                    'receiver_id'     => $request['receiver_id'],
                    'msg_type'        => $request['msg_type'],
                    'thumbnail_image' => $thumbnail_image  ?? null,
                    'msg'             => isset($request['file'])? $request['file']:$request['msg'],
                    'chat_id'         => $chat_id,
                ]);
                //send notification to user
                $is_mute = \App\Models\MuteConversation::where('chat_id',$chat_id)->where('deleted_by',$request['receiver_id'])->first();
                if($is_mute == ""){
                    $userDetails        =   \App\Models\User::where('id',$request['receiver_id'])->first();
                    $rep_array  =   array(isset($request['file'])? $request['file']:$request['msg']);
                    $action     =   'USER_MESSAGE';
                    $subject  =  auth()->user()->username;
                    saveNotificationActivity($rep_array,$action,$request['receiver_id'],$subject);
                }
        
            }else{
                $group_id  = $request['group_id'];
    
                $message = Chat::create([
                    'sender_id'     => auth()->user()->id,
                    'msg_type'      => $request['msg_type'],
                    'msg'           => isset($request['file'])? $request['file']:$request['msg'],
                    'thumbnail_image' => $thumbnail_image  ?? null,
                    'group_id'       => $group_id,
                ]);
                
                $groupDetails       =   \App\Models\Group::where('id',$group_id)->first();
                $members_id         =   \App\Models\GroupMember::where('group_id',$group_id)->pluck('user_id')->where('user_id','!=',auth()->user()->id)->toArray();
                $mess_text          =   isset($request['file'])? $request['file']: $request['msg'];
                 $mess              =   auth()->user()->username.": ".$mess_text;
                $rep_array          =   $mess;
                $action             =   'USER_MESSAGE';
                $subject            =  $groupDetails->group_name;
                sendGroupNotification($rep_array,$action,$members_id,$subject,$group_id);

            }
            $status = 1;
            // ChatUser::where('id',$chat_id)->touch();
            return response()->json([
                "status" =>$status,
                "response" =>  $message,
                'chat_file_path' => URL::to('/') . '/public/storage/uploads/chat_upload',
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }
    public function allConversation(){
        try {
            $result =  ChatUser::where('sender_id',auth()->user()->id)->orWhere('receiver_id',auth()->user()->id)->with('receiver:id,firstname,lastname,image,created_at,is_online','sender:id,firstname,lastname,image,created_at,is_online')->with('onlyOneChat')->withCount('isMuted')->get()->sortByDesc('onlyOneChat.created_at');
            if(count($result) > 0){
                        foreach($result as $key => $data){
                            if(isset($data->receiver->id) &&  $data->receiver->id == auth()->user()->id){
                                unset($result[$key]->receiver);
                            }
                            if(isset($data->sender->id) && $data->sender->id == auth()->user()->id){
                                unset($result[$key]->sender);
                            }
                            if($data->onlyOneChat  == null){
                                unset($result[$key]);
                            }
                        }
                        $result  = array_values($result->toArray());
                    }
                    $status = 1;
                // ChatUser::where('id',$chat_id)->touch();
                return response()->json([
                    "status" =>$status,
                    "response" =>  $result,
                ]);
            }
            catch (\Exception $exception){
                return error_response($exception);
            }
        }
    public function allSearchConversation(Request $request){
        try {

            $text =     $request->text;
            $user_id = auth()->user()->id;
            if($text != ""){
                $receiver =  ChatUser::where(function ($query) use ($user_id) {
                    $query->where('sender_id',$user_id)->orWhere('receiver_id',$user_id);
                })
                ->with('receiver:id,firstname,lastname,image,created_at,is_online')
                ->whereHas('receiver', function($query) use ($text,$user_id) {
                    $query->whereRaw("concat(firstname, ' ', lastname) like '%" .$text. "%' ")->where('id','!=',$user_id);
                })
                ->with('onlyOneChat')->whereHas('onlyOneChat')->withCount('isMuted')->get()->sortByDesc('onlyOneChat.created_at')->toArray();


                // if(count($receiver) > 0){
                //     foreach($receiver as $k => $data){
                //         if( isset($data['onlyOneChat'])  && $data['onlyOneChat']  == null){
                //             unset($receiver[$k]);
                //         }
                //     }
                // }


                $sender = ChatUser::where(function ($query) use ($user_id) {
                    $query->where('sender_id',$user_id)->orWhere('receiver_id',$user_id);
                })
                ->with('sender:id,firstname,lastname,image,created_at,is_online')
                 ->whereHas('sender',function($query) use($text,$user_id) {
                    $query->whereRaw("concat(firstname, ' ', lastname) like '%" .$text. "%' ")->where('id','!=',$user_id);
                })
                ->with('onlyOneChat')->whereHas('onlyOneChat')->withCount('isMuted')->get()->sortByDesc('onlyOneChat.created_at')->toArray();
           
                // if(count($sender) > 0){
                //     foreach($sender as $key => $data){

                //         if( isset($data['onlyOneChat'])  && $data['onlyOneChat']  == null){
                //             unset($sender[$key]);
                //         }
                //     }
                // }

                // if(count($sender) > 0){
                //     foreach($sender as $key => $data){
                        
                //         if(isset($data->receiver->id) &&  $data->receiver->id == auth()->user()->id){
                //             unset($sender[$key]->receiver);
                //         }
                //         if(isset($data->sender->id) && $data->sender->id == auth()->user()->id){
                //             unset($sender[$key]->sender);
                //         }
                //         if($data->onlyOneChat  == null){
                //             unset($sender[$key]);
                //         }
                //     }
                //     $sender  = array_values($sender->toArray());
                // }
                $result = array_merge($sender, $receiver);
                        $status = 1;
                    // ChatUser::where('id',$chat_id)->touch();
                    return response()->json([
                        "status" =>$status,
                        "response" =>  $result,
                    ]);
            }else{
                $result =  ChatUser::where('sender_id',auth()->user()->id)->orWhere('receiver_id',auth()->user()->id)->with('receiver:id,firstname,lastname,image,created_at,is_online','sender:id,firstname,lastname,image,created_at,is_online')->with('onlyOneChat')->withCount('isMuted')->get()->sortByDesc('onlyOneChat.created_at');
                if(count($result) > 0){
                            foreach($result as $key => $data){
                                if(isset($data->receiver->id) &&  $data->receiver->id == auth()->user()->id){
                                    unset($result[$key]->receiver);
                                }
                                if(isset($data->sender->id) && $data->sender->id == auth()->user()->id){
                                    unset($result[$key]->sender);
                                }
                                if($data->onlyOneChat  == null){
                                    unset($result[$key]);
                                }
                            }
                            $result  = array_values($result->toArray());
                        }
                        $status = 1;
                    // ChatUser::where('id',$chat_id)->touch();
                    return response()->json([
                        "status" =>$status,
                        "response" =>  $result,
                    ]);
                }
          
             }
            catch (\Exception $exception){
                return error_response($exception);
            }
        
    }

    


    public function chatLike(Request $request){
        $validator = Validator::make($request->all(), [
            'msg_id' => 'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            $like_chat = ChatLike::where(['user_id' => auth('api')->user()->id,'chat_id' => $request['msg_id']])->first();
            if (empty($like_chat)) {
                $like_chat = new ChatLike();
                $like_chat->chat_id = $request['msg_id'];
                $like_chat->user_id = auth('api')->user()->id;
                $like_chat->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Msg Liked Successfully',
                    'data' => $like_chat
                ]);
            }
            $like_chat->delete();
            return no_records('Msg UnLiked successfully');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    public function clearChat(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required',
            ]);
            $clear_only_single_chat =   Chat::where('chat_id',$request['chat_id'])->where('del_chat_user_id',0)->update([
                'del_chat_user_id' => auth('api')->user()->id
            ]);
            
            $clear_both_chat =   Chat::where('chat_id',$request['chat_id'])->where('del_chat_user_id','!=',auth('api')->user()->id)->where('del_chat_user_id','!=',0)->delete();
        
            return response()->json([
                'status' => 1,
                'message' => 'chat cleared successfully',
            ]);
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    public function deleteChat(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'msg_id' => 'required',
            ]);
            $clear_only_single_chat =   Chat::where('id',$request['msg_id'])->where('del_chat_user_id',0)->update([
                'del_chat_user_id' => auth('api')->user()->id
            ]);
            $clear_both_chat =   Chat::where('id',$request['msg_id'])->where('del_chat_user_id','!=',auth('api')->user()->id)->where('del_chat_user_id','!=',0)->delete();
        
            return response()->json([
                'status' => 1,
                'message' => 'chat clear successfully',
            ]);
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }

    public function searchChat(Request $request){
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
            "status" => 0,
            "message" => $error,
        ]);
        }

        $group =   Chat::where('chat_id',$request['chat_id'])->where('del_chat_user_id','!=',auth('api')->user()->id)->get();
        return response()->json([
            'status' => 1,
            'data' =>  $group,
            ]);
    }


    public function createGroup(Request $request){
        try {
            // return $request["users"];

            // return is_array($request["users"]);
            $validator = Validator::make($request->all(), [
            'name'      => 'required|string|min:7|max:25',
            'image'      => 'nullable|image|max:5120',
             ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
             }
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file) {
                    $destinationPath = 'storage/uploads/group_icons';
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename =  time(). '.' . $extension;
                    $file->move($destinationPath, $filename);
                }
            }
            $group =   Group::create([
            'group_name' => $request['name'],
            'group_icon' => $filename??'',
            'created_by' =>  auth('api')->user()->id,
        ]);

        $added =  GroupMember::create([
            "group_id" => $group['id'],
            "user_id"  => auth('api')->user()->id,
            "is_admin" => 1,
            "is_accept" => 1,
        ]);
        // if(is_array($request["users"])){
        //     foreach($request["users"] as $key => $id){
        //        $added =  GroupMember::create([
        //             "group_id" => $group->id,
        //             "user_id"  => $id,
        //         ]);
        //     }
        // }
        if(isset($request['users']) &&  $request['users'] != ""){
          $users_ids  = explode(",",$request['users']);
          foreach($users_ids as $key => $id){
                   $added =  GroupMember::create([
                        "group_id" => $group->id,
                        "user_id"  => $id,
                    ]);
                }
         }
            return response()->json([
            'status' => 1,
            'message' => 'Group created successfully',
            ]);
        }catch (\Exception $exception){
            return error_response($exception);
        }

    }


    public function addMember(Request $request){
        $validator = Validator::make($request->all(), [
            'ids'      => 'required',
            'group_id' => 'required'
        ]);
        if(is_array($request["ids"])){
            foreach($request["ids"] as $key => $id){
               $added =  GroupMember::create([
                    "group_id" => $request['group_id'],
                    "user_id"  => $id
                ]);
            }
        }else{
          $ids =  json_decode($request["ids"],true);
            foreach($ids as $key => $id){
                $added =  GroupMember::create([
                    "group_id" => $request['group_id'],
                    "user_id"  => $id
                ]);
            }
        }
        return response()->json([
            'status' => 1,
            'message' => 'Request has been sent to respective users',
            ]);
    }


    public function leaveGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required'
        ]);
        $leave =  GroupMember::where('user_id',auth('api')->user()->id)->where('group_id',$request['group_id'])->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Group Leave successfully',
            ]);
    }

    public function deleteGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required'
        ]);
        $clear_only_single_chat  =  Chat::where('group_id',$request['group_id'])->delete();
        $deleteAllMembers        =  GroupMember::where('group_id',$request['group_id'])->delete();
        $deleteGroup             =  Group::where('created_by',auth('api')->user()->id)->where('id',$request['group_id'])->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Group deleted successfully',
            ]);
        // if($deleteGroup->isNotEmpty()){
          
        // }else{
        //     return response()->json([
        //         'status' => 1,
        //         'message' => 'You are not authorised person to delete this group',
        //         ]);
        // }

     
    }
    public function allMembers(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required'
        ]);
        $AllMembers        =  GroupMember::where('group_id',$request['group_id'])->where('is_accept',1)->with('member')->get();
        $group             =   Group::where('id',$request['group_id'])->get();
        return response()->json([
            'status' => 1,
            'data' => $AllMembers, 
            'group_data' =>$group
            ]);
    }

    public function searchAllMembers(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required'
        ]);
        $text =  $request->text;
        if($text != ""){
            $AllMembers        =  GroupMember::where('group_id',$request['group_id'])->where('is_accept',1)->whereHas('member',function($query) use($text) {
                $query->whereRaw("concat(firstname, ' ', lastname) like '%" .$text. "%' ");
            })->with('member')->get();
            $group             =   Group::where('id',$request['group_id'])->get();
            return response()->json([
                'status' => 1,
                'data' => $AllMembers, 
                'group_data' =>$group
                ]);
        }else{
            $AllMembers        =  GroupMember::where('group_id',$request['group_id'])->where('is_accept',1)->with('member')->get();
            $group             =   Group::where('id',$request['group_id'])->get();
            return response()->json([
                'status' => 1,
                'data' => $AllMembers, 
                'group_data' =>$group
                ]);
        }
    
    }
    public function allGroupConversation(Request $request){
        $group_data = GroupMember::where('user_id',auth('api')->user()->id)->where('is_accept',1)->with('groups.onlyOneGroupChat.sendingBy:id,firstname','groups.acceptedGroupMember.member:id,username')->withCount('isMuted')->get();
        
        // $members_count = GroupMember::where('group_id',$request['group_id'])->count();
        return response()->json([
            'status' => 1,
            'data' => $group_data,
            ]);
        // $result =  ChatUser::where('sender_id',auth()->user()->id)->orWhere('receiver_id',auth()->user()->id)->with('receiver:id,firstname,lastname,image,created_at','sender:id,firstname,lastname,image,created_at')->with('onlyOneChat')->get()->sortByDesc('onlyOneChat.created_at');
    }

    public function allSearchGroupConversation(Request $request){
        try {

            $text =     $request->text;
            if($text != ""){
                $group_data = GroupMember::where('user_id',auth('api')->user()->id)->where('is_accept',1)->whereHas('groups', function($query) use ($text) {
                    $query->where('group_name', 'like', '%' . $text . '%');
                })->with('groups.onlyOneGroupChat.sendingBy:id,firstname','groups.GroupMember.member:id,username')->withCount('isMuted')->get();
                        $status = 1;
                    // ChatUser::where('id',$chat_id)->touch();
                    return response()->json([
                        "status" =>$status,
                        "data" =>  $group_data,
                    ]);
            }else{
                $group_data = GroupMember::where('user_id',auth('api')->user()->id)->where('is_accept',1)->with('groups.onlyOneGroupChat.sendingBy:id,firstname','groups.GroupMember.member:id,username')->withCount('isMuted')->get();
        
                // $members_count = GroupMember::where('group_id',$request['group_id'])->count();
                return response()->json([
                    'status' => 1,
                    'data' => $group_data,
                    ]);
                }
             }
            catch (\Exception $exception){
                return error_response($exception);
            }
    }


    public function syncContact(Request $request){
       $contact_num =  $request['contacts'];
       if(is_array($contact_num)){
       $users_data =  User::whereIn('cellphone',$contact_num)->orWhereIn('phone',$contact_num)->select('id', 'cellphone', 'firstname', 'lastname', 'image','username')->get();
        return response()->json([
            'status' => 1,
            'data' => $users_data,
            ]);
       }else{
            return "it is a string";
       }
    }
    public function allFriends(){
        try {

        $group_id  = request()->group_id ;
        if($group_id != null){
            $AllMembers        =  GroupMember::where('group_id',$group_id)->pluck('user_id');
            $data = User::where('id', auth('api')->user()->id)->with('myFollowing.following', function ($query) use($AllMembers) {
                return $query->whereNotIn('id', $AllMembers)->select('id','image','firstname','lastname','username');
            })->with('Myfollwer.follower', function ($query) use ($AllMembers) {
                return $query->whereNotIn('id',$AllMembers)->select('id','image','firstname','lastname','username');
            })->paginate(20);
            $newdata = isset($data[0])? $data[0]->toArray() : "";
            if(is_array($newdata) && count($data) > 0){
                $myFollowing  = array_column($newdata["my_following"],"following") ;
                $myfollwer  = array_column($newdata["myfollwer"],"follower") ;
                $array =         array_unique(array_merge ($myFollowing, $myfollwer), SORT_REGULAR);
                $newarray = [];
                foreach($array as $pdata) {
                    if($pdata != null){
                        $newarray[] =  $pdata;
                    }
                }
                $data  = $newarray;
            }
            return response()->json([
                'status' => 1,
                'message' => 'My friends data.',
                'data' => $data
            ]); 
        }else{
            $data = User::with('myFollowing.following:id,image,firstname,lastname,username','Myfollwer.follower:id,image,firstname,lastname,username')->where('id', auth('api')->user()->id)->paginate(20);
            $newdata = isset($data[0])? $data[0]->toArray() : "";
            if(is_array($newdata) && count($data) > 0){
                $myFollowing  = array_column($newdata["my_following"],"following") ;
                $myfollwer  = array_column($newdata["myfollwer"],"follower") ;
                $array =         array_unique(array_merge ($myFollowing, $myfollwer), SORT_REGULAR);
                $newarray = [];

                foreach($array as $pdata) {
                    if($pdata != null){
                        $newarray[] =  $pdata;
                    }
                }
                $data  = $newarray;
            }
                return response()->json([
                    'status' => 1,
                    'message' => 'My friends data.',
                    'data' => $data
                ]); 
        }
  
        // $following_list = Follower::where('follower_id', $request['user_id'] ?? auth('api')->id())->with('following.isFollowings')->with('follower.isFollowings');
        // $total_count = $following_list->count();
        // $following_list = $following_list->paginate(10);
   
        }catch (\Exception $exception){
            return error_response($exception);
        }
    }
    public function editGroup(Request $request){
        try {
            $validator = Validator::make($request->all(), [
            'name'       => 'required|string|min:7|max:25',
            'image'      => 'image|max:5120',
            'group_id'   => 'required'

        ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
            }
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file) {
                    $destinationPath = 'storage/uploads/group_icons';
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename =  time(). '.' . $extension;
                    $file->move($destinationPath, $filename);
                }
                $group =   Group::where('id',$request['group_id'])->update([
                    'group_name' => $request['name'],
                    'group_icon' => $filename,
                ]);
            }
            $group =   Group::where('id',$request['group_id'])->update([
                'group_name' => $request['name'],
            ]);


            return response()->json([
            'status' => 1,
            'message' => 'Group update successfully',
            ]);
        }catch (\Exception $exception){
            return error_response($exception);
        }

    }

    

    public function groupActiveInactive(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'status'    => 'required'
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
            "status" => 0,
            "message" => $error,
        ]);
        }
        $group =   Group::where('id',$request['group_id'])->where('created_by',auth('api')->user()->id)->update([
                    'is_inactive_group' => $request['status'],
        ]);
        if($request['status'] == 0){
            return response()->json([
                'status' => 1,
                'message' => 'Group Deactive successfully',
                ]);
    
        }else{
            return response()->json([
                'status' => 1,
                'message' => 'Group Acivate successfully',
                ]);
        }
    
    }

    public function membersActiveInactive(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'status'    => 'required'
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
            "status" => 0,
            "message" => $error,
        ]);
        }
        $group =   Group::where('id',$request['group_id'])->where('created_by',auth('api')->user()->id)->update([
            'is_inactive_member' => $request['status'],
            ]);
            if($request['status'] == 0){
                return response()->json([
                    'status' => 1,
                    'message' => 'Group Members Deactive successfully',
                    ]);

            }else{
                return response()->json([
                    'status' => 1,
                    'message' => 'Group Members Acivate successfully',
                    ]);
            }
                    
    }
    

    public function clearGroupChat(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
            "status" => 0,
            "message" => $error,
        ]);
        }

        $group =   Chat::where('group_id',$request['group_id'])->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Chat Cleard successfully',
            ]);

    }
    public function groupSearch(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
            "status" => 0,
            "message" => $error,
        ]);
        }
        $group =   Chat::where('group_id',$request['group_id'])->get();
        return response()->json([
            'status' => 1,
            'data' =>  $group,
            ]);

    }

    public function allRequest(){
        $AllMembers        =  GroupMember::where('user_id',auth('api')->user()->id)->where('is_accept',0)->with('groups.admin:id,username,image')->get();
        return response()->json([
            'status' => 1,
            'data' =>   $AllMembers,
            ]);
    }

    public function acceptRejectGroup(Request $request){
        $validator = Validator::make($request->all(), [
            'group_id' => 'required',
            'is_accept' => 'required'
        ]);
        if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }
        if($request->is_accept == "1"){
            $AllMembers        =  GroupMember::where('user_id',auth('api')->user()->id)->where('group_id',$request->group_id)->update([
                'is_accept' => 1
            ]);
            return response()->json([
                'status' => 1,
                'message' =>   "Request accepted successfully",
                ]);
        }else{
            $AllMembers        =  GroupMember::where('user_id', auth('api')->user()->id)->where('group_id', $request->group_id)->delete();
            return response()->json([
                'status' => 1,
                'message' =>   "Request rejected successfully",
                ]);
        }
    }

    public function muteConversation(Request $request){
        if(isset($request['chat_id'])){
        $muted =     MuteConversation::where('chat_id',$request['chat_id'])->where('deleted_by',auth('api')->user()->id)->first();
            if($muted != ""){   
              $remove_mute =   MuteConversation::where('chat_id',$request['chat_id'])->where('deleted_by',auth('api')->user()->id)->delete();
              return response()->json([
                'status' => 1,
                'message' =>   "Unmuted successfully",
                ]);
            }else{
                $add_mute =   MuteConversation::create([
                    'chat_id' => $request['chat_id'],
                    'deleted_by' => auth('api')->user()->id,
                ]);
                return response()->json([
                    'status' => 1,
                    'message' =>   "Muted successfully",
                    ]);
            }
        }else{
            $muted =     MuteConversation::where('group_id',$request['group_id'])->where('deleted_by',auth('api')->user()->id)->first();
            if($muted != ""){   
              $remove_mute =   MuteConversation::where('group_id',$request['group_id'])->where('deleted_by',auth('api')->user()->id)->delete();
              return response()->json([
                'status' => 1,
                'message' =>   "Unmuted successfully",
                ]);
            }else{
                $add_mute =   MuteConversation::create([
                    'group_id' => $request['group_id'],
                    'deleted_by' => auth('api')->user()->id,
                ]);
                return response()->json([
                    'status' => 1,
                    'message' =>   "Muted successfully",
                    ]);
            }

        }

    }

    public function getAllNotification(){
      $notificaitons =   Notifications::where('user_id',auth('api')->user()->id)->get();
      return response()->json([
        'status' => 1,
        'data' =>   $notificaitons,
        ]);
    }

    

    

    

}
