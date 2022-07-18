<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
|------------------------------------------
| PUBLIC API
|------------------------------------------
*/
Route::group(['namespace' => 'Api'], function () { // 
    // notifications
    Route::group(['prefix' => 'notifications',], function () {
        Route::post('/{user}', 'NotificationsController@index');
        Route::post('/{user}/unread', 'NotificationsController@unread');
        Route::post('/{user}/read/{notification}', 'NotificationsController@read');
        Route::post('/actions/latest', 'NotificationsController@getLatestActions');
    });

    // analytics
    Route::group(['prefix' => 'analytics'], function () {
        Route::post('/keywords', 'AnalyticsController@getKeywords');
        Route::post('/visitors', 'AnalyticsController@getVisitors');
        Route::post('/browsers', 'AnalyticsController@getBrowsers');
        Route::post('/referrers', 'AnalyticsController@getReferrers');
        Route::post('/page-load', 'AnalyticsController@getAvgPageLoad');
        Route::post('/bounce-rate', 'AnalyticsController@getBounceRate');
        Route::post('/visited-pages', 'AnalyticsController@getVisitedPages');
        Route::post('/active-visitors', 'AnalyticsController@getActiveVisitors');
        Route::post('/unique-visitors', 'AnalyticsController@getUniqueVisitors');
        Route::post('/visitors-views', 'AnalyticsController@getVisitorsAndPageViews');
        Route::post('/visitors/locations', 'AnalyticsController@getVisitorsLocations');

        Route::post('/age', 'AnalyticsController@getUsersAge');
        Route::post('/devices', 'AnalyticsController@getDevices');
        Route::post('/gender', 'AnalyticsController@getUsersGender');
        Route::post('/device-category', 'AnalyticsController@getDeviceCategory');

        Route::post('/interests-other', 'AnalyticsController@getInterestsOther');
        Route::post('/interests-market', 'AnalyticsController@getInterestsMarket');
        Route::post('/interests-affinity', 'AnalyticsController@getInterestsAffinity');
    });
        Route::group(['middleware' => ['jwt.verify','active_user']], function() {

            //user and auth api's
            Route::post('/step1','AuthApiController@profileStepOne'); 
            Route::get('/get_country','AuthApiController@getCountry'); 
            Route::post('/get_state','AuthApiController@getStates'); 
            Route::post('/get_city','AuthApiController@getCity'); 
            Route::post('/store_address','AuthApiController@userAddress'); 
            Route::get('/get_interest','AuthApiController@getInterest'); 
            Route::post('/user_interest','AuthApiController@userInterest'); 
            Route::post('/edit_profile','AuthApiController@updateProfile'); 
            Route::post('/change_password','AuthApiController@changePassword'); 
            Route::post('/user_info','AuthApiController@UserInfoData'); 
            Route::get('/user_data','AuthApiController@UserData'); 
            Route::post('/discover','AuthApiController@discoverUser'); 
            Route::post('/change_cellphone','AuthApiController@changeMobileNumber'); 
            // Route::post('/varification_cellphone','AuthApiController@varification_cellphone'); 
            Route::post('/push_notification','AuthApiController@notificatoinSetting'); 

            //post and videos api's
            Route::post('/create_post','PostController@CreatePost'); 
            Route::post('/delete_post','PostController@deletePost'); 
            Route::get('/all_posts','PostController@homePageVideos'); 
            Route::post('/post_like','PostController@postLike'); 
            Route::post('/post_view','PostController@postCount'); 
            Route::post('/comment_post','PostController@CommentPost'); 
            Route::post('/reply_comment','PostController@CommentOnComment'); 
            Route::get('/get_comment','PostController@getComment'); 
            Route::post('/like_comment','PostController@likeComment'); 
            Route::post('/get_child_comment','PostController@getChildComment'); 
            Route::post('/delete_comment','PostController@deleteComment'); 
            Route::post('/report_post','PostController@reportPost'); 
            Route::get('/get_report_list','PostController@getReportList'); 
            Route::post('/upload_post','PostController@uploadPost'); 
            Route::post('/create_all_thumbnail','PostController@CreateAllThumb'); 
            Route::post('/user_post_videos','PostController@getUserPost'); 
            Route::post('/user_like_videos','PostController@getLikeVideos'); 
            Route::post('/user_upload_videos','PostController@UserPostVideos'); 
            Route::post('/user_liked_video','PostController@UserLikeVideos'); 
            Route::post('/sponsored_videos','PostController@sponserdVideoData'); 
            Route::post('/tag_videos','PostController@tagVideoData'); 
            Route::post('/search_sponsored_video','PostController@searchSponserdVideo'); 
            Route::post('/video_share','PostController@videoShare'); 


            //discover module api's
            Route::post('/discover','DiscoverController@discover'); 
            Route::post('/discover/tags','DiscoverController@tags'); 
            Route::post('/discover/followings','DiscoverController@following'); 
            Route::any('/discover/recommanded','DiscoverController@recommondation'); 
            Route::get('/discover/popular_tags','DiscoverController@popularTags'); 
            Route::post('/discover/search','DiscoverController@topSearch'); 
            Route::post('/discover/tag_video','DiscoverController@tagVideos'); 
            Route::post('/discover/sponsored','DiscoverController@sponsored'); 
            Route::get('/discover/popular_videos','DiscoverController@popularVideos'); 


            //follow and unfollow
            Route::post('/follow','FollowerController@followAndUnfollow'); 
            Route::post('/remove_follwer','FollowerController@removeFollwer'); 
            Route::post('/following_list','FollowerController@followingList'); 
            Route::post('/follower_list','FollowerController@followerList'); 
            Route::post('/search_following_list','FollowerController@searchFollowing'); 
            Route::post('/search_follower_list','FollowerController@searchFollower'); 

            //block and unblock
            Route::post('/block','BlockController@blockAndUnblock'); 
            Route::get('/get_block_list','BlockController@blockList'); 


            //message
            Route::post('all-messages', 'MessageController@allMsg');
            Route::post('send-messages', 'MessageController@store');
            Route::get('conversation_list', 'MessageController@allConversation');
            Route::post('conversation_list', 'MessageController@allSearchConversation');

            Route::post('like_chat', 'MessageController@chatLike');
            Route::post('clear_chat', 'MessageController@clearChat');
            Route::post('delete_chat', 'MessageController@deleteChat');
            Route::post('search_chat', 'MessageController@searchChat');


            //group-msg
            Route::post('create_group', 'MessageController@createGroup');
            Route::get('group_conversation_list', 'MessageController@allGroupConversation');
            Route::post('group_conversation_list', 'MessageController@allSearchGroupConversation');
            Route::post('add_members', 'MessageController@addMember');
            Route::post('sync_contact', 'MessageController@syncContact');
            Route::post('leave_group', 'MessageController@leaveGroup');
            Route::post('delete_group', 'MessageController@deleteGroup');

            Route::post('all_members', 'MessageController@allMembers');
            Route::post('search_all_members', 'MessageController@searchAllMembers');
            Route::get('my_friends', 'MessageController@allFriends');
            Route::post('edit_group', 'MessageController@editGroup');
            Route::get('leave_group', 'MessageController@leaveGroup');
            Route::post('active_deative_group','MessageController@groupActiveInactive'); 
            Route::post('active_deative_members','MessageController@membersActiveInactive'); 

            Route::post('clear_group_chat','MessageController@clearGroupChat'); 
            Route::post('group_serach','MessageController@groupSearch'); 
            Route::get('all_requests', 'MessageController@allRequest');
            Route::post('group_request','MessageController@acceptRejectGroup'); 
            Route::post('mute_conversation','MessageController@muteConversation'); 

            
            // Route::get('all_group', 'MessageController@createGroup');

            //Notification Route
            Route::get('get_notfication','MessageController@getAllNotification'); 

            
// CommentOnComment
    });
        Route::post('/otp_verification','AuthApiController@otpVarification'); 
        Route::post('/login', 'AuthApiController@login'); 
        Route::post('/sign_up','AuthApiController@signUp'); 
        Route::post('/otpVarification','AuthApiController@otpVarification'); 
        Route::post('/social_login','AuthApiController@socialLogin'); 
        Route::post('/update_social_data','AuthApiController@UpdateSocial'); 
        Route::post('/social_otp_varification','AuthApiController@socialOtpVerification'); 

        Route::post('/forget_password','AuthApiController@forgetPassword'); 
        Route::get('/faq','AuthApiController@faqData'); 
        Route::post('/reset_password','AuthApiController@resetPassword'); 
        Route::get('/term_and_service','AuthApiController@termAndService'); 
        Route::get('/info_content','AuthApiController@infoContent'); 
        Route::get('/event_data','AuthApiController@eventData'); 
        Route::get('/video_stream/{name}','PostController@videoStream'); 


    // Route::any('/index', array('as'=>'Api.index','uses'=>'ApiController@index')); 
});