<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="author" content="{!! config('app.author') !!}">
        <meta name="keywords" content="{!! config('app.keywords') !!}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $HTMLDescription ?? config('app.description') }}"/>
        <title>{{ $HTMLTitle ?? config('app.name') }}</title>
        <link rel="stylesheet" href="/css/admin.css?v={{ config('app.assets_version') }}">
        @toastr_css        
        @yield('styles')
    </head>
    <body class="hold-transition login-page">

        @yield('content')

        <script src="/js/admin.js?v={{ config('app.assets_version') }}"></script>

        @yield('scripts')

        <script type="text/javascript" charset="utf-8">
            $(document).ready(function () {
                new ButtonClass();
            });
        </script>
        
        @jquery
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>   
        <script src="{{asset('developer/admin/js/jqueryvalidation.js')}}"></script>
    
        @toastr_js
        @toastr_render 
        
    </body>
</html>
