<?php

namespace App\Http\Controllers\Website\Account;

use App\Models\Traits\Documentable;
use App\Models\Traits\Photoable;
use App\Models\Traits\Videoable;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Website\WebsiteController;
use Illuminate\Validation\Rule;

use App\Models\Traits\Videoable;
use Bpocallaghan\Sluggable\HasSlug;
use Bpocallaghan\Sluggable\SlugOptions;



class ProfileController extends WebsiteController
{
    use SoftDeletes, HasSlug, Photoable, Documentable, Videoable;

    public function index()
    {
        $user = user();

        return $this->view('account.profile', compact('user'));
    }

    /**
     * Update the user's profile info
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $attributes = request()->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|email|' . Rule::unique('users')->ignore(user()->id),
            'password'  => 'nullable|min:4|confirmed',
            'cellphone'  => 'nullable',
        ]);

        // update user
        user()->update([
            'firstname' => $attributes['firstname'],
            'lastname'  => $attributes['lastname'],
            'email'     => $attributes['email'],
            'cellphone' => $attributes['cellphone'],
        ]);

        // only update when a new password was entered
        $message = '';
        if ($attributes['password'] && strlen($attributes['password']) >= 2) {
            $message = " and <strong>Password</strong> ";
            user()->update([
                'password' => bcrypt($attributes['password']),
            ]);
        }

        // alert()->success('Updated!',
        //     "Your personal information {$message} was successfully updated.");
        Toastr()->success('Your personal information was successfully updated.', 'success');
                   
        return redirect()->back();
    }
}