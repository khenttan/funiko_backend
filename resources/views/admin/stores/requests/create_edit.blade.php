@extends('admin.admin')

@section('content')
    <form id="stores" class="card card-primary" method="POST" action="{{$selectedNavigation->url . (isset($item)? "/{$item->id}" : '')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        <input name="_token" type="hidden" value="{{ csrf_token() }}">
        <input name="_method" type="hidden" value="{{isset($item)? 'PUT':'POST'}}">

        <div class="card-header">
            <h3 class="card-title">
                <span><i class="fa fa-edit"></i></span>
                <span>Edit the store!!</span>
            </h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            @include('admin.partials.card.info')

            <fieldset>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Store Name</label>
                            <input type="text" class="form-control {{ form_error_class('name', $errors) }}" id="name" name="shop_name" placeholder="Enter Name" value="{{ ($errors && $errors->any()? old('name') : (isset($item)? $item->shop_name : '')) }}">
                            {!! form_error_message('name', $errors) !!}
                        </div>
                    </div>
                </div>
                    

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Store Bio</label>
                            <input type="text" class="form-control {{ form_error_class('description', $errors) }}" id="description" name="shop_description" placeholder="Enter Description" value="{{ ($errors && $errors->any()? old('description') : (isset($item)? $item->shop_description : '')) }}">
                            {!! form_error_message('description', $errors) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="address">Store Address</label>
                            <input type="text" class="form-control {{ form_error_class('address', $errors) }}" id="address" name="shop_address" placeholder="Enter address" value="{{ ($errors && $errors->any()? old('address') : (isset($item)? $item->shop_address : '')) }}">
                            {!! form_error_message('address', $errors) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="mobile">Contact No.</label>
                            <input type="text" class="form-control {{ form_error_class('mobile', $errors) }}" id="mobile" name="shop_mobile" placeholder="Enter Mobile" value="{{ ($errors && $errors->any()? old('mobile') : (isset($item)? $item->shop_mobile : '')) }}">
                            {!! form_error_message('mobile', $errors) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Store Image (1920 x 600)</label>
                    <div class="input-group">
                        <input id="photo-label" type="text" class="form-control {{ form_error_class('photo', $errors) }}" readonly placeholder="Browse for an image">
                        <span class="input-group-append">
                            <button type="button" class="btn btn-default" onclick="document.getElementById('photo').click();">Browse</button>
                        </span>
                        <input id="photo" style="display: none" accept="{{ get_file_extensions('image') }}" type="file" name="shop_photo" onchange="document.getElementById('photo-label').value = this.value">
                        {!! form_error_message('photo', $errors) !!}
                    </div>
                </div>

                @if(isset($item) && $item && $item->shop_photo)
                    <section>
                        <img src="{{ uploaded_images_url($item->shop_photo) }}" style="max-width: 100%; max-height: 300px;">
                        <input type="hidden" name="shop_photo" value="{{ $item->shop_photo }}">
                    </section>
                @endif
            </fieldset>

        </div>
        @include('admin.partials.form.form_footer')
    </form>

@endsection
