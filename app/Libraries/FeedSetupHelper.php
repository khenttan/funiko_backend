<?php

namespace App\libraries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use HTML;
use Config;
use Auth;
use Blade;
use Cookie;
use DB;
use File;
use Hash;
use Redirect;
use Response;
use Session;
use URL;
use Validator;
use App\Libraries\CustomHelper;
use App\Models\User;
use Carbon\Carbon;
use JWTAuth;
use App\Models\Stores;
use App\Models\Product;
use App\Models\ProductPhotos;
use Illuminate\Validation\Rule;
use App\Models\Feed;
use App\Models\Video;
use App\Models\Photo;

use App\Models\Like;

use App\Models\Comment;

use App\Models\CommentThread;
use App\Models\Traits\ImageThumb;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
/**
 * StoreSetupHelper Helper
 *
 * Add your methods in the class below
 */
class FeedSetupHelper
{

    



    /**StoreSetupHelper::getProductValidation()
     * @Description Function  for validation on category creation
     * @Used at Front HomeController
     * @param null
     * @return $validation message and validation
     *
     */
    public static function getFeedValidation($formData, $attribute)
    {
        /* define validatation messages */
        $message = array(
            'title.required' 				    =>		 	trans('Please enter feed title'),
            'description.required' 			    =>		 	trans('Please enter description'),
            'link.required' 			        =>		 	trans('Please enter link'),
        );

        /**
         * Validation rules for this model
         */
        $validate = array(
            'title'         		=>		 'required|min:3|max:191',
            'description'  			=>		 'required',
            'link'                 =>       'required',
         );

        

        /* return validation with werror messages */
        return array($message, $validate);
    } //end getProductValidation()
    
    /**
    * StoreSetupHelper:: createProduct()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function createFeed($formData = array(), $attribute = array())
    {
        $response = $data			=	array();
        $from						=	$attribute['from']??'';
        $type						=	$attribute['type']??'';
        list($message, $validate) 	= 	FeedSetupHelper::getFeedValidation($formData, $attribute);
        $validator 					= 	Validator::make($formData, $validate, $message);

        // Check Validation
        if ($validator->fails()) {
            $response 	= 	array('status' => "error", 'validator' => $validator);
            return $response;
        }

        $create     =   Feed::create($formData);

        if(!empty($formData['media'])){
            //dd(Request::file('video')->getClientOriginalExtension());
        //          $response      =   array('status' => "success", 'data' => 'yes baba');
        // return $response;
            

            $allowedVideoExtension=['mp4','video/mp4'];
            $allowedPhotoExtension=['jpg','png'];
            //photos
            
            if(in_array($formData['media']->extension(),$allowedPhotoExtension)){
                $upload=self::uploadPhotos($formData['media'],$create->id);     
            }
            //video
            else if(in_array($formData['media']->extension(),$allowedVideoExtension)){   
                $upload=self::uploadVideos($formData['media'],$create->id);        
            }
            //something else
            else{
                $response       =   array('status' => "file_error","fileFormat"=>$formData['media']->extension());
                return $response;        
            }
            
        }
        //Feeds create		
        $response 		= 	array('status' => "success", 'data' => $data);
        return $response;
    } //end createProduct()

    /**
    * StoreSetupHelper:: createProduct()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function editFeed($formData = array(), $attribute = array())
    {
        $response = $data           =   array();
        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';
        list($message, $validate)   =   FeedSetupHelper::getFeedValidation($formData, $attribute);
        $validator                  =   Validator::make($formData, $validate, $message);
        $feedId                     =   $formData['feed_id']??'';
        // Check Validation
        if ($validator->fails()) {
            $response   =   array('status' => "error", 'validator' => $validator);
            return $response;
        }

        $update=[
            'title'         =>  $formData['title']??'',
            'link'          =>  $formData['link']??'',
            'description'   =>  $formData['description']??''
        ];
        $update         =       Feed::where('id',$feedId)->update($update);
        $feed           =       Feed::find($feedId);
      
        if(!empty($formData['media'])){
           

            $allowedVideoExtension=['mp4','video/mp4'];
            $allowedPhotoExtension=['jpg','png'];
            //photos
            
            if(in_array($formData['media']->extension(),$allowedPhotoExtension)){

                $photo=Photo::where('photoable_id',$feed['id'])->first();
                //Storage Delete
                if(!empty($photo) && file_exists(upload_path_images().$photo['filename'])){
                    $path = upload_path_images().$photo['filename'];
                    unlink($path);
                }
                //Db delete
                $photoDelete=Photo::where('photoable_id',$feed['id'])->delete();
                $videoDelete=Video::where('videoable_id',$feed['id'])->delete();
                $upload=self::uploadPhotos($formData['media'],$feed['id']);     
            }
            //video
            else if(in_array($formData['media']->extension(),$allowedVideoExtension)){   
                
                $video=Video::where('videoable_id',$feed['id'])->first();
                //Storage Delete
                if(!empty($video) && file_exists(upload_path_videos().$video['filename']) ){
                    $path = upload_path_videos().$video['filename'];
                    unlink($path);
                }    
                //Db delete
                $videoDelete=Video::where('videoable_id',$feed['id'])->delete();
                $photoDelete=Photo::where('photoable_id',$feed['id'])->delete();
                
                $upload=self::uploadVideos($formData['media'],$feedId);        
            }
            //something else
            else{
                $response       =   array('status' => "file_error","fileFormat"=>$formData['media']->extension());
                return $response;        
            }
        }
        //Feeds create      
        $response       =   array('status' => "success", 'data' => $data);
        return $response;
    } //end createProduct()


    /**
    * StoreSetupHelper:: createLike()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function createLikeDislike($formData = array(), $attribute = array())
    {
        $response           =   array();
        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';

       

        //dd($formData);
        $userLike    =   Like::where('user_id',$formData['user_id'])
                            ->where('feed_id',$formData['feed_id'])
                            ->first();
       // dd($userLike);      
       //Update                      
        if(!empty($userLike))                            
        {
            if($userLike->is_like==config('globalConstant.like'))
            {
                $userLike->is_like=config('globalConstant.unlike');
                $response['message']    =   'You have successfully unlike this feed.';
            }
            else{
                $userLike->is_like=config('globalConstant.like');
                $response['message']    =   'You have successfully like this feed.';
           
            }
            $userLike->save();
        }
        //Insert
        else{
            $insert=[
                'user_id'       =>      $formData['user_id']??'',
                'feed_id'       =>      $formData['feed_id']??'',
                'is_like'       =>      config('globalConstant.like')
            ];
            Like::create($insert);
            $response['message']    =   'You have successfully like this feed.';
            
        }
        //Feeds create      
        $response       =   array('status' => "success", 'data' => $response);
        return $response;
    } //end createProduct()


      /**StoreSetupHelper::getProductValidation()
     * @Description Function  for validation on category creation
     * @Used at Front HomeController
     * @param null
     * @return $validation message and validation
     *
     */
    public static function getCommentValidation($formData, $attribute)
    {
        /* define validatation messages */
        $message = array(
            'comment.required'                    =>          trans('Please enter comment'),
            
        );

        /**
         * Validation rules for this model
         */
        $validate = array(
            'comment'                 =>       'required|max:191',
         );

        

        /* return validation with werror messages */
        return array($message, $validate);
    } //end getProductValidation()

    /**
    * StoreSetupHelper:: createLike()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function createComment($formData = array(), $attribute = array())
    {
        $response                   =   array();

        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';


        list($message, $validate)   =   FeedSetupHelper::getCommentValidation($formData, $attribute);

        $validator                  =   Validator::make($formData, $validate, $message);

        // Check Validation
        if ($validator->fails()) {
            $response   =   array('status' => "error", 'validator' => $validator);
            return $response;
        }
    

        //Insert
        $insert=[
            'user_id'   =>  $formData['user_id']??'',
            'feed_id'   =>  $formData['feed_id']??'',
            'comment'   =>  $formData['comment']??'',
            'is_comment'=>  config('globalConstant.comment')
        ];
           
        $com=Comment::create($insert);
        $response['message']    =   'You have successfully commented on this feed.';
        $response['id']         =   $com->id;        
        //Feeds create      
        $response       =   array('status' => "success", 'data' => $response);
        return $response;
    } //end createProduct()


    /**
    * StoreSetupHelper:: createLike()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function deleteFeed($formData = array(), $attribute = array())
    {
        $response                   =   array();

        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';

        $deleteFeed                 =   Feed::where('id',$formData['feed_id'])->delete();

        //Feeds create      
        $response       =   array('status' => "success", 'message' => 'Feed deleted succeessfully!');
        return $response;
    } //end createProduct()



    /**
    * StoreSetupHelper:: createLike()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function deleteComment($formData = array(), $attribute = array())
    {
        $response                   =   array();

        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';

        $comment=Comment::find($formData['comment_id']);
        $comment->deleted_by=$formData['user_id'];
        $comment->save();

        $deleteComment              =   Comment::where('id',$formData['comment_id'])->delete();

        
        $threads                    =   CommentThread::where('comment_id',$formData['comment_id'])->get();


        if(!empty($threads)){
          
            CommentThread::where('comment_id',$formData['comment_id'])->delete();
        }
    
        //Feeds create      
        $response       =   array('status' => "success", 'data' => $deleteComment);
        return $response;
    } //end createProduct()

    /**
    * StoreSetupHelper:: createLike()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function deleteThread($formData = array(), $attribute = array())
    {
        $response                   =   array();

        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';

        $comment=CommentThread::find($formData['comment_id']);
        $comment->deleted_by=$formData['user_id'];
        $comment->save();


        $deleteComment              =   CommentThread::where('id',$formData['comment_id'])->delete();
        
        
        //Feeds create      
        $response       =   array('status' => "success", 'data' => $deleteComment);
        return $response;
    } //end createProduct()


    /**
    * StoreSetupHelper::createThread()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function createThread($formData = array(), $attribute = array())
    {
        $response                   =   array();
        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';

        list($message, $validate)   =   FeedSetupHelper::getCommentValidation($formData, $attribute);
        $validator                  =   Validator::make($formData, $validate, $message);

        // Check Validation
        if ($validator->fails()) {
            $response   =   array('status' => "error", 'validator' => $validator);
            return $response;
        }
    

        //Insert
        $insert=[
            'user_id'           =>      $formData['user_id']??'',
            'feed_id'           =>      $formData['feed_id']??'',
            'comment_id'        =>      $formData['comment_id']??'',
            'comment'           =>      $formData['comment']??'',
            'is_comment'        =>      config('globalConstant.comment')
        ];
           
        CommentThread::create($insert);
        $response['message']    =   'You have successfully commented on this thread.';
        
        //Feeds create      
        $response       =   array('status' => "success", 'data' => $response);
        return $response;

    } //end createProduct()




    /**
    * StoreSetupHelper:: createProduct()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function getFeedsListing($formData = array(), $attribute = array())
    {

        $response = $data           =   array();
        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';
        $page                       =   isset($formData['page'])?$formData['page']:1;
        $records                    =   isset($formData['records'])?$formData['records']:5;

        $feeds=Feed::with('productImages')->with('productVideos')
                            ->orderBy('created_at','desc')->paginate((int)$records, ['*'], 'page', $page)->toArray();

        //Fullname
        
        if(!empty($feeds) && count($feeds['data'])>0 ){
           for($i=0;$i<count($feeds['data']);$i++){
                // Most Liked Products
                $totalLikes=0;
                $totalComments=0;

                $like       =       Feed::whereHas('likes', function($query)  {
                                                $query->where('is_like',config('globalConstant.like'));
                                            })
                                        ->withCount(['likes' => function ($query) {
                                                $query->where('is_like', config('globalConstant.like'));
                                            }])
                                        ->find($feeds['data'][$i]['id']);
                                           
                $comment=Feed::with('comments')->whereHas('comments')->withCount('comments')->find($feeds['data'][$i]['id']);

                if(!empty($like)){
                    $likeData=$like->toArray();
                    $totalLikes=$likeData['likes_count']+$totalLikes;
                    $feeds['data'][$i]['total_likes']       =   $totalLikes;
                }
                else{
                    $feeds['data'][$i]['total_likes']       =   $totalLikes;   
                }

                if(!empty( $comment)){
                    $commentData=$comment->toArray();
                    $totalComments=$commentData['comments_count']+$totalComments;
                    $feeds['data'][$i]['total_comments']    =   $totalComments;
                }
                else{
                    $feeds['data'][$i]['total_comments']    =   $totalComments;
                }



                $userLike    =   Like::where('user_id',$formData['user_id'])
                                    ->where('feed_id',$feeds['data'][$i]['id'])
                                    ->where('is_like',1) 
                                    ->first();

                if(!empty($userLike)){
                    $feeds['data'][$i]['is_like']=1;
                }
                else{
                    $feeds['data'][$i]['is_like']=0;   
                }                    

                $fullname   =   CustomHelper::getFullName($feeds['data'][$i]['user_id']);
                $image      =   CustomHelper::getUserImage($feeds['data'][$i]['user_id']);
                
                $feeds['data'][$i]['fullname']      =    $fullname;
                $feeds['data'][$i]['profile']       =    config('globalConstant.image_path').$image;
           }
        }
        
      


        //Feeds create      
        $response       =   array('status' => "success", 'data' =>  $feeds );
        return $response;
    } //end createProduct()




    /**
    * StoreSetupHelper:: createProduct()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function getUserFeedsListing($formData = array(), $attribute = array())
    {

        $response = $data           =   array();
        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';
        $page                       =   isset($formData['page'])?$formData['page']:1;
        $records                    =   isset($formData['records'])?$formData['records']:5;
        $user_id                    =   $formData['user_id']??'';    
        
        $feeds=Feed::where('user_id',$user_id)->with('productImages')->with('productVideos')
                            ->orderBy('created_at','desc')->paginate((int)$records, ['*'], 'page', $page)->toArray();

        
        //Fullname
        
        if(!empty($feeds) && count($feeds['data'])>0 ){
           for($i=0;$i<count($feeds['data']);$i++){
                //dd($feeds[$i]['user_id']);lkdjaskd
                // Most Liked Products
                $totalLikes=0;
                $totalComments=0;

                $like=Feed::withCount('likes')->whereHas('likes', function($query)  {
                                            $query->where('is_like',config('globalConstant.like'));
                                        })->withCount('comments')->find($feeds['data'][$i]['id']);

                $comment=Feed::withCount('comments')->whereHas('comments')->withCount('comments')->find($feeds['data'][$i]['id']);

                if(!empty($like)){
                    $likeData=$like->toArray();
                    $totalLikes=$likeData['likes_count']+$totalLikes;
                    $feeds['data'][$i]['total_likes']       =   $totalLikes;
                }
                else{
                    $feeds['data'][$i]['total_likes']       =   $totalLikes;   
                }

                if(!empty( $comment)){
                    $commentData=$comment->toArray();
                    $totalComments=$commentData['comments_count']+$totalComments;
                    $feeds['data'][$i]['total_comments']    =   $totalComments;
                }
                else{
                    $feeds['data'][$i]['total_comments']    =   $totalComments;
                }



                $userLike    =   Like::where('user_id',$formData['user_id'])
                                    ->where('feed_id',$feeds['data'][$i]['id'])
                                    ->where('is_like',1) 
                                    ->first();


                if(!empty($userLike)){
                    $feeds['data'][$i]['is_like']=1;
                }
                else{
                    $feeds['data'][$i]['is_like']=0;   
                }                    

                $fullname   =   CustomHelper::getFullName($feeds['data'][$i]['user_id']);
                $image      =   CustomHelper::getUserImage($feeds['data'][$i]['user_id']);
                
                $feeds['data'][$i]['fullname']      =    $fullname;
                $feeds['data'][$i]['profile']       =    config('globalConstant.image_path').$image;
           }
        }
        
      


        //Feeds create      
        $response       =   array('status' => "success", 'data' =>  $feeds );
        return $response;
    } //end createProduct()





   



  public static function getTop($formData = array(), $attribute = array())
    {
        //dd($formData);
        $page                       =   isset($formData['page'])?$formData['page']:1;
        $perPage                    =   isset($formData['records'])?$formData['records']:5;


        $feeds=User::withCount('feeds')->with('feeds')->has('feeds')->get()->toArray();
        foreach ($feeds as $key => $value) {
            $total=0;
            if (isset($value['image']) && !empty($value['image']) && file_exists( config('globalConstant.shop_photo_path').$value['image'])) {
                        $feeds[$key]['image']       =    config('globalConstant.image_path').$value['image'];
                      }
                else{
                    $feeds[$key]['image']       = '';    
                }

            foreach ($value['feeds'] as $fkey => $fvalue) {
                $feedID=$fvalue['id'];
                $like=Feed::withCount('likes')->whereHas('likes', function($query)  {
                                            $query->where('is_like',config('globalConstant.like'));
                                        })->find($feedID);
                
                if(!empty($like)){
                    $likeData=$like->toArray();
                    $total=$likeData['likes_count']+$total;
                }
            }
            $feeds[$key]['total_likes']=$total;
        }


        //Like Score By Average
        foreach($feeds as $key=>$value){
            $feeds[$key]['avg']=$value['total_likes']/$value['feeds_count'];
        }


        $array = collect($feeds)->sortBy('avg')->reverse()->toArray();  
        
        $top=array_values($array);


        $items_per_page = $perPage ;
        $current_page = $page ?: ( Paginator::resolveCurrentPage() ?: 1);
        //dd($items_per_page,$current_page);
        $paginated_response = new LengthAwarePaginator(
            collect($top)->forPage($current_page, $items_per_page)->values(),
            count($top),
            $items_per_page,
            $current_page,
            //['path' => url('api/portfolios')]
        );



        $response       =   array('status' => "success", 'data' => $paginated_response);
        return $response;
  } //end createProduct()



  public static function getComments($formData = array(), $attribute = array())
    {

        $feedId=$formData['feed_id'];
        
        
        $page                       =   isset($formData['page'])?$formData['page']:1;
        $perPage                    =   isset($formData['records'])?$formData['records']:10;

        $feeds=Feed::with('comments')->whereHas('comments')->withCount('comments')->find($feedId);
        
        $comments=[];
        if(empty($feeds))
        {
            $response       =   array('status' => "success", 'data' => $comments );
            return $response;
        }    
        $comments=$feeds['comments'];
        

        //dd($comments->toArray());
        
        foreach($comments as $ckey => $cvalue ){
                $userData =  User::where('id',$cvalue['user_id'])->first()->toArray();
                $commentId=     $cvalue['id'];
        
                $thread  =  Comment::withCount('threads')->find($commentId)->toArray();
              
                $comments[$ckey]['thread_count']    =   $thread['threads_count'];    
                $comments[$ckey]['fullname']        =   $userData['firstname'].$userData['lastname'];
                if (isset($userData['image']) && !empty($userData['image']) && file_exists( config('globalConstant.shop_photo_path').$userData['image'])) {
                        $comments[$ckey]['image']        =    config('globalConstant.image_path').$userData['image'];
                }
                else{
                        $comments[$ckey]['image']     = '';    
                }

           }
            $items_per_page = $perPage ;
            $current_page = $page ?: ( Paginator::resolveCurrentPage() ?: 1);
            $paginated_response = new LengthAwarePaginator(
                collect($comments)->forPage($current_page, $items_per_page)->values(),
                count($comments),
                $items_per_page,
                $current_page,
                //['path' => url('api/portfolios')]
            );



        $response       =   array('status' => "success", 'data' => $paginated_response );
        return $response;
        
    
  } //end createProduct()



  public static function getThreads($formData = array(), $attribute = array())
    {
        $commentId=$formData['comment_id']??'';

        $threads=CommentThread::where('comment_id',$commentId)->get();
            
        
  
        //dd($comments->toArray());
        if(empty($threads))
        {
            $response       =   array('status' => "success", 'data' => 'No threads Found!' );
            return $response;
        }
        foreach($threads as $ckey => $cvalue ){
                $userData =  User::where('id',$cvalue['user_id'])->first()->toArray();
                
                $threads[$ckey]['fullname']=$userData['firstname'].$userData['lastname'];
                if (isset($userData['image']) && !empty($userData['image']) && file_exists( config('globalConstant.shop_photo_path').$userData['image'])) {
                        $threads[$ckey]['image']        =    config('globalConstant.image_path').$userData['image'];
                }
                else{
                    $threads[$ckey]['image']     = '';    
                }

           }
        
        
        $response       =   array('status' => "success", 'data' => $threads );
        return $response;
  } //end createProduct()









    /**
     * Upload a new video to the album
     * @return \Illuminate\Http\JsonResponse
     */
    public static function uploadVideos($video,$id)
    {
        // upload the video here
        //$attributes = request()->validate(Video::$rules);      
        // get the videoable
        
        $videoable = Feed::find($id);

        if (!$videoable) {
            return json_response_error('Whoops', 'We could not find the videoable.');
        }

        // move and create the video
        $video = self::moveAndCreateVideo($video, $videoable);

        if (!$video) {
            return false;
        }

        return true;        
    }

    /**
     * Save Image in Storage, crop image and save in public/uploads/images
     * @param UploadedFile $file
     * @param              $videoable
     * @param array        $size
     * @return PhotosController|bool|\Illuminate\Http\JsonResponse
     */
    public static function moveAndCreateVideo(
        UploadedFile $file,
        $videoable
    ) {
        $extension = '.' . $file->extension();

        $name = token();
        $filename = $name . $extension;

        $path = upload_path_videos();

        $file->move($path, $filename);

        $originalName = $file->getClientOriginalName();
        $originalName = substr($originalName, 0, strpos($originalName, $extension));
        $name = strlen($originalName) <= 2 ? $videoable->name : $originalName;
        $video = Video::create([
            'filename'       => $filename,
            'videoable_id'   => $videoable->id,
            'videoable_type' => get_class($videoable),
            'name'           => strlen($name) < 2 ? 'Video Name' : $name,
        ]);

        return $video;
    }


    /**
     * Upload a new photo to the album
     * @return \Illuminate\Http\JsonResponse
     */
    public static function uploadPhotos($photo,$id)
    {
        // upload the photo here
        //$attributes = request()->validate(Photo::$rules);
        $photoable = Feed::find($id);
        // get the photoable
        //$photoable = input('photoable_type')::find(input('photoable_id'));

        if (!$photoable) {
            return json_response_error('Whoops', 'We could not find the photoable.');
        }
        $photoResult = self::moveAndCreatePhoto($photo, $photoable);

        if (!$photoResult) {
            return false;
        }

        return true;
    }


     /**
     * Save Image in Storage, crop image and save in public/uploads/images
     * @param UploadedFile $file
     * @param              $photoable
     * @param array        $size
     * @return PhotosController|bool|\Illuminate\Http\JsonResponse
     */
    public static function moveAndCreatePhoto(
        UploadedFile $file,
        $photoable,
        $size = ['l' => [1024, 768], 's' => [320, 240]]
    ) {
        
        $extension = '.' . $file->extension();

        $name = token();
        $filename = $name . $extension;

        $path = upload_path_images();
        $imageTmp = Image::make($file->getRealPath());

        if (!$imageTmp) {
            return false;
        }

        if (isset($photoable::$LARGE_SIZE)) {
            $largeSize = $photoable::$LARGE_SIZE;
            $thumbSize = $photoable::$THUMB_SIZE;
        }
        else {
            $largeSize = $size['l'];
            $thumbSize = $size['s'];
        }

        // save original
        $imageTmp->save($path . $name . Photo::$originalAppend . $extension);

        // if height is the biggest - resize on max height
        if ($imageTmp->width() < $imageTmp->height()) {

            // resize the image to the large height and constrain aspect ratio (auto width)
            $imageTmp->resize(null, $largeSize[1], function ($constraint) {
                $constraint->aspectRatio();
            })->save($path . $filename);

            // resize the image to the thumb height and constrain aspect ratio (auto width)
            $imageTmp->resize(null, $thumbSize[1], function ($constraint) {
                $constraint->aspectRatio();
            })->save($path . $name . ImageThumb::$thumbAppend . $extension);
        }
        else {
            // resize the image to the large width and constrain aspect ratio (auto height)
            $imageTmp->resize($largeSize[0], null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path . $filename);

            // resize the image to the thumb width and constrain aspect ratio (auto width)
            $imageTmp->resize($thumbSize[0], null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path . $name . ImageThumb::$thumbAppend . $extension);
        }

        $originalName = $file->getClientOriginalName();
        $originalName = substr($originalName, 0, strpos($originalName, $extension));
        $name   = strlen($originalName) <= 2 ? $photoable->name : $originalName;
        $photo  = Photo::create([
            'filename'       => $filename,
            'photoable_id'   => $photoable->id,
            'photoable_type' => get_class($photoable),
            'name'           => strlen($name) < 2 ? 'Photo Name' : $name,
        ]);

        return $photo;
    }


    /**
    * StoreSetupHelper:: createProduct()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function showPreferences($formData = array(), $attribute = array())
    {
        $response = $data           =   array();
        $from                       =   $attribute['from']??'';
        $type                       =   $attribute['type']??'';

        dd( Product::with('category')->find(1)->toArray());


    } //end createProduct()


    /**
    * StoreSetupHelper:: getCategory()
    * @function for user Shop setup in site
    * @Used in overAll System
    * @param $form data as form data
    * @param $attribute as attribute array
    * @return response array
    */
    public static function getCategory($formData = array(), $attribute = array())
    {
		$create->features();

    }//end getCategory()



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // public static function paginate($items, $perPage , $page , $options = [])
    // {

    //     $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    //     $items = $items instanceof Collection ? $items : Collection::make($items);
    //     return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    // }

/**
 * Paginate a laravel colletion or array of items.
 *
 * @param  array|Illuminate\Support\Collection $items   array to paginate
 * @param  int $perPage number of pages
 * @return Illuminate\Pagination\LengthAwarePaginator    new LengthAwarePaginator instance 
 */
public static function paginate($items, $perPage,$page)
{
    if(is_array($items)){
        $items = collect($items);
    }
    //dump($items);
    return new LengthAwarePaginator(
        $items->forPage( $page ?: ( Paginator::resolveCurrentPage() ?: 1)   , $perPage),
        $items->count(), $perPage,
        Paginator::resolveCurrentPage(),
        ['path' => Paginator::resolveCurrentPath()]
    );
}


}// end StoreSetupHelper
