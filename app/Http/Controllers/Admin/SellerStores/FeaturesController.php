<?php

namespace App\Http\Controllers\Admin\SellerStores;

use Redirect;
use App\Http\Requests;
use App\Models\StoreFeature;

use App\Models\SubFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;

use Illuminate\Validation\Rule;

class FeaturesController extends AdminController
{
	/**
	 * Display a listing of product_features.
	 *
	 * @return Response
	 */
	public function index()
	{
		save_resource_url();
      
		return $this->view('stores.features.index')->with('items', StoreFeature::all());
	}

	/**
	 * Show the form for creating a new product_features.
	 *
	 * @return FeaturesController
	 */
	public function create()
	{
		return $this->view('stores.features.create_edit');
	}

	/**
	 * Show the form for creating a new product_features.
	 *
	 * @return FeaturesController
	 */
	public function subcreate($id)
	{
		return $this->view('stores.subfeatures.create')->with('id',$id);
	}


	/**
	 * Store a newly created product_sizes in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$attributes = request()->validate(StoreFeature::$rules, StoreFeature::$messages);
    
        $feature = $this->createEntry(StoreFeature::class, $attributes);

        return redirect_to_resource();
	}

	/**
	 * Store a newly created product_sizes in storage.
	 *
	 * @return Response
	 */
	public function subsave($id)
	{
		$attributes = request()->validate(SubFeature::$rules, SubFeature::$messages);
		$attributes['type_id']=$id;
		
        $feature = $this->createEntry(SubFeature::class, $attributes);

        return redirect_to_resource();
	}

	/**
     * Display the specified banner.
     *
     * @param Banner $banner
     * @return Factory|View
     */
    public function show($id)
    {
		save_resource_url();
		return $this->view('stores.subfeatures.index')->with('items', SubFeature::where('type_id',$id)->get());
    }


	/**
	 * Show the form for editing the specified product_features.
	 *
	 * @param ProductFeature $feature
     * @return Response
     */
    public function edit($id)
	{
        $feature=StoreFeature::find($id);
		return $this->view('stores.features.create_edit')->with('item', $feature);
	}
	/**
	 * Show the form for editing the specified product_features.
	 *
	 * @param ProductFeature $feature
     * @return Response
     */
    public function subedit($id)
	{
        $feature=SubFeature::find($id);
		return $this->view('stores.subfeatures.edit')->with('item', $feature);
	}

	/**
	 * Update the specified product_sizes in storage.
	 *
	 * @param ProductFeature $feature
     * @return Response
     */
    public function update($id)
	{
        $feature=StoreFeature::find($id);

		$validate['name'] 		= ['required','string','min:3','max:191',Rule::unique('attribute_type')->ignore($id)];

		$attributes = request()->validate($validate, StoreFeature::$messages);
		
        $feature = $this->updateEntry($feature, $attributes);

        return redirect_to_resource();
	}
	/**
	 * Update the specified product_sizes in storage.
	 *
	 * @param ProductFeature $feature
     * @return Response
     */
    public function subupdate($id)
	{
        $feature=SubFeature::find($id);

		$validate['name'] 		= ['required','string','min:3','max:191',Rule::unique('attributes')->ignore($id)];

		$attributes = request()->validate($validate, SubFeature::$messages);
		
        $feature = $this->updateEntry($feature, $attributes);

        return redirect_to_resource();
	}

	/**
	 * Remove the specified product_sizes from storage.
	 *
	 * @param ProductFeature $feature
	 * @return Response
	 */
	public function destroy($id)
	{
        $feature=StoreFeature::find($id);

        $this->deleteEntry($feature, request());

        return redirect_to_resource();
	}
	/**
	 * Remove the specified product_sizes from storage.
	 *
	 * @param ProductFeature $feature
	 * @return Response
	 */
	public function subdestroy($id)
	{
        $feature=SubFeature::find($id);

        $this->deleteEntry($feature, request());

        return redirect_to_resource();
	}
}
