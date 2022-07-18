<?php

namespace App\Http\Controllers\Admin\Pages;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Page;
use App\Models\Content;
use App\Models\Traits\ImageThumb;
use App\Http\Controllers\Admin\AdminController;
use Intervention\Image\Facades\Image;

class PageContentController extends AdminController
{
    /**
     * Display a listing of content.
     *
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Page $page)
    {
        save_resource_url();

        $page->load('sections');

        return $this->view('pages.components.page_components')->with('page', $page);
    }

    /**
     * Show the form for creating a new content.
     *
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Page $page)
    {
        return $this->view('pages.components.content')->with('page', $page);
    }

    /**
     * Store a newly created news in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {


        if (request()->file('media') === null) {
            $attributes = request()->validate(Arr::except(Content::$rules, 'media'),
                Content::$messages);
        }
        else if($request->has('video_title')){
            $filenameWithExt= $request->file('video_title')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('video_title')->getClientOriginalExtension();
            //$fileNameToStore = $filename. '_'.time().'.'.$extension;
             $file = $request->file('video_title');
             $fileNameToStore=$file->getClientOriginalName();
             $destinationPath =  public_path('uploads');
             
            $file->move($destinationPath,$file->getClientOriginalName());
        }else {
            $attributes = request()->validate(Content::$rules, Content::$messages);

            $media = $this->moveAndCreatePhoto($attributes['media'], $size = ['l' => Content::$LARGE_SIZE, 's' => Content::$THUMB_SIZE]);
            if ($media) {
                $attributes['media'] = $media;
            }
        }
        $attributes['video_title'] = isset($fileNameToStore)?$fileNameToStore:'nope';
        $Content = $this->createEntry(Content::class, $attributes);
        return redirect('admin/pages/' . $request->page_id . '/sections/content/' . $Content->id . '/edit');
    }

    /**
     * Show the form for editing the specified content.
     *
     * @param Page        $page
     * @param Content $content
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Page $page, Content $content)
    {
        return $this->view('pages.components.content')->with('page', $page)->with('item', $content);
    }

    /**
     * Update the specified content in storage.
     *
     * @param Page        $page
     * @param Content $content
     * @return Response
     */

    public function update(Page $page, Content $content)
    {
        
        if (is_null(request()->file('media'))) {            
            
            $attributes = request()->validate(Arr::except(Content::$rules, 'media'),
                Content::$messages);
        }
        
        else {
            //die('asdfeee');
            $attributes = request()->validate(Content::$rules, Content::$messages);

            $media = $this->moveAndCreatePhoto($attributes['media'], $size = ['l' => Content::$LARGE_SIZE, 's' => Content::$THUMB_SIZE]);
            if ($media) {
                $attributes['media'] = $media;
            }
            //prd($attributes);
        }
        if (is_null(request()->file('video_title'))) {
            $fileNameToStore = "no video";           
        }
        else{
            $filenameWithExt= $request->file('video_title')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('video_title')->getClientOriginalExtension();
            //$fileNameToStore = $filename. '_'.time().'.'.$extension;
             
             $file = $request->file('video_title');
             $fileNameToStore=$file->getClientOriginalName();
             $destinationPath = 'uploads';
            $file->move($destinationPath,$file->getClientOriginalName());
        }

        $attributes['video_title'] = isset($fileNameToStore)?$fileNameToStore:'nope';
        unset($attributes['page_id']);
        $item = $this->updateEntry($content, $attributes);

        return redirect_to_resource();
    }

    /**
     * Remove the specified content from storage.
     *
     * @param Page        $page
     * @param Content $section
     * @return Response
     * @internal param $page_section
     */
    public function destroy(Page $page, Content $section)
    {
        // delete page_content
        $this->deleteEntry($section, request());

        log_activity('Page Component Deleted',
            'A Page Content was successfully removed from the Page', $section);

        return redirect_to_resource();
    }

    /**
     * @param Page $page
     * @return array
     */
    public function updateOrder(Page $page)
    {
        $items = json_decode(request('list'), true);

        foreach ($items as $key => $item) {

            $row = Content::find($item['id']);
            if ($row) {
                $row->update([
                    'list_order' => ($key + 1)
                ]);
            }
        }

        return ['result' => 'success'];
    }

    /**
     * @param Page        $page
     * @param Content $content
     * @return array
     */
    public function removeMedia(Page $page, Content $content)
    {
        $content->media = null;
        //$content->video_title=null;
        $content->save();

        return ['result' => 'success'];
    }
    public function removeVideo(Page $page, Content $content)
    {
        $content->video_title = null;
        $content->save();

        return ['result' => 'success'];
    }

    /**
     * Save Image in Storage, crop image and save in public/uploads/images
     * @param UploadedFile $file
     * @param array        $size
     * @return PageContentController|bool|\Illuminate\Http\JsonResponse
     */
    private function moveAndCreatePhoto(
        UploadedFile $file, $size = ['l' => [1024, 768], 's' => [600, 240]]
    ) {
        $extension = '.' . $file->extension();

        $name = token();
        $filename = $name . $extension;

        $path = upload_path_images();
        $imageTmp = Image::make($file->getRealPath());

        if (!$imageTmp) {
            return false;
        }

        $largeSize = $size['l'];
        $thumbSize = $size['s'];

        // save original
        $imageTmp->save($path . $name . ImageThumb::$originalAppend . $extension);

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

        return $filename;
    }
}
