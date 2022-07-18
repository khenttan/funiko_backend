@extends('website.website')

@section('content')
    {{-- <section class="container body contact">

        <div class="row mb-5">
            <div class="col-12">
                <h2 class="page-header text-center">{!! isset($pageTitle) ? $pageTitle : $page->name !!}</h2>
            </div>
        </div>

        @include('website.pages.page_components', ['item' => $page])

        <div class="row pb-5">
            <div class="order-2 order-md-1 col-12 col-md-7 col-lg-6">
                <form id="form-contact-us" accept-charset="UTF-8" action="/contact/submit" method="POST" class="needs-validation" novalidate>
                    {!! csrf_field() !!}

                    <div class="form-group form-row">
                        <div class="col">
                            <label class="sr-only">First name</label>
                            <input type="text" class="form-control form-control-lg validate" name="firstname" id="firstname" placeholder="First name" required>
                        </div>
                        <div class="col">
                            <label class="sr-only">Last name</label>
                            <input type="text" class="form-control form-control-lg validate" name="lastname" id="lastname" placeholder="Last name" required>
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col">
                            <label class="sr-only">Email Address</label>
                            <input type="email" class="form-control form-control-lg validate" name="email" id="email" placeholder="Email Address" required>
                        </div>
                        <div class="col">
                            <label class="sr-only">Telephone Number</label>
                            <input type="text" class="form-control form-control-lg validate" name="phone" id="phone" placeholder="Telephone Number">
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col">
                            <label class="sr-only">Your Message</label>
                            <textarea class="form-control form-control-lg validate" rows="3" name="content" id="content" placeholder="Any additional comments" required></textarea>
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col">
                            <button type="submit" id="g-recaptcha-contact" class="btn btn-block btn-lg btn-outline-primary g-recaptcha" data-widget-id="0"><span>Submit</span></button>
                        </div>
                    </div>

                    @include('website.partials.form.feedback')
                </form>
            </div>

            <div class="order-1 order-md-2 col-12 col-md-5 col-lg-6 contact-details">
                <div class="border mb-3 p-3">
                    <div class="row">
                        <div class="col-1 text-primary" data-icon="fa fa-fw fa-phone pr-3"></div>
                        <div class="col"><strong>Phone </strong><br>
                            @if(isset($settings->telephone))  <a href="tel:{{ trim($settings->telephone) }}">{{ $settings->telephone }}</a> <br>@endif
                            @if(isset($settings->cellphone)) <a href="tel:{{ trim($settings->cellphone) }}">{{ $settings->cellphone }}</a>@endif
                        </div>
                    </div>
                </div>


                @if(isset($settings->email))
                <div class="border mb-3 p-3">
                    <div class="row">
                        <div class="col-1 text-primary" data-icon="fa fa-fw fa-envelope pr-3"></div>
                        <div class="col">
                            <strong>Email </strong><br>
                            <a href="mailto:{{ trim($settings->email) }}">{{ $settings->email }}</a>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($settings->address))
                <div class="border mb-3 p-3">
                    <div class="row">
                        <div class="col-1 text-primary" data-icon="fa fa-fw fa-map-marked-alt pr-3"></div>
                        <div class="col">
                            <strong>Physical Address</strong>
                            <br>{{ $settings->address }}
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($settings->po_box))
                <div class="border mb-3 p-3">
                    <div class="row">
                        <div class="col-1 text-primary" data-icon="fa fa-fw fa-print pr-3"></div>
                        <div class="col">
                            <strong>Postal Address</strong>
                            <br> {{ $settings->po_box }}
                        </div>
                    </div>
                </div>
            @endif


            </div>
        </div>
    </section>
    <section class="location-map">
        <h3 class="d-none">Location Map</h3>

        <div id="js-map-contact-us" class="google-maps" style="height: 400px"></div>
    </section>

@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_map_key') }}"></script>
    <script type="text/javascript" charset="utf-8">
        $(function () {
            var map = initGoogleMapView('js-map-contact-us', '{{ $settings->latitude }}', '{{ $settings->longitude }}', {{ $settings->zoom_level }});
            addGoogleMapMarker(map, '{{ $settings->latitude }}', '{{ $settings->longitude }}', false);

            var content = '<h4>{{ $settings->name }}</h4>' + $('.contact-details').html();
            addGoogleMapMarkerClick(map, '{{ $settings->name }}', '{{ $settings->latitude }}', '{{ $settings->longitude }}', content);
        });
    </script> --}}



    <div class="wraper-inner">
        <div class="main-banner">
           <div class="inner-banner inner-space">
              <figure><img src="images/contact-bannner-bg.jpg"></figure>
              <div class="container">
                 <div class="banner-inner-content ">
                    <h4>Contact Us</h4>
                 </div>
              </div>
           </div>
     
           <div class="contact-us-box inner-spce-bottom">
              <div class="container">
     
     
                 <div class="row box-shadow-comm m-0">
                    <div class="col-7">
                       <div class="common-heading  mb-4 pb-1">
                          <small class="small-title">Contact</small>
                          
                          <h3 class="inner-title">Share your Feedback </h3>
                       </div>
                       <form id="form-contact-us" accept-charset="UTF-8" action="/contact-us/submit" method="POST" class="needs-validation" novalidate>
                            {!! csrf_field() !!}
                          <div class="mb-3">
                             <input type="text" class="form-control form-control-lg {{ form_error_class('firstname', $errors) }}" name="firstname" id="firstname" placeholder="Name" required>
                             {!! form_error_message('firstname', $errors) !!}
                            </div>
                          <div class="common-group">
                          <div class="mb-3">
                            <input type="email" class="form-control form-control-lg  {{ form_error_class('email', $errors) }}" name="email" id="email" placeholder="Email Address" required>
                            {!! form_error_message('email', $errors) !!}

                        </div>
                          <div class="mb-3">
                            <input type="text" class="form-control form-control-lg {{ form_error_class('phone', $errors) }}" name="phone" id="phone" placeholder="Telephone Number">
                            {!! form_error_message('phone', $errors) !!}

                        </div>
                         </div>
                          <div class="mb-3">
                             <textarea class="form-control form-control-lg {{ form_error_class('content', $errors) }}" rows="3" name="content" id="content" placeholder="Any additional comments" required></textarea>
                             {!! form_error_message('content', $errors) !!}

                            </div>
     
                         <div class="button-container-2   mt-4">  <span class="mas">Submit</span> <button type="submit" class="common-btn button-effects">Submit</button></div>
                         
                       </form>
                    </div>
                    <div class="col-5">
                       <div class="contact-us-R">
                        <h3 class="inner-title">Contact Us </h3>
                       <ul class="contact-info">
                          <li>
                             <a href="mailto:{{ trim($settings->email) }}"> <i class="ri-mail-unread-fill"></i> <span>{{ $settings->email }}</span></a>
                          </li>
                          <li>
                             <a><i class="ri-phone-fill"></i> <span>{{ $settings->telephone }}</span></a>
                          </li>
                          <li>
                             <a><i class="ri-map-pin-2-fill"></i> <span>{{ $settings->address }}</span></a>
                          </li>
                       </ul>
                       </div>
                    </div>
                 </div>
     
     
           <div class="map mt-5">
                 <div class="map-box">
                 <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d26360817.240440972!2d-113.7464011090823!3d36.24257412185098!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sin!4v1650282944920!5m2!1sen!2sin" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
             </div>
           </div>
              </div>
           </div>
     
        </div>
     </div>


@endsection
