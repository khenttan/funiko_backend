<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;

class UserProfileController extends BaseController
{
       /**
    * Function for api data
    *
    * @param null
    *
    * @return json reponse.
    */
    public function stepOne(Request $request) { 
    	return print_r($request->all());
    }

}
