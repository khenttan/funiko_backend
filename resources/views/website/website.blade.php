<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="{{ config('app.author') }}">
        <meta name="keywords" content="{{ config('app.keywords') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $description ?? config('app.description') }}"/>

        <meta property="og:type" name="og:type" content="website"/>
        <meta property="og:site_name" content="{{ config('app.name') }}"/>
        <meta property="og:url" name="og:url" content="{{ request()->url() }}"/>
        <meta property="og:caption" name="og:caption" content="{{ config('app.url') }}"/>
        <meta property="fb:app_id" name="fb:app_id" content="{{ config('app.facebook_id') }}"/>
        <meta property="og:title" name="og:title" content="{{ $title ?? config('app.title') }}">
        <meta property="og:description" name="og:description" content="{{ $description ?? config('app.description') }}">
        <meta property="og:image" name="og:image" content="{{ config('app.url') }}{{ $image ?? '/images/logo.png' }}">

        <link rel="shortcut icon" type="image/ico" href="/favicon.ico">

        <title>{{ $title ?? config('app.name') }}</title>
        {{-- <link rel="stylesheet" href="/css/website.css?v={{ config('app.assets_version') }}"> --}}
        <link href="/front/css/bootstrap.min.css" rel="stylesheet" >
        <link rel="stylesheet" href="/css/toastr.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="/front/css/owl.carousel.min.css">
        <link rel="stylesheet" href="/front/css/all.css">
        <link rel="stylesheet" href="/front/fonts/remixicon.css">
        <link rel="stylesheet" href="/front/css/animation.css">
        <link rel="stylesheet" href="/front/css/style.css?v={{ config('app.assets_version') }}">
        <link rel="stylesheet" href="/front/css/root-css.css">
        <link rel="stylesheet" href="/front/css/responsive.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/front/css/swiper-bundle.min.css">
        {{-- @yield('styles') --}}
        <!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.css">
         -->
        @toastr_css
    </head>

    <body>

        @if(config('app.env') != 'local')
            @include('partials.facebook')
        @endif

        @include('website.partials.header')

        {{-- @include('website.partials.banners') --}}

        <main>
            @yield('content')
        </main>

        @include('website.partials.footer')

  
        {{-- @yield('scripts') --}}

        @if(config('app.env') != 'local')
            @include('partials.analytics')
        @endif
        

        @jquery

        @toastr_js

        @toastr_render

        @include('website.partials.form.captcha')
        <script src="/front/js/bootstrap.bundle.min.js"></script>
        
        <script src="/front/js/owl.carousel.min.js"></script>
        
        
        <script type="text/javascript" src="/front/js/swiper-bundle.min.js"></script>
        
        
        <script>
        window.onscroll = function() {
            myFunction()
        };
        var header = document.getElementById("fixed-header");
        var sticky = header.offsetTop;
        
        function myFunction() {
            if(window.pageYOffset > sticky) {
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }
        }
        </script>
        
        
        <script type="text/javascript">
           $('.owl-carousel.slider-owl').owlCarousel({
                loop:false,
                 margin:30,
                 nav:false,
                 dot:true,
                 //animateOut: 'fadeOut',
                 //animateOut: 'slideOutleft',
                //animateIn: 'flipInX',
                 responsive:{
                     0:{
                         items:2
                     },
                     576:{
                         items:3
                     },
                     768:{
                         items:3
                     },
                     1000:{
                         items:3
                     }
                 }
             });
        </script>
        
        
        <script type="text/javascript">
        
        
        var slider = new Swiper ('.gallery-slider', {
            slidesPerView: 1,
            centeredSlides: true,
            loop: true,
           
            loopedSlides: 6, //スライドの枚数と同じ値を指定
            // navigation: {
            //     nextEl: '.swiper-button-next',
            //     prevEl: '.swiper-button-prev',
            // },
            autoplay: 
            {
              delay: 2000,
            }
        
        });
        
        //サムネイルスライド
        var thumbs = new Swiper ('.gallery-thumbs', {
            slidesPerView: '5',
            spaceBetween: 10,
            centeredSlides: true,
            loop: true,
            slideToClickedSlide: true,
               // Responsive breakpoints
          breakpoints: {
            // when window width is >= 320px
            320: {
              slidesPerView: 2,
              spaceBetween: 5
            },
            // when window width is >= 480px
            481: {
              slidesPerView: 2,
              spaceBetween: 10
            },
            // when window width is >= 640px
            991: {
              slidesPerView: 4,
              spaceBetween: 10
            },
             1200: {
              slidesPerView: 5,
              spaceBetween: 10
            }
          }
        });
        
        //3系
        //slider.params.control = thumbs;
        //thumbs.params.control = slider;
        
        //4系～
        slider.controller.control = thumbs;
        thumbs.controller.control = slider;

        // $(document).ready(function() {
        // $(".jumper").on("click", function( e ) {

        //     e.preventDefault();

        //     $("body, html").animate({ 
        //     scrollTop: $( $(this).attr('href') ).offset().top 
        //     }, 600);

        // });
        // });


        </script>
    
    
        </body>
        </html>
