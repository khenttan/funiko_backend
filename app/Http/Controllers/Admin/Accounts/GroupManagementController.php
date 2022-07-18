<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupMember;
use App\Http\Controllers\Admin\AdminController;

class GroupManagementController extends AdminController
{
      /**
     * Display a listing of client.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        save_resource_url();

        $items = Group::with('admin:id,username')->withCount('GroupMember')->orderBy('created_at', 'desc')->get()->toArray();

        return $this->view('accounts.groups.index')->with('items', $items);
    }


      /**
     * Display a listing of client.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showMembers($id)
    {
        $items = Group::with('admin:id,username','GroupMember.member:id,username,image')->where('id',$id)->orderBy('created_at', 'desc')->first()->toArray();
        return $this->view('accounts.groups.members')->with('items', $items);
    }

    

          /**
     * Change the status for editing the specified resource.
     *
     * @param  int  $id
     * @param  int  $status
     * @return \Illuminate\Http\Response
     */
    public function status($id,$status)
    {
        Group::find($id)->update(['is_active' => $status]);
        return redirect()->back();
    }



}
