
<header class="topHeader" id="fixed-header">
    <div class="container">
    <div>
       <nav class="navbar navbar-expand-lg ">
          <div class="navbar-inner-box">
             <div class="nav-L">
                <a class="navbar-brand desktop-view-logo" href="/"  title="{{ config('app.name') }}">
                    <img src="images/logo.png">
                </a>
                <a class="navbar-brand mob-logo-view " style="display:none" href="#"><img src="images/footer-logo.png"></a>
             </div>
             <div class="nav-R">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="ri-menu-3-line"></i>
                </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                   <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    {{-- @if(isset($navigation))
                    @include('website.partials.navigation.top_level', ['collection' => $navigation['root'], 'navigation' => $navigation])
                     @endif --}}

                     <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Home</a>
                     </li>
                      <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/about">About</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link active jumper" aria-current="page" href="{{Config::get('app.url')}}#Features">Features</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link active jumper" aria-current="page" href="{{Config::get('app.url')}}#Testimonials" >Testimonials</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link active jumper" aria-current="page" href="{{Config::get('app.url')}}#Screenshots">Screenshots</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/contact-us">contact us</a>
                     </li>
                      <li class="mob-view-btn" style="display:none">
                         <div class="header-btn-group">
                           <div class="button-container-2">  <span class="mas">Download the app</span> <a href="#" class="download-app-btn button-effects">Download app</a></div>
                      </div>
                      </li>
                   </ul>
                </div>
                <div class="header-btn-group desktop-view-btn">
                  <div class="button-container-2"> <span class="mas">Download the app</span><a href="#" class="download-app-btn button-effects">Download the app</a></div>
                </div>
             </div>
          </div>
       </nav>
    </div>
 </header>