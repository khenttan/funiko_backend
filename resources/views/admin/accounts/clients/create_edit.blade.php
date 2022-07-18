@extends('admin.admin')

@section('content')

    <div class="card  card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <span><i class="fa fa-edit"></i></span>
                <span>{{ isset($item)? 'Edit the ' . $item->title . ' entry': 'Create a new Client' }}</span>
            </h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <form id="admincreate" method="POST" action="{{$selectedNavigation->url . (isset($item)? "/{$item->id}" : '')}}" accept-charset="UTF-8">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">
            <input name="_method" type="hidden" value="{{isset($item)? 'PUT':'POST'}}">

            <div class="card-body">
                @include('admin.partials.card.info')

                <fieldset>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fullname">Fullname</label>
                                <input type="text" class="form-control {{ form_error_class('fullname', $errors) }}" id="fullname" name="fullname" placeholder="Enter full name" value="{{ ($errors && $errors->any()? old('fullname') : (isset($item)? $item->fullname : '')) }}">
                                {!! form_error_message('fullname', $errors) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control {{ form_error_class('username', $errors) }}" id="username" name="username" placeholder="Enter user name" value="{{ ($errors && $errors->any()? old('username') : (isset($item)? $item->username : '')) }}">
                                {!! form_error_message('username', $errors) !!}
                            </div>
                        </div>
                    </div>
                    <input type="hidden"  id="dial_code" name ="dial_code" value="1" >
                    <input type="hidden"  id="country_code" name ="country_code" value="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cellphone">Cellphone</label>
                                <div class="input-group">
                                    <input   id="phone" type="text" name="cellphone"  class="form-control {{ form_error_class('cellphone', $errors) }}" id="cellphone" name="cellphone" placeholder="Enter Cellphone" value="{{ ($errors && $errors->any()? old('cellphone') : (isset($item)? $item->cellphone : '')) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-mobile-alt"></i></span>
                                    </div>
                                    
                                    {!! form_error_message('cellphone', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-group">
                                    <input type="text" class="form-control {{ form_error_class('email', $errors) }}" id="email" name="email" placeholder="Enter Email" value="{{ ($errors && $errors->any()? old('email') : (isset($item)? $item->email : '')) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    {!! form_error_message('email', $errors) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                 @if(!isset($item)) 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control {{ form_error_class('password', $errors) }}" name="password" placeholder="Password" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    {!! form_error_message('password', $errors) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control {{ form_error_class('password_confirmation', $errors) }}" name="password_confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}" autocomplete="new-password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    {!! form_error_message('password_confirmation', $errors) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                            	<label for="profile">Profile</label>
                                <div class="input-group" >
                                    <label><input class="radio" {{ ( isset($item) && $item->user_type==config('globalConstant.buyer'))? "checked" : "" }} type="radio" name="user_type" value="{{config('globalConstant.buyer')}}">Buyer</label>
                                    <label style="margin-left: 30px;"><input class="radio" {{ ( isset($item) && $item->user_type==config('globalConstant.seller'))? "checked" : "" }} type="radio" name="user_type" value="{{config('globalConstant.seller')}}">Seller</label>
                                </div>
                                {!! form_error_message('user_type', $errors) !!}
                            </div>
                        </div>
                    </div>
                    {{-- 
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                            	<label for="roles">Roles</label>
                            	{!! form_select('roles[]', $roles, ($errors && $errors->any()? old('roles') : (isset($item)? $item->roles->pluck('id')->all() : '')), ['class' => 'select2 form-control ' . form_error_class('roles', $errors), 'multiple']) !!}
                            	{!! form_error_message('roles', $errors) !!}
                            </div>
                        </div>
                    </div>
                    --}}
                </fieldset>
            </div>
            @include('admin.partials.form.form_footer')
        </form>
    </div>
    

@endsection
@section('scripts')
    @parent
    <script src="{{asset('developer/admin/js/jquery.min.js')}}"></script>
    <!--For initialization-->
    
    <script>
    
    const phoneInputField = document.querySelector("#phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
        initialCountry: "{{isset($item->country_code)?$item->country_code:'auto'}}",
       
        //initialDialCode: "{{isset($item->dial_code)?$item->dial_code:'auto'}}",
        separateDialCode: true,
        autoHideDialCode: true,
        formatOnDisplay: false ,
        geoIpLookup: function (success, failure) {
        $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
          var countryCode = (resp && resp.country) ? resp.country : "us";
          success(countryCode);
        });
      },

        utilsScript: "{{asset('developer/admin/js/utils.js')}}",
        
    });
    </script>
    <!--Init Ends Here -->
    
    <script>
    //Fetching Values on change of country code and dial code
    $(document).ready(function() {              
        $('.iti__flag-container').click(function() { 
            var countryData = phoneInput.getSelectedCountryData();
            $('#country_code').val(countryData.iso2);
            $('#dial_code').val(countryData.dialCode);
        });  
    });
    //Fetching Initial Values of country code and dial code
    setTimeout(()=>{
        var countryData = phoneInput.getSelectedCountryData();
        $('#country_code').val(countryData.iso2);
        $('#dial_code').val(countryData.dialCode);
    },1000)
    </script>
@endsection