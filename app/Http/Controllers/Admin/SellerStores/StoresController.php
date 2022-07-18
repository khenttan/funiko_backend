<?php

namespace App\Http\Controllers\Admin\SellerStores;

use App\Models\Stores;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\UploadedFile;

use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
class StoresController extends AdminController
{
    
    /**
     * Display a listing of banner.
     *
     * @return Factory|View
     */
    public function index()
    {
        save_resource_url();

        return $this->view('stores.requests.index')->with('items', Stores::all());
    }

    /**
     * Update the status
     * @param Orders $Orders
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        //update query

        Stores::where('id',$request->id)->update([
            'is_approved'       =>      $request->status,
            'admin_reply'       =>      isset($request->reply)?$request->reply:''
        ]);
        notify()->success('Successfully','Store status updated successfully!');
        return json_response();

    }

    /**
     * Display the specified banner.
     *
     * @param Banner $banner
     * @return Factory|View
     */
    public function show($id)
    {
        $stores=Stores::find($id);
        return $this->view('stores.requests.show')->with('item', $stores);
    }

    /**
     * Show the form for editing the specified banner.
     *
     * @param Banner $banner
     * @return Factory|View
     */
    public function edit($id)
    {
        $stores=Stores::find($id);
        return $this->view('stores.requests.create_edit')->with('item', $stores);
    }

    /**
     * Update the specified banner in storage.
     *
     * @param Banner $banner
     * @return RedirectResponse|Redirector
     */
    public function update($id,Request $request)
    {
        if (request()->file('shop_photo') === null) {
            $attributes = request()->validate(
                Arr::except(Stores::$rules, 'shop_photo'),
                Stores::$messages
            );
        } else {
            $attributes = request()->validate(Stores::$rules, Stores::$messages);

            $photo = $this->uploadImage($attributes['shop_photo']);
            if ($photo) {
                $attributes['shop_photo'] = $photo;
            }
        }

        $stores=Stores::find($id);
        
        $this->updateEntry($stores, $attributes);

        return redirect_to_resource();
    }

    /**
     * Remove the specified store from storage.
     *
     * @param Store $id
     * @return RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        $store=Stores::find($id);
        
        $this->deleteEntry($store, request());

        return redirect_to_resource();
    }
     /**
     * Upload the banner image, create a thumb as well
     *
     * @param        $file
     * @param string $path
     * @param array  $size
     * @return string|void
     */
    private function uploadImage(
        UploadedFile $file, $path = '', $size = ['o' => [], 'tn' => []]
    ) {
        
        $data = getimagesize($file);
        $width = $data[0];
        $height = $data[1];

        $name = token();
        $extension = $file->guessClientExtension();


        $filename = $name . '.' . $extension;
        $filenameThumb = $name . '-tn.' . $extension;
        $imageTmp = Image::make($file->getRealPath());

        if (!$imageTmp) {
            return notify()->error('Oops', 'Something went wrong', 'warning shake animated');
        }

        $path = upload_path_images($path);

        // original
        $imageTmp->save($path . $name . '-o.' . $extension);

        // save the image
        $image = $imageTmp->fit($width, $height)->save($path . $filename);


        $image->fit($width, $height)->save($path . $filenameThumb);

        return $filename;
    }

}
