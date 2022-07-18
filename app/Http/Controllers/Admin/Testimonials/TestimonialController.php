<?php

namespace App\Http\Controllers\Admin\Testimonials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Http\Controllers\Admin\AdminController;

class TestimonialController extends AdminController
{
    public function list(){

         $list = Testimonial::orderBy('created_at', 'desc')->paginate(config('custom-paginate.paginate.number'));
        // $list = FaqManagement::orderBy('created_at', 'desc')->paginate(config('custom-paginate.paginate.number'));
        return $this->view('testimonials.list', compact('list'));
    }

    public function add(Request $request){
    
        if ($request->isMethod('post')){
            $request->validate([
               'name' => 'required|string|max:100',
               'role' => 'required|string|max:100',
               'text' => 'required|string|max:250',
               'image' => 'mimes:jpg,jpeg,png|max:5000',
               ]);
           $testimonial = new Testimonial();
           if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file) {
                $destinationPath = public_path('/storage/uploads/testimonials/');
                $extension = $request->file('image')->getClientOriginalExtension();
                $filename =  time(). '.' . $extension;
                $file->move($destinationPath, $filename);
                $testimonial->image = $filename;
            }
            }
           $testimonial->name = $request['name'];
           $testimonial->role = $request['role'];
           $testimonial->text = $request['text'];

           $testimonial->save();
            toastr()->success('Testimonial successfully added!');
            return redirect()->route('testimonials.list');
        }

        return $this->view('testimonials.add');
    }
    public function edit(Request $request,$id,$page=null){
        $edit = Testimonial::findOrFail(base64_decode($id));
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:100',
                'role' => 'required|string|max:100',
                'text' => 'required|string|max:250',
                'image' => 'mimes:jpg,jpeg,png|max:5000',
            ]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file) {
                    $destinationPath = 'public/storage/uploads/testimonials/';
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $filename =  time(). '.' . $extension;
                    $file->move($destinationPath, $filename);
                    $edit->image = $filename;
                }
            }
            $edit->name = $request['name'];
            $edit->role =  $request['role'];
            $edit->text = $request['text'];
            $edit->save();
            toastr()->success('Testimonials successfully updated!');
            return redirect()->route('testimonials.list');
        }
        return $this->view('testimonials.edit', compact('edit'));
    }


    public function delete(Request $request, $id){
        $delete = Testimonial::findOrFail(base64_decode($id));
        if ($delete){
            $delete->delete();
            toastr()->success('Testimonial Ad successfully deleted!');
            return redirect()->back();
        }
        toastr()->error('Testimonial not deleted!');
        return redirect()->back();
    }
}
