<footer>
    <div class="footer-menu">
     
       <div class="container">
          <div class="row">
             <div class="col-md-4 footer-content">
                <div class="footer-top-left">
                   <figure><img src="images/footer-logo.png"></figure>
                   <p>{{ $settings->description }}</p>
                 
                </div>
             </div>
                <div class="col-md-2 footer-content ps-4">
                <h3>Explore</h3>
                <ul>
                 {{-- @if(isset($footerNavigation['root']))
                    @foreach($footerNavigation['root'] as $nav)
                           <li><a href="{{ $nav->url }}">{{ $nav->title }}</a></li>
                    @endforeach --}}
         
                  <li><a href="/">Home</a></li>
                  <li><a href="/about">About Us</a></li>
                  <li><a href="{{Config::get('app.url')}}#Testimonials">Testimonials</a></li>
                </ul>
             </div>
             <div class="col-md-3 footer-content ps-4">
                <h3>Help</h3>
                <ul>
                   <li><a href="/contact-us">Contact Us</a></li>
                   <li><a href="/terms-and-conditions">Terms and conditions</a></li>
                   <li><a href="/privacy-policy">Privacy Policy</a></li>
                   
                </ul>
             </div>
             <div class="col-md-3 footer-content">
                <h3>Follow Us</h3>
                <ul class="footer-social">
                     @if(isset($settings->facebook))
                      <li class="facebook-bg">
                         <a href="{{ $settings->facebook }}"><img src="images/face-book-icon.png"></a>
                      </li>
                     @endif
                    @if(isset($settings->twitter))
                    <a target="_blank" title="Twitter" href="{{ $settings->twitter }}"><i class="fab fa-twitter"></i> 
                    <li class="pinterest-bg">
                         <a href="{{ $settings->twitter }}"><img src="images/twitter.png"></a>
                    </li>
                    </a>@endif
                    @if(isset($settings->instagram))
                      <li class="pinterest-bg">
                         <a href="{{ $settings->instagram }}"><img src="images/instagram.png"></a>
                      </li>
                    @endif
                   </ul>
             </div>
          </div>
       </div>
    </div>
    <div class="bottom-footer">
       <div class="container">
          <p>Copyright© FUNIKO 2022. All rights reserved</p>
       </div>
    </div>
 </footer>