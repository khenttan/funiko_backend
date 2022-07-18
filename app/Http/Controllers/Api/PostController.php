<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\PostVideo;
use App\Models\Tag;
use App\Models\PostLike;
use App\Models\PostCount;
use App\Models\Comment;
use App\Models\PostShare;
use Iman\Streamer\VideoStreamer;
use App\Models\FAQ;
use App\Models\ReportPost;
use Pion\Laravel\ChunkUpload\Providers\ChunkUploadServiceProvider;
use App\Models\ReportList;
use App\VideoStream;
use App\Models\UserInterest;
use App\Models\CommentLike;
use Thumbnail;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use HTML, Config, Blade, Cookie, DB, File, Hash, Redirect, Response, Session, URL, Validator,JWTAuth;
use FFMpeg; 
class PostController extends Controller
{

    /**
     * Store a newly created posts in storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function CreatePost(Request $request){
        $validator = Validator::make($request->all(), [
            'video'       => 'required|mimes:mp4',
            'description' => 'nullable|string|max:255',
            'video_title' => 'required|string|max:255',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "status" => 0,
                "message" => $error,
            ]);
        }

        if ($request->hasFile('video')) {

            $file 					= $request->file('video');
            $destinationPath 		= public_path('/uploads/post_videos/');
            $extension 				= $request->file('video')->getClientOriginalExtension();
            $filename 				= time() . '.' . $extension;
            $fullfilename  = public_path('/uploads/post_videos/').$filename;
            // $sucs =  $file->move($destinationPath, $filename);
            $post_file_name = $filename;
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($request->file('video'));
            $clip = $video->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(0), FFMpeg\Coordinate\TimeCode::fromSeconds(60));
            $clip->save(new FFMpeg\Format\Video\X264(), $fullfilename);
            

            // $ffmpeg = FFMpeg\FFMpeg::create();
            // $video = $ffmpeg->openAdvanced(public_path('/uploads/post_videos/').$filename);
            // $clip = $video->clip(FFMpeg\Coordinate\TimeCode::fromSeconds(60), FFMpeg\Coordinate\TimeCode::fromSeconds(0));
            // $clip->save(new FFMpeg\Format\Video\X264(), $filename);

            // $video->filters()>clip(FFMpeg\Coordinate\TimeCode::fromSeconds(10),
            // FFMpeg\Coordinate\TimeCode::fromSeconds(1));
            //save the thumbnail

            $extension_type   = $file->getClientMimeType();
            $thumbnail_path     =  public_path('/uploads/image_thumbnail/'); 
            $video_path       = $destinationPath.'/'.$filename;
            $thumbnail_image  = auth('api')->user()->id.".".time().".jpg";
            $thumbnail_status = Thumbnail::getThumbnail($video_path,$thumbnail_path,$thumbnail_image,1);





            if (!$thumbnail_status) {
                return response()->json([
                    "status" => 0,
                    "message" => "Thumbnail image not upload",
                ]);
            }else{
                $creat_post = PostVideo::create([
                    'user_id'        => auth('api')->user()->id,
                    'video'          =>  $post_file_name,
                    'thumbnail_image' =>  $thumbnail_image,
                    'description'    => $request->description ?? NULL,
                    'video_title'    =>  $request->video_title ,
                ]);
                if(isset($request->tag)  && $request->tag != null){
                    if(is_array($request->tag)){
                        foreach($request->tag as $tag){
                            $creat_tags = Tag::create([
                                'post_video_id'  => $creat_post['id'],
                                'tags'          =>  $tag,
                            ]);
                        }
                    }else{
                        $tags_data = json_decode($request->tag);
                        foreach($tags_data as $tag){
                            $creat_tags = Tag::create([
                                'post_video_id'  => $creat_post['id'],
                                'tags'          =>  $tag,
                            ]);
                        }
                    }
                }
                return response()->json([
                    'status' => 1,
                    'message' => 'List',
                    'video_path' => URL::to('/') . '/uploads/post_videos/',
                    'data' => $creat_post,
                    'message' => "Post uploaded successfully",
                ]);
    
            }

        }else{
            return response()->json([
                "status" => 0,
                "message" => "please Upload a Video",
            ]);
        }
        
        
    }


    /**
     * Get a  created posts from storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function homePageVideos(){
        try {

            $report_ids = ReportPost::where('reported_by',auth('api')->user()->id)->pluck('post_video_id');
            

            $user_interest_tags = UserInterest::where('user_id',auth('api')->user()->id)->with('interestCategory')->get()->toArray();
            if($user_interest_tags != "" && count($user_interest_tags) > 0){
                $user_interest_tags = array_column($user_interest_tags, 'interest_category');
                $user_interest_tags = array_column($user_interest_tags, 'name');

                $user_interst =  PostVideo::with('user','tags','postView')->where('is_delete',0)->whereNotIn('id',$report_ids)->whereDoesntHave('blockByMe', function($q){ 
                    $q->where('block_to_id',auth('api')->user()->id);
                })->whereHas('tags', function($query) use ($user_interest_tags) {
                    foreach($user_interest_tags as $k =>$userTag){
                        $query->where('tags', 'like', '%' .$userTag. '%');
                    }
                })->whereDoesntHave('blockBYOther', function($q){
                                $q->where('block_by_id',auth('api')->user()->id);
                            })->withCount(['postLike','is_like','comments','postShare'])->inRandomOrder()->paginate(4);
                            if ($user_interst->isNotEmpty()) {
                                return response()->json([
                                'status'            => 1,
                                'message'           => 'User data',
                                'video_path'        => URL::to('/') . '/uploads/post_videos/',
                                'user_image_path'    => URL::to('/') . '/uploads/profile-image/',
                                'data' => $data
                            ]);
                            }
                            
             }
            $data =  PostVideo::with('user','tags','postView')->where('is_delete',0)->whereNotIn('id',$report_ids)->whereDoesntHave('blockByMe', function($q){ 
                            $q->where('block_to_id',auth('api')->user()->id);
                        })->whereDoesntHave('blockBYOther', function($q){
                                        $q->where('block_by_id',auth('api')->user()->id);
                                    })->withCount(['postLike','is_like','comments','postShare'])->inRandomOrder()->paginate(4);

            if ($data->isNotEmpty()) {
                return response()->json([
                'status'            => 1,
                'message'           => 'User data',
                'video_path'        => URL::to('/') . '/uploads/post_videos/',
                'user_image_path'    => URL::to('/') . '/uploads/profile-image/',
                'data' => $data
            ]);
            }
            return no_records('No Records');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    /**
     * Get a  particular posts from storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function UserPostVideos(Request $request){
        try {
            if(isset($request['user_id']) && $request['user_id'] != null){
                $user_id  =   $request['user_id'];
            }else{
                $user_id = auth('api')->user()->id;
            }

            $data =  PostVideo::with('user','tags','postView')->where('is_delete',0)->where('user_id',$user_id)->where('id','!=',$request['video_id'])->withCount(['postLike','is_like','comments','postShare'])->paginate(4)->toArray();
               if(isset($request['page']) && $request['page'] == 1 ){
                    $video_data = PostVideo::with('user','tags','postView')->where('id',$request['video_id'])->withCount(['postLike','is_like','comments','postShare'])->get()->toArray();
                    $data['data']  =  array_merge($video_data,$data['data']);
                }
            //old concept
            // $data =  PostVideo::with('user','tags','postView')
            // ->whereDoesntHave('blockByMe', function($q){ 
            //     $q->where('block_to_id',auth('api')->user()->id);
            // })->orWhereDoesntHave('blockBYOther', function($q){
            //     $q->where('block_by_id',auth('api')->user()->id);
            // })->where('is_delete',0)->where('user_id',$user_id)->where('id','!=',$request['video_id'])->withCount(['postLike','is_like','comments','postShare'])->paginate(4)->toArray();
           
            // if(isset($request['page']) && $request['page'] == 1 ){
            //         $video_data = PostVideo::with('user','tags','postView')->whereDoesntHave('blockByMe', function($q){ 
            //             $q->where('block_to_id',auth('api')->user()->id);
            //         })->orWhereDoesntHave('blockBYOther', function($q){
            //             $q->where('block_by_id',auth('api')->user()->id);
            //         })->where('id',$request['video_id'])->withCount(['postLike','is_like','comments','postShare'])->get()->toArray();
            //         $data['data']  =  array_merge($video_data,$data['data']);
            //     }
            return response()->json([
                'status'            => 1,
                'message'           => 'User data',
                'data'              => $data,
            ]);
            return no_records('No Records');
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    /**
     * Get a  created like videos from storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function UserLikeVideos(Request $request){
        $videos_list = PostLike::where('user_id', $request['user_id'] ?? auth('api')->id())->where('post_video_id','!=',$request['video_id'])->with('videos.user','videos.tags','videos.postView','videos.is_like','videos.comments','videos.postLike','videos.postShare')->has('videos')->paginate(4)->toArray();
        if (isset($request['page']) && $request['page'] == 1) {
            $partiCular_video = PostLike::where('user_id', $request['user_id'] ?? auth('api')->id())->where('post_video_id', $request['video_id'])->with('videos.user', 'videos.tags', 'videos.postView', 'videos.is_like', 'videos.comments', 'videos.postLike','videos.postShare')->has('videos')->get()->toArray();
            $videos_list['data'] =  array_merge($partiCular_video, $videos_list['data']);
        }
        $videos_liste = array_column($videos_list['data'],'videos');
        foreach($videos_liste as  $key => $vide){
            $videos_liste[$key]['is_like_count'] =  count($vide['is_like']);
            $videos_liste[$key]['comments_count'] = count($vide['comments']);
            $videos_liste[$key]['post_like_count'] = count($vide['post_like']);
            $videos_liste[$key]['post_share_count'] = count($vide['post_share']);
        }
        $videos_list['data'] = $videos_liste;
        return response()->json([
            'status'            => 1,
            'message'           => 'Liked Videos data',
            'data' => $videos_list
        ]);
    }


      /**
     * Get a  created sponserd videos from storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sponserdVideoData(Request $request){
        $videos_list = PostCount::orderBy('post_count','DESC')->where('post_video_id','!=',$request['video_id'])->with('videos.user','videos.tags','videos.postView','videos.is_like','videos.comments','videos.postLike','videos.postShare')->has('videos')->paginate(4)->toArray();
        if (isset($request['page']) && $request['page'] == 1) {
            $partiCular_video = PostCount::where('post_video_id', $request['video_id'])->with('videos.user', 'videos.tags', 'videos.postView', 'videos.is_like', 'videos.comments', 'videos.postLike','videos.postShare')->has('videos')->get()->toArray();
            $videos_list['data'] =  array_merge($partiCular_video, $videos_list['data']);
        }
        $videos_liste = array_column($videos_list['data'],'videos');
        foreach($videos_liste as  $key => $vide){
            $videos_liste[$key]['is_like_count'] =  count($vide['is_like']);
            $videos_liste[$key]['comments_count'] = count($vide['comments']);
            $videos_liste[$key]['post_like_count'] = count($vide['post_like']);
            $videos_liste[$key]['post_share_count'] = count($vide['post_share']);
        }
        $videos_list['data'] = $videos_liste;
        return response()->json([
            'status'            => 1,
            'message'           => 'Sponserd Videos data',
            'data' => $videos_list
        ]);
    }

     /**
     * Get a searched sponserd videos from storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function searchSponserdVideo(Request $request){
        try {
            $text = $request->text;
            $videos_list = PostCount::orderBy('post_count', 'DESC')->with('videos')->where('post_video_id','!=',$request['video_id'])->with('videos.user','videos.tags','videos.postView','videos.is_like','videos.comments','videos.postLike','videos.postShare')->whereHas('videos', function ($q) use ($text) {
                $q->where('video_title', 'like', '%'.$text.'%');
            })->paginate(4)->toArray();
            if (isset($request['page']) && $request['page'] == 1) {
                $partiCular_video = PostCount::where('post_video_id', $request['video_id'])->with('videos.user', 'videos.tags', 'videos.postView', 'videos.is_like', 'videos.comments', 'videos.postLike','videos.postShare')->get()->toArray();
                $videos_list['data'] =  array_merge($partiCular_video, $videos_list['data']);
            }
            $videos_liste = array_column($videos_list['data'],'videos');
            foreach($videos_liste as  $key => $vide){
                $videos_liste[$key]['is_like_count'] =  count($vide['is_like']);
                $videos_liste[$key]['comments_count'] = count($vide['comments']);
                $videos_liste[$key]['post_like_count'] = count($vide['post_like']);
                $videos_liste[$key]['post_share_count'] = count($vide['post_share']);
            }
            $videos_list['data'] = $videos_liste;
            return response()->json([
                'status'            => 1,
                'message'           => 'Sponserd Videos data',
                'data' => $videos_list
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }

    /**
     * Get a searched tags videos from storage.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function tagVideoData(Request $request){
        try {
            $videos_list    =  Tag::where('tags',$request['tag_name'])->where('post_video_id', '!=', $request['video_id'])->with('video.user', 'video.tags', 'video.postView', 'video.is_like', 'video.comments', 'video.postLike','video.postShare')->has('video')->paginate(4)->toArray();
            if (isset($request['page']) && $request['page'] == 1) {
                $partiCular_video = Tag::where('post_video_id', $request['video_id'])->where('tags',$request['tag_name'])->with('video.user', 'video.tags', 'video.postView', 'video.is_like', 'video.comments', 'video.postLike','video.postShare')->has('video')->get()->toArray();
                $videos_list['data'] =  array_merge($partiCular_video, $videos_list['data']);
            }
            $videos_liste = array_column($videos_list['data'], 'video');
            foreach ($videos_liste as  $key => $vide) {
                $videos_liste[$key]['is_like_count'] =  count($vide['is_like']);
                $videos_liste[$key]['comments_count'] = count($vide['comments']);
                $videos_liste[$key]['post_like_count'] = count($vide['post_like']);
                $videos_liste[$key]['post_share_count'] = count($vide['post_share']);

            }
            $videos_list['data'] = $videos_liste;
            return response()->json([
                'status'            => 1,
                'message'           => 'tag video data',
                'data' => $videos_list
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }
        
    /**
     * Get a user post.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getUserPost(Request $request){
        try {
            $videos_list = PostVideo::where('user_id', $request['user_id'] ?? auth('api')->id())->where('is_delete',0)->paginate(6);
            return response()->json([
                'status'            => 1,
                'message'           => 'Post Videos data',
                'data' => $videos_list
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }

    /**
     * Get a user like videos.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getLikeVideos(Request $request){
        try {
            $videos_list = PostLike::where('user_id', $request['user_id'] ?? auth('api')->id())->with('videos')->has('videos')->paginate(6)->toArray();
            $videos_liste = array_column($videos_list['data'],'videos');
            
            $videos_list['data'] = $videos_liste;
            return response()->json([
                'status'            => 1,
                'message'           => 'Liked Videos data',
                'data' => $videos_list
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }


     /**
     * Get a user sponserd videos.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sponsoredVideos(Request $request){
        try{
            $data =  PostVideo::with('user','tags','postView')->where('id','!=',$request['video_id'])->withCount(['postLike','is_like','comments'])->paginate(4)->toArray();
            
            if(isset($request['page']) && $request['page'] = 1 ){
                $video_data = PostVideo::with('user','tags','postView')->where('id',$request['video_id'])->withCount(['postLike','is_like','comments'])->get()->toArray();
                $data['data']  =  array_merge($video_data,$data['data']);
            }
            shuffle($data['data']);
            return response()->json([
                'status'            => 1,
                'message'           => 'User data',
                'data'              => $data,
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }

    }
    
    /**
     * add a like to the post.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postLike(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:post_videos,id',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            $post_data = PostLike::where(['post_video_id' => $request['post_id'],
            'user_id' => auth('api')->user()->id])->first();
            if (empty($post_data)) {
                $like = new PostLike();
                $like->post_video_id = $request['post_id'];
                $like->user_id = auth('api')->id();
                $like->save();
                
                $user_id =  PostVideo::where('id',$request['post_id'])->first();
                $userDetails        =   \App\Models\User::where('id',$request['user_id'])->first();
                $rep_array  =   array();
                $action     =   'FOLLOWED';
                $subject = auth('api')->user()->username." likes your post";
                saveNotificationActivity($rep_array,$action,$user_id->user_id,$subject);

                return response()->json([
                    'status' => 1,
                    'message' => 'Post liked successfully.',
                    'like_status' => 1,
                    'data' => $like,
                    //'notification' => $notification
                ]);
            }
            $post_data->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Post unliked successfully.',
                'unlike_status' => 0,
                'data' => []
            ]);
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    /**
     * increment a count of view to a post.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postCount(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:post_videos,id',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        $count_data  = DB::table('post_counts')->where('post_video_id',$request['post_id'])->increment('post_count', 1);
        if (empty($count_data)) {
            $post_data = new PostCount();
            $post_data->post_video_id = $request['post_id'];
            $post_data->post_count = 1;
            $post_data->save();
            return response()->json([
                'status' => 1,
                'message' => 'Post viewed successfully.',
                'like_status' => 1,
                'data' => $post_data,
                //'notification' => $notification
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'Post viewed successfully.',
            'like_status' => 1,
            'data' => $count_data,
            //'notification' => $notification
        ]);

    }


    public function CommentPost(Request $request){
        $validator = Validator::make($request->all(), [
            'post_video_id'          =>   'required',
            'comment_text'          =>   'required|min:1|max:100',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {

            $data =  PostVideo::where('id',$request['post_video_id'])->first();
            if($data->is_delete != "1"){
                $create = Comment::create([
                    'post_video_id' => $request['post_video_id'],
                    'user_id'       => auth('api')->id(),
                    'comment_text'  => $request['comment_text'],
                ]);
                return response()->json([
                    'status' => 1,
                    'message' => 'Comment successfully.',
                    'data' => $create,
                    //'notification' => $notification
                ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'No video found',
                    //'notification' => $notification
                ]);
            }

        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }

    public function CommentOnComment(Request $request){
        $validator = Validator::make($request->all(), [
            'post_video_id'          =>   'required',
            'comment_id'            =>   'required',
            'comment_text'          =>   'required|min:1|max:100',
        ]);
        if ($validator->fails()){
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            $video_data =  PostVideo::where('id',$request['post_video_id'])->first();
            if($video_data->is_delete != "1"){
                $create = Comment::create([
                    'post_video_id' => $request['post_video_id'],
                    'user_id'       => auth('api')->id(),
                    'comment_text'  => $request['comment_text'],
                    'parent_id'     => $request['comment_id'],    
                ]);
                return response()->json([
                    'status' => 1,
                    'message' => 'comment successfully.',
                    'data' => $create,
                    //'notification' => $notification
                ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'No video found',
                    //'notification' => $notification
                ]);
            }
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }

    public function getComment(Request $request){
        try{
            $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:post_videos,id',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
            }
            $data =  Comment::with('user')->where('post_video_id',$request['post_id'])->where('parent_id',0)
            ->withCount(['parent','is_like','commentLike'])->paginate(10);
                return response()->json([
                'status'        => 1,
                'data' => $data
            ]);
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }


    public function getChildComment(Request $request){
        try{
            $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:post_videos,id',
            'comment_id' => 'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
            }
            $data =  Comment::with('user')->where('post_video_id',$request['post_id'])->where('parent_id',$request['comment_id'])->get();
                return response()->json([
                'status'        => 1,
                'data' => $data
            ]);
        }
        catch (\Exception $exception){
            return error_response($exception);
        }
    }



    public function likeComment(Request $request){
        $validator = Validator::make($request->all(), [
            'comment_id'            =>   'required',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                "success" => 0,
                "message" => $error,
            ]);
        }
        try {
            $post_data = CommentLike::where(['comment_id' => $request['comment_id'],
            'user_id' => auth('api')->user()->id])->first();
            if (empty($post_data)) {
                $like = new CommentLike();
                $like->comment_id = $request['comment_id'];
                $like->user_id = auth('api')->id();
                $like->save();
                return response()->json([
                    'status' => 1,
                    'message' => 'Commeent liked successfully.',
                    'like_status' => 1,
                    'data' => $like,
                    //'notification' => $notification
                ]);
            }
            $post_data->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Comment unliked successfully.',
                'unlike_status' => 0,
                'data' => []
            ]);
        } catch (\Exception $exception) {
            return error_response($exception);
        }
    }

        public function deleteComment(Request $request){
            $validator = Validator::make($request->all(), [
                'comment_id'            =>   'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                    "success" => 0,
                    "message" => $error,
                ]);
            }
            $deleteuserchild = Comment::where('parent_id', $request['comment_id'])->delete();
            $deleteuser = Comment::where('id', $request['comment_id'])->delete();
            return response()->json([
                'status' => 1,
                'message' => 'Comment deleted successfully.',
                'unlike_status' => 0,
                'data' => []
            ]);
        }

        public function reportPost(Request $request){
            $validator = Validator::make($request->all(), [
                'post_id'            =>   'required',
                'report_id'             =>  'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                    "success" => 0,
                    "message" => $error,
                ]);
            }

            $video_data =  PostVideo::where('id',$request['post_id'])->first();
            if ($video_data->is_delete != "1") {
                $post_data = ReportPost::where(['post_video_id' => $request['post_id'],'reported_by' => auth('api')->user()->id])->first();
                if (empty($post_data)) {
                    $like = new ReportPost();
                    $like->post_video_id = $request['post_id'];
                    $like->reported_by   = auth('api')->id();
                    $like->report_id     = $request['report_id'];
    
                    
                    $like->save();
                    return response()->json([
                        'status' => 1,
                        'message' => 'Post Reported successfully.',
                        'like_status' => 1,
                        'data' => $like,
                        //'notification' => $notification
                    ]);
                }
                return response()->json([
                    'status' => 1,
                    'message' => 'Post already reported.',
                    'data' => []
                ]);
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'No video found',
                    //'notification' => $notification
                ]);
            }
        }

        public function getReportList(){
           $data =  ReportList::get();
            return response()->json([
                'status' => 1,
                'data' => $data,
                //'notification' => $notification
            ]);
        }

        public function uploadPost(Request $request) {
                $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
            
                if (!$receiver->isUploaded()) {
                    // file not uploaded
                }
                $fileReceived = $receiver->receive(); // receive file
                if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
                    $file = $fileReceived->getFile(); // get file
                    $extension = $file->getClientOriginalExtension();
                    $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion
                    $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name
                    $disk = Storage::disk(config('filesystems.default'));
                    $path = $disk->putFileAs('videos', $file, $fileName);
                    // delete chunked file
                    unlink($file->getPathname());
                    return [
                        'path' => asset('storage/' . $path),
                        'filename' => $fileName
                    ];
                }
                // otherwise return percentage information
                $handler = $fileReceived->handler();
                return [
                    'done' => $handler->getPercentageDone(),
                    'status' => true
                ];
            }

        public function videoShare(Request $request) {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required|exists:post_videos,id',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json([
                    "status" => 0,
                    "message" => $error,
                ]);
            }

            $video_data =  PostVideo::where('id',$request['post_id'])->first();
            if ($video_data->is_delete != "1") {
                $post_data = PostShare::where(['post_video_id' => $request['post_id'],
                'user_id' => auth('api')->user()->id])->first();
                if (empty($post_data)) {
                    $like = new PostShare();
                    $like->post_video_id = $request['post_id'];
                    $like->user_id = auth('api')->id();
                    $like->save();
                    return response()->json([
                        'status' => 1,
                        'message' => 'Post shared successfully.',
                        'data' => $like,
                        //'notification' => $notification
                    ]);
    
                }else{
                    return response()->json([
                        'status' => 0,
                        'message' => 'Post already shared.',
                        'data' => [],
                        //'notification' => $notification
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 0,
                    'message' => 'No video found',
                    //'notification' => $notification
                ]);
            }
                
            }



            // public function CreateAllThumb(){
            //   $all_data =   PostVideo::get()->toArray();
            //   //print_r($all_data[0]['video']);
            //   $thumbnail_path     = 'public/storage/uploads/image_thumbnail/';
            //   $thumbnail_image  = auth('api')->user()->id.".".time().".jpg";
            //  echo  $thumbnail_status = Thumbnail::getThumbnail((string)$all_data[0]['video'],$thumbnail_path,$thumbnail_image);

            //   die;
            //   foreach($all_data as $post){
               
            //     $thumbnail_status = Thumbnail::getThumbnail($post['video'],$thumbnail_path,$thumbnail_image);

            //     $creat_post = PostVideo::where('id',$post['id'])->update([
            //         'thumbnail_image' => $thumbnail_image,
            //     ]);
            //   }


            // }
            public function deletePost(Request $request) {
                $validator = Validator::make($request->all(), [
                    'post_id' => 'required|exists:post_videos,id',
                ]);
                if ($validator->fails()) {
                    $error = $validator->errors()->first();
                    return response()->json([
                        "success" => 0,
                        "message" => $error,
                    ]);
                }
               $delete =  PostVideo::where('user_id', auth('api')->user()->id)->where('id',$request['post_id'])->update([
                    'is_delete' => 1
                ]);
                if($delete != ""){
                    return response()->json([
                        'status' => 1,
                        'message' => 'Post deleted successfully.',
                        //'notification' => $notification
                    ]);
                }else{
                    return response()->json([
                        'status' => 0,
                        'message' => 'Something went wrong .',
                        //'notification' => $notification
                    ]);
                }
             
            }

            public function videoStream($name) {
                 $video_path =  '/uploads/post_videos/' .$name ;

                $path = public_path($video_path);
    
                VideoStreamer::streamFile($path);
                // // $stream = new VideoStream($filePath);
                // $stream = new \App\VideoStream($video_path);
                // $stream->start();                
            }

            

            
        
    
}
