<?php

use Illuminate\Support\Facades\Route;

/*
|------------------------------------------
| Admin (when authorized and admin)
|------------------------------------------
*/
Route::group(['middleware' => ['auth', 'auth.admin'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::any('/', 'DashboardController@index')->name('admin');

            //comments
    Route::group([ 'namespace' => 'Comments'], function () {
        Route::resource('comments', 'CommentsController');
    });

    // profile
    Route::get('/profile', 'ProfileController@index');
    Route::put('/profile/{user}', 'ProfileController@update');

    // analytics
    Route::group(['prefix' => 'analytics'], function () {
        Route::get('/summary', 'AnalyticsController@summary');
        Route::get('/devices', 'AnalyticsController@devices');
        Route::get('/visits-and-referrals', 'AnalyticsController@visitsReferrals');
        Route::get('/interests', 'AnalyticsController@interests');
        Route::get('/demographics', 'AnalyticsController@demographics');
    });

    // EmailTemplate
    Route::namespace('EmailTemplate')->group(function () {
        Route::get('/email_template', 'EmailTemplateController@listTemplate');
        Route::get('/email_template/create', 'EmailTemplateController@addTemplate')->name('EmailTemplate.add_template');
        Route::post('/email_template/save/{id?}', 'EmailTemplateController@saveTemplate')->name('EmailTemplate.save_template');
        Route::get('/email_template/edit/{id}', 'EmailTemplateController@editTemplate')->name('EmailTemplate.edit_template');
        Route::post('/email_template/get_constant', 'EmailTemplateController@getConstant')->name('EmailTemplate.getConstant');
        Route::delete('/email_template/delete/{id}', 'EmailTemplateController@destroy')->name('EmailTemplate.deleteTemplate');
    });

    // banners
    Route::namespace('Banners')->group(function () {
        Route::get('/banners/order', 'OrderController@index');
        Route::post('/banners/order', 'OrderController@update');
        Route::resource('/banners', 'BannersController');
    });

    // faq
    Route::namespace('FAQ')->group(function () {
        Route::resource('/faqs/categories', 'CategoriesController')
            ->names([
                'index' => 'faqs_categories.index',
                'create' => 'faqs_categories.create',
                'store' => 'faqs_categories.store',
                'show' => 'faqs_categories.show',
                'edit' => 'faqs_categories.edit',
                'update' => 'faqs_categories.update',
                'destroy' => 'faqs_categories.destroy',
            ]);
        Route::get('/faqs/order', 'OrderController@index');
        Route::post('/faqs/order', 'OrderController@update');
        Route::resource('/faqs', 'FAQsController');
    });

    //locations
    Route::group(['prefix' => 'locations', 'namespace' => 'Locations'], function () {
        //Route::resource('branches', 'BranchesController');
        Route::resource('suburbs', 'SuburbsController');
        Route::resource('cities', 'CitiesController');
        Route::resource('provinces', 'ProvincesController');
        Route::resource('countries', 'CountriesController');
        Route::resource('continents', 'ContinentsController');
    });

    // history
    Route::group(['prefix' => 'activities', 'namespace' => 'LatestActivities'], function () {
        Route::get('/', 'LatestActivitiesController@website');
        Route::get('/admin', 'LatestActivitiesController@admin');
        Route::get('/website', 'LatestActivitiesController@website');
    });

    // pages
    Route::group(['prefix' => 'pages', 'namespace' => 'Pages'], function () {
        Route::get('/order/{type?}', 'OrderController@index');
        Route::post('/order/{type?}', 'OrderController@updateOrder');

        // // manage page sections list order
        // Route::get('/{page}/sections', 'PageContentController@index');
        // Route::post('/{page}/sections/order', 'PageContentController@updateOrder');
        // Route::delete('/{page}/sections/{section}', 'PageContentController@destroy');

        // // page components
        // Route::resource('/{page}/sections/content', 'PageContentController');
        // //remove content media
        // Route::post('/{page}/sections/content/{content}/removeMedia', 'PageContentController@removeMedia');

    });
    Route::resource('pages', 'Pages\PagesController');

    // news and events
    Route::group(['prefix' => 'news', 'namespace' => 'News'], function () {
        Route::resource('articles', 'NewsController');
        Route::resource('categories', 'CategoriesController')
            ->names([
                'index' => 'news_categories.index',
                'create' => 'news_categories.create',
                'store' => 'news_categories.store',
                'show' => 'news_categories.show',
                'edit' => 'news_categories.edit',
                'update' => 'news_categories.update',
                'destroy' => 'news_categories.destroy',
            ]);
    });

    // products
    Route::group(['prefix' => 'shop', 'namespace' => 'Shop'], function () {
        Route::get('categories/order', 'CategoriesOrderController@index');
        Route::post('categories/order', 'CategoriesOrderController@updateListOrder');
        Route::resource('categories', 'CategoriesController')
            ->names([
                'index' => 'shop_categories.index',
                'create' => 'shop_categories.create',
                'store' => 'shop_categories.store',
                'show' => 'shop_categories.show',
                'edit' => 'shop_categories.edit',
                'update' => 'shop_categories.update',
                'destroy' => 'shop_categories.destroy',
            ]);
        Route::resource('products', 'ProductsController');
        Route::resource('features', 'FeaturesController');
        Route::resource('status', 'StatusesController');

        Route::get('checkouts', 'CheckoutsController@index');
        Route::get('checkouts/{checkout}', 'CheckoutsController@show');
        Route::get('transactions', 'TransactionsController@index');
        Route::get('transactions/{transaction}', 'TransactionsController@show');
        Route::get(
            'transactions/{transaction}/print/{format?}',
            'TransactionsController@printOrder'
        );
        Route::post('transactions/{transaction}/status', 'TransactionsController@updateStatus');

        Route::get('/searches', 'SearchesController@index');
        Route::get('/searches/datatable', 'SearchesController@getTableData');
        Route::post('/searches/datatable/dates', 'SearchesController@updateDates');
        Route::get('/searches/datatable/reset', 'SearchesController@resetDates');
    });


    Route::group(['prefix' => 'advertisement', 'namespace' => 'Advertisment'], function () {
        Route::get('{id}/videoUpload', 'AdvertismentController@uploadVideos');
        Route::post('{id}/videoUpload/upload', 'AdvertismentController@uploadRealVideos')->name('upload_chunk');
        Route::get('{id}/edit', 'AdvertismentController@edit');
        Route::PUT('{id}', 'AdvertismentController@update');
        Route::Delete('{id}', 'AdvertismentController@destroy');
        Route::resource('/', 'AdvertismentController');
    });
    Route::group(['prefix' => 'stores', 'namespace' => 'SellerStores'], function () {

        //Seller Requests
        Route::get('sellers', 'StoresController@index');
        Route::get('sellers/{id}/edit', 'StoresController@edit');
        Route::get('sellers/{id}', 'StoresController@show');
        Route::put('sellers/{id}', 'StoresController@update');
        Route::any('sellers/status', 'StoresController@updateStatus');
        Route::delete('sellers/{id}', 'StoresController@destroy');
        
        //Features
        Route::get('features', 'FeaturesController@index');
        Route::get('features/create', 'FeaturesController@create');
        Route::post('features', 'FeaturesController@store');

        Route::get('features/{id}/edit', 'FeaturesController@edit');
        Route::put('features/{id}', 'FeaturesController@update');
        Route::delete('features/{id}', 'FeaturesController@destroy');

        //Subfeatures
        Route::get('features/subfeatures/{id}', 'FeaturesController@show')->name('subfeatures.show');
        Route::get('features/subfeatures/{id}/create', 'FeaturesController@subcreate');
        Route::any('features/subfeatures/save/{id}', 'FeaturesController@subsave')->name('subfeatures.insert');
        
        Route::get('features/subfeatures/{id}/edit', 'FeaturesController@subedit');
        Route::put('features/subfeatures/{id}', 'FeaturesController@subupdate')->name('subfeatures.update');

        Route::delete('features/subfeatures/{id}', 'FeaturesController@subdestroy')->name('subfeatures.delete');

        Route::resource('/categories', 'CategoriesController')
        ->names([
            'index'         =>       'products_categories.index',
            'create'        =>       'products_categories.create',
            'store'         =>       'products_categories.store',
            'show'          =>       'products_categories.show',
            'edit'          =>       'products_categories.edit',
            'update'        =>       'products_categories.update',
            'destroy'       =>       'products_categories.destroy',
        ]);

         //Products
         Route::resource('/products', 'ProductsController')
         ->names([
             'index'        =>      'products.index',
             'create'       =>      'products.create',
             'store'        =>      'products.store',
             'show'         =>      'products.show',
             'edit'         =>      'products.edit',
             'update'       =>      'products.update',
             'destroy'      =>      'products.destroy',
         ]);    
    });
    
    // reports
    Route::group(['prefix' => 'reports', 'namespace' => 'Reports'], function () {
        Route::get('summary', 'SummaryController@index');
        // feedback contact us
        Route::get('contact-us', 'ContactUsController@index');
        Route::post('contact-us/chart', 'ContactUsController@getChartData');
        Route::get('contact-us/datatable', 'ContactUsController@getTableData');

        Route::resource('/report-list', 'ReportListController');

    });

    // accounts
    Route::group(['prefix' => 'accounts', 'namespace' => 'Accounts'], function () {
        // clients
        Route::resource('clients', 'ClientsController');
        Route::post('clients/{id}/status', 'ClientsController@updateStatus');
        Route::get('clients/{id}/status', 'ClientsController@show');
        Route::get('client_video/{id}/{status}/status', 'ClientsController@clientVideoStatus')->name('back.client_video.status');
        Route::get('client_video/{id}/delete', 'ClientsController@clientVideoDelete')->name('back.client_video.delete');

        // users
        Route::get('administrators', 'AdministratorsController@index');
        Route::delete('administrators', 'AdministratorsController@destroy');
    });

    Route::group(['prefix' => 'group-management', 'namespace' => 'Accounts'], function () {
        // clients
        Route::get('/{id}/{status}', 'GroupManagementController@status')->name('back.group.status');
        Route::get('/{id}', 'GroupManagementController@showMembers');
        Route::resource('/', 'GroupManagementController');

        // Route::post('clients/{id}/status', 'ClientsController@updateStatus');
        // Route::get('clients/{id}/status', 'ClientsController@show');

        // users
        Route::get('administrators', 'AdministratorsController@index');
        Route::delete('administrators', 'AdministratorsController@destroy');
    });



    // settings
    Route::group(['prefix' => 'settings', 'namespace' => 'Settings'], function () {
        Route::resource('roles', 'RolesController');

        Route::resource('settings', 'SettingsController');

        Route::resource('templates', 'TemplatesController');

        Route::resource('layouts', 'LayoutsController');

        // navigation
        Route::get('navigations/order', 'NavigationOrderController@index');
        Route::post('navigations/order', 'NavigationOrderController@updateOrder');
        Route::resource('navigations', 'NavigationsController');
    });

    Route::group(['namespace' => 'Resources'], function () {

        // resource image crop - featured image (single image file name in resource table) - for banners
        Route::get('/{resourceable}/{resource}/crop-resource/', 'CropResourceController@showPhoto');

        // get resources - new photoable, documentable, videoable
        Route::get('/{resourceable1}/{resourceable2}/{resource}/resources', 'ResourceController@showResource');

        Route::group(['prefix' => 'resources'], function () {
            // resource categories
            Route::resource('/categories', 'CategoriesController')
                ->names([
                    'index'         =>       'resources_categories.index',
                    'create'        =>       'resources_categories.create',
                    'store'         =>       'resources_categories.store',
                    'show'          =>       'resources_categories.show',
                    'edit'          =>       'resources_categories.edit',
                    'update'        =>       'resources_categories.update',
                    'destroy'       =>       'resources_categories.destroy',
                ]);

            //photos - list, delete, upload, edit, cover
            Route::get('/photos', 'PhotosController@index');
            Route::delete('/photos/{photo}', 'PhotosController@destroy');
            Route::post('/photos/upload', 'PhotosController@uploadPhotos');
            Route::post('/photos/{photo}/edit/name', 'PhotosController@updatePhotoName');
            Route::post('/photos/{photo}/cover', 'PhotosController@updatePhotoCover');

            //photos order
            Route::get('/photos/{resourceable}/{resource}/order', 'PhotosOrderController@showPhotos');
            Route::post('/photos/order', 'PhotosOrderController@update');

            // attach existing photos
            Route::post('/photos/attach', 'PhotosController@attach');

            // croppers
            Route::get('/photos/crop/{photo}', 'CropperController@showPhotos');
            Route::post('/photos/crop/{photo}', 'CropperController@cropPhoto');

            // resource image crop - featured image (single image file name in resource table) - for page content
            Route::get('/{resourceable}/{resource}/crop-resource/', 'CropResourceController@showPhoto');
            Route::post('/photos/crop-resource', 'CropResourceController@cropPhoto');

            //videos - list, create, edit, destroy, getInfo, cover
            Route::get('/videos', 'VideosController@index');
            Route::post('/videos/create', 'VideosController@store');
            Route::post('/videos/{video}/edit', 'VideosController@update');
            Route::delete('/videos/{video}', 'VideosController@destroy');
            Route::post('/videos/{video}/getInfo', 'VideosController@videoInfo');
            Route::post('/videos/{video}/cover', 'VideosController@updateVideoCover');
            //upload videos
            Route::post('/videos/upload', 'VideosController@uploadVideos');
            Route::post('/videos/{video}/edit/name', 'VideosController@updateVideoName');
            // attach existing videos
            Route::post('/videos/attach', 'VideosController@attach');

            //videos order
            Route::get('/videos/{resourceable}/{resource}/order', 'VideosOrderController@showVideos');
            Route::post('/videos/order', 'VideosOrderController@update');

            //documents - list, destroy, upload, edit
            Route::get('/documents', 'DocumentsController@index');
            Route::delete('/documents/{document}', 'DocumentsController@destroy');
            Route::post('/documents/upload', 'DocumentsController@upload');
            Route::post('/documents/{document}/edit/name', 'DocumentsController@updateName');
            //documents order
            Route::get('/documents/{resourceable}/{resource}/order', 'DocumentsOrderController@showDocuments');
            Route::post('/documents/order', 'DocumentsOrderController@update');
            // attach existing documents
            Route::post('/documents/attach', 'DocumentsController@attach');
        });

        // sections
        Route::resource('/{resourceable}/{resource}/sections', 'SectionsController');
        Route::post('/{resourceable}/{resource}/sections/{section}/content/attach', 'SectionsController@attach');
        // order sections
        Route::post('/{resourceable}/{resource}/sections/order', 'SectionsController@updateOrder');
        //content
        Route::resource('/{resourceable}/{resource}/sections/{section}/content', 'ContentController');
        Route::post('/{resourceable}/{resource}/sections/{section}/content/{content}/remove', 'ContentController@remove');
        //order content
        Route::post('/{resourceable}/{resource}/sections/{section}/content/order', 'ContentController@updateOrder');
        //remove content media
        Route::post('/{resourceable}/{resource}/sections/{section}/content/{content}/removeMedia', 'ContentController@removeMedia');
        //view contents
        Route::get('/resources/content/', 'ContentController@show');
        //delete content
        Route::delete('/content/{content}', 'ContentController@deleteContent');

    });


    Route::group(['namespace' => 'notification','prefix' => 'push-notifications'], function ()
    {
        #####  notifications MODULE  ROUTING START HERE
        Route::any('/', array(
            'as' => 'PushNotification.index',
            'uses' => 'AdminPushNotificationController@listTemplate'
        ));
        Route::get('create', array(
            'as' => 'PushNotification.add',
            'uses' => 'AdminPushNotificationController@addTemplate'
        ));
        Route::post('add-push-notifications', array(
            'as' => 'PushNotification.save',
            'uses' => 'AdminPushNotificationController@saveTemplate'
        ));
        Route::get('edit/{id}', array(
            'as' => 'PushNotification.edit',
            'uses' => 'AdminPushNotificationController@editTemplate'
        ));
        Route::post('edit-push-notifications/{id}', array(
            'as' => 'PushNotification.update',
            'uses' => 'AdminPushNotificationController@updateTemplate'
        ));
        Route::post('/push-notifications/get_constant', 'AdminPushNotificationController@getConstant')
            ->name('PushNotification.getConstant');
        Route::any('delete-push-notifications/{id}', array(
            'as' => 'PushNotification.delete',
            'uses' => 'AdminPushNotificationController@deletePushNotification'
        ));
    });


    Route::get('testimonials', 'Testimonials\TestimonialController@list')->name('testimonials.list');
    Route::match(['get', 'post'],'testimonials/create', 'Testimonials\TestimonialController@add')->name('testimonials.add');
    Route::match(['get', 'post'],'testimonials/edit/{id}/{page?}', 'Testimonials\TestimonialController@edit')->name('testimonials.edit');
    Route::get('testimonials/delete/{id}', 'Testimonials\TestimonialController@delete')->name('testimonials.delete');


});
