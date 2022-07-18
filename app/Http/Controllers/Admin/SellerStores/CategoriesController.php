<?php

namespace App\Http\Controllers\Admin\SellerStores;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Intervention\Image\Facades\Image;
use Redirect;
use App\Http\Requests;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

use Illuminate\Validation\Rule;
class CategoriesController extends AdminController
{
    /**
     * Display a listing of product_category.
     *
     * @return Response
     */
    public function index()
    {
        save_resource_url();

        $items = ProductCategory::with('parent')->get();
        
        return $this->view('stores.categories.index')->with('items', $items);
    }

    /**
     * Show the form for creating a new product_category.
     *
     * @return Response
     */
    public function create()
    {
        $parents = ProductCategory::getAllList();

        return $this->view('stores.categories.create_edit')->with('parents', $parents);
    }

    /**
     * Store a newly created product_category in storage.
     *
     * @return Response
     */
    public function store()
    {
        $attributes = request()->validate(ProductCategory::$rules, ProductCategory::$messages);
    
        $category = $this->createEntry(ProductCategory::class, $attributes);
        $category->updateUrl()->save();

        return redirect_to_resource();
    }

    /**
     * Display the specified product_category.
     *
     * @param ProductCategory $category
     * @return Response
     */
    public function show(ProductCategory $category)
    {
        return $this->view('stores.categories.show')->with('item', $category);
    }

    /**
     * Show the form for editing the specified product_category.
     *
     * @param ProductCategory $category
     * @return Response
     */
    public function edit(ProductCategory $category)
    {
        $parents = ProductCategory::getAllList();

        return $this->view('stores.categories.create_edit')
            ->with('item', $category)
            ->with('parents', $parents);
    }

    /**
     * Update the specified product_category in storage.
     *
     * @param ItemCategory $category
     * @return Response
     */
    public function update(ProductCategory $category)
    {

        //dd(request()->file('photo'));

        $id=$category->id;

        if (request()->file('photo') === null) {
            
            $validate['name'] 		=   ['required','string','min:3','max:191',Rule::unique('product_categories')->ignore($id)->whereNull('deleted_at')];
            
            $attributes = request()->validate($validate,
                ProductCategory::$messages);
        }
        else {

            $validate['name'] 		=      ['required','string','min:3','max:191',Rule::unique('product_categories')->ignore($id)->whereNull('deleted_at')];
            $validate['slug']       =       ['nullable'];
            $validate['url']        =          ['nullable'];
            $validate['parent_id']  =    ['nullable'];
            $validate['photo']      =    ['required','max:6000','mimes:jpg,jpeg,png,bmp'];

            $attributes             =   request()->validate($validate, ProductCategory::$messages);


            $photo = $this->uploadImage($attributes['photo']);
            if ($photo) {
                $attributes['image'] = $photo;
            }
        }

        unset($attributes['photo']);

        $category = $this->updateEntry($category, $attributes);
        $category->updateUrl()->save();

        return redirect_to_resource();
    }

    /**
     * Remove the specified product_category from storage.
     *
     * @param ItemCategory $category
     * @return Response
     */
    public function destroy(ProductCategory $category)
    {
        $this->deleteEntry($category, request());

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
