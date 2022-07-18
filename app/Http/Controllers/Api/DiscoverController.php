<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\PostVideo;
use App\Models\User;
use App\Models\PostCount;
use App\Models\Block;
use App\Models\Follower;
use App\Models\Advertisment;
use HTML, Config, Blade, Cookie, DB, File, Hash, Redirect, Response, Session, URL, Validator,JWTAuth;
class DiscoverController extends Controller
{
    /**
     * getting discover data.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function discover(Request $request){
        $validator = Validator::make($request->all(), [
            'text'                       =>  'required',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        $tags_data = Tag::where('tags', 'like', $request->text);

        return response()->json([
            'status' => 1,
            'tags_data' => $tags_data,
            'message' => 'data successfully update',
        ]);


    }

    /**
     * getting tags data.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function tags(Request $request){
        if($request->text == ""){
            $tags_data = Tag::has('video')->paginate(20);
        }else{
            $tags_data = Tag::where('tags', 'like','%'.$request->text.'%')->has('video')->paginate(20);
        }
        return response()->json([
            'status' => 1,
            'data' => $tags_data,
            'message' => 'data successfully',
        ]);
    }

    public function following(Request $request){
        try {
            if ($request->text == "") {
                $following_list = Follower::with('following')->where('follower_id', auth('api')->user()->id);
                $total_count = $following_list->count();
                $following_list = $following_list->paginate(10);
                if ($following_list->isNotEmpty()) {
                    return response()->json([
                'status' => 1,
                'message' => 'Following data.',
                'total_count' => $total_count,
                'data' => $following_list   
                    ]);
                }
                return response()->json([
                'status' => 0 ,
                'message' => 'No Following data.',
                'total_count' => 0,
                'data' => []
                ]);
            } else {
                $text = $request->text;
                $following_list = Follower::with('following')->whereHas('following', function ($q) use ($text) {
                    $q->whereRaw("concat(firstname, ' ', lastname) like '%" .$text. "%' ")->orWhere('username', 'like', '%'.$text.'%')->orWhere('cellphone', 'like', '%'.$text.'%');
                })->where('follower_id', auth('api')->user()->id);
                $total_count = $following_list->count();
                $following_list = $following_list->paginate(10);
                if ($following_list->isNotEmpty()) {
                    return response()->json([
                'status' => 1,
                'message' => 'Following data.',
                'total_count' => $total_count,
                'data' => $following_list
                 ]);
                }
                return no_records('No Records');
            }
        }
            catch (\Exception $exception){
                return error_response($exception);
            }
    }

    public function recommondation(Request $request){
        try {
            if ($request->isMethod('post')) {
                $text = $request->text;
                $myfollwersdata = Follower::where('follower_id',auth('api')->user()->id)->select('following_id')->get()->toArray();
                $blockBy = Block::where('block_by_id',auth('api')->user()->id)->pluck('block_to_id')->toArray();
                $blockTo = Block::where('block_to_id',auth('api')->user()->id)->pluck('block_by_id')->toArray();
                $all_block = array_merge($blockBy,$blockTo);
                $all_block = array_unique($all_block);
                $ids =  array_column($myfollwersdata, 'following_id');
                $ids = array_merge($all_block,$ids);
                $all_users_data  = User::where(function($query) use ($text){
                    $query->where('firstname', 'like', '%'.$text.'%')->orWhere('lastname', 'like', '%'.$text.'%')->orWhere('username', 'like', '%'.$text.'%')->orWhere('cellphone', 'like', '%'.$text.'%');
                })->where('id','!=', auth('api')->user()->id)->get()->whereNotIn('id', $ids)->where('status','!=','0')->toArray();
                $all_users_data =   array_values($all_users_data);
                return response()->json([
                    'status' => 1,
                    'message' => 'Recommended Data.',
                    'data' => $all_users_data
                ]);

            }else{
             $data  =  User::where('id', auth('api')->user()->id)->with('myFollowing.following.otherUserFollowing.following')->first();
             $recommend = [];
            if(isset($data->myFollowing)){
                $followers_data = $data->myFollowing->toArray();
                foreach($followers_data as $key => $users_data){
                    if(count($users_data['following']['other_user_following']) > 0){
                        foreach($users_data['following']['other_user_following'] as $k => $recommendData){
                            $recommend[]  = $recommendData['following'];
                        }
                    }
                } 
            }
            $myfollwersdata = Follower::where('follower_id',auth('api')->user()->id)->select('following_id')->get()->toArray();
            $ids =  array_column($myfollwersdata, 'following_id');
            $all_users_data  = User:: where(function($query){
                $query->where('country',auth('api')->user()->country);
                $query->orWhere('state',auth('api')->user()->state );
                $query->orWhere('city',auth('api')->user()->city);
            })->whereNotIn('id', $ids)->where('id','!=', auth('api')->user()->id)->get();
            $all_recomeneded = [];
            $all_recomeneded = array_merge($recommend,$all_users_data->toArray());
            $final_array = [];
            foreach($all_recomeneded as $key => $value){
                $final_array[$value['id']] = $value;
            }

            $all_recomeneded =  array_unique($all_recomeneded, SORT_REGULAR);
            $ids = array_column($all_recomeneded, 'id');
                $ids = array_unique($ids);
                $array = array_filter($all_recomeneded, function ($key, $value) use ($ids) {
                    return in_array($value, array_keys($ids));
                }, ARRAY_FILTER_USE_BOTH);
                $array  = array_values($array);

               $blockBy = Block::where('block_by_id',auth('api')->user()->id)->pluck('block_to_id')->toArray();
                $blockTo = Block::where('block_to_id',auth('api')->user()->id)->pluck('block_by_id')->toArray();
                $all_block = array_merge($blockBy,$blockTo);
                $all_block = array_unique($all_block);

                foreach($array as $key => $arr){
                    if(in_array($arr["id"],$all_block)){
                        unset($array[$key]);
                    }
                }
                if(count($array) > 10){
                    $array = array_slice($array, 0, 10);   // returns "a", "b", and "c"
                    shuffle($array);
                }
                return response()->json([
                    'status' => 1,
                    'message' => 'Recommended Data.',
                    'data' => $array
                ]);

            }
           

        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    public function popularTags(){
        try {
            $allRecords =      Tag::groupBy('tags')
            ->selectRaw('count(*) as total, tags')
            ->orderBy('total', 'desc')
            ->has('video')
            ->take(10)
            ->get()
            ->toArray();
            return response()->json([
                'status' => 1,
                'message' => 'Popular tags Data.',
                'data' => $allRecords
            ]);
        }catch (\Exception $exception){
            return error_response($exception);
        }

    }

    public function topSearch(Request $request){
       $text = $request->text;
        if($text != ""){
            $blockBy = Block::where('block_by_id',auth('api')->user()->id)->pluck('block_to_id')->toArray();
            $blockTo = Block::where('block_to_id',auth('api')->user()->id)->pluck('block_by_id')->toArray();
            $all_block = array_merge($blockBy,$blockTo);
            $all_block = array_unique($all_block);
            $all_users_data  = User::where(function($query) use ($text){
                    $query->whereRaw("concat(firstname, ' ', lastname) like '%" .$text. "%' ")->orWhere('username', 'like', '%'.$text.'%')->orWhere('cellphone', 'like', '%'.$text.'%');
            })->where('id','!=', auth('api')->user()->id)->get()->whereNotIn('id', $all_block)->where('status','!=','0')->toArray();
            $all_users_data =   array_values($all_users_data);
            return response()->json([
                'status' => 1,
                'message' => 'Top search Data.',
                'data' => $all_users_data
            ]);
        }else{
            return response()->json([
                'status' => 1,
                'message' => 'No record found.',
                'data' => []
            ]);
        }
  
    }

    public function tagVideos(Request $request){
        try {
            $tagname = $request->tag_name;
            $data    =  Tag::where('tags', $tagname)->with('video')->has('video')->get();
            return response()->json([
                'status' => 1,
                'message' => 'Videos Data.',
                'data' => $data
            ]);
        }catch (\Exception $exception){
            return error_response($exception);
        }
    }

    public function sponsored(Request $request){
        $text = $request->text;
        if ($text != "") {
            $post_data = PostCount::orderBy('post_count', 'DESC')->with('videos')->whereHas('videos', function ($q) use ($text) {
                $q->where('video_title', 'like', '%'.$text.'%');
            })->paginate(10)->toArray();
     

        }else{
            $post_data = PostCount::orderBy('post_count','DESC')->with('videos')->has('videos')->paginate(8)->toArray();
            $ads  = Advertisment::inRandomOrder()->limit(2)->get();
            $videos_liste = array_column($post_data['data'],'videos');
            shuffle($videos_liste);
            $videos_list['data'] = $videos_liste;
            $videos_list['data'] =  array_merge($videos_liste,$ads->toArray());

            return response()->json([
                'status'            => 1,
                'message'           => 'Sponserd Videos data',
                'data'              => $videos_list
            ]);

        }
           $videos_liste = array_column($post_data['data'],'videos');
           shuffle($videos_liste);
           $videos_list['data'] = $videos_liste;
           return response()->json([
               'status'            => 1,
               'message'           => 'Sponserd Videos data',
               'data'              => $videos_list
           ]);
    }   

    
    public function popularVideos(){
        $postdata =     PostVideo::withCount(['postLike','is_like','comments'])->orderBy('post_like_count','DESC')->paginate(10);
        return response()->json([
            'status'            => 1,
            'message'           => 'da Videos data',
            'data'              => $postdata
        ]);
    }
}
