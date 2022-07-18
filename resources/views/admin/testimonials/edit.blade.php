@extends('admin.admin')

@section('content')
    <div class="pcoded-inner-content">

        <div class="main-body">

            <div class="page-wrapper">
                <div class="form-group row">
                    <div class="col-md-12" style="margin-right: 873px;">
                        {{-- <a class="btn waves-effect waves-light btn-grd-primary" href="{{route('banner.management.list')}}">
                            <i class="fa fa-backward"></i>Back
                        </a> --}}
                    </div>
                </div>
                <!-- Page body start -->
                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-block">
                                    <form id="banner-management-edit-form" method="post" action="{{route('testimonials.edit', ['id'=>base64_encode($edit->id), 'page'=>request('page')])}}" novalidate enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Name </label>
                                            <div class="col-sm-10">
                                                <input type="name"  name="name"placeholder="Enter name"  value="{{ $edit->name }}" class="form-control @error('text') is-invalid @enderror" id="text" rows="10" cols="80">
                                                @error('content')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Role </label>
                                            <div class="col-sm-10">
                                                <input type="name" name="role"placeholder="Enter role"   value="{{ $edit->role }}" class="form-control @error('text') is-invalid @enderror" id="text" rows="10" cols="80">
                                                @error('content')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Image</label>
                                            <div class="col-sm-10">
                                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror">
                                                @error('image')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label"></label>
                                            <div class="col-sm-10">
                                                <img src="{{asset('public/storage/uploads/testimonials/'.$edit->image ?? '')}}" style="width: 50px; height: 50px">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Banner Text </label>
                                            <div class="col-sm-10">
                                                <textarea name="text" placeholder="Enter Text"    class="form-control @error('text') is-invalid @enderror" id="text" rows="10" cols="80"> {{ $edit->text }}</textarea>
                                                @error('content')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <button class="btn waves-effect waves-light btn-grd-primary">Submit
                                                    <i class="fa fa-refresh fa-spin" style="display: none" id="spinner"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection