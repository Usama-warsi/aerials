    <footer class="footer_dark">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12">
                        <div class="widget">
                            @if (theme_option('logo_footer') || theme_option('logo'))
                                <div class="footer_logo">
                                    <a href="{{ route('public.single') }}">
                                        <img  class="lazy" width="238" height="78" src="{{url('storage/blank.webp')}}" data-src="{{ RvMedia::getImageUrl(theme_option('logo_footer') ? theme_option('logo_footer') : theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" />
                                    </a>
                                </div>
                            @endif
                            <p>{!! theme_option('about-us') !!}</p>
                        </div>
                        @if (theme_option('social_links'))
                            <div class="widget">
                                <ul class="social_icons social_white">
                                    @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                                        @if (count($socialLink) == 4)
                                            <li>
                                                <a href="{{ $socialLink[2]['value'] }}"
                                                   title="{{ $socialLink[0]['value'] }}" style="background-color: {{ $socialLink[3]['value'] }}; border: 1px solid {{ $socialLink[3]['value'] }};" target="_blank">
                                                    <i class="{{ $socialLink[1]['value'] }}"></i>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    {!! dynamic_sidebar('footer_sidebar') !!}
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="widget">
                            <h6 class="widget_title">{{ __('Contact Info') }}</h6>
                            <ul class="contact_info contact_info_light">
                                @if (theme_option('address'))
                                @php 
                                $string = theme_option('address');
                                    $clean_string = strip_tags($string);

                                @endphp
                                
                                    <li>
                                        <i class="ti-location-pin"></i>
                                        <p><a href="https://www.google.com/maps/place/{!! $clean_string !!}" target="_blank">{!! theme_option('address') !!}</a></p>
                                    </li>
                                @endif
                                @if (theme_option('email'))
                                    <li>
                                        <i class="ti-email"></i>
                                        <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                                    </li>
                                @endif
                                @if (theme_option('hotline'))
                                    <li>
                                        <i class="ti-mobile"></i>
                                        <p><a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a></p>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom_footer border-top-tran">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-md-0 text-center text-md-left">{{ theme_option('copyright') }}</p>
                    </div>
                    <div class="col-md-6">
                        <ul class="footer_payment text-center text-lg-right">
                            @foreach(json_decode(theme_option('payment_methods', []), true) as $method)
                                @if (!empty($method))
                                    <li><img class="lazy" width="49" height="32" src="{{url('storage/blank.webp')}}" data-src="{{ RvMedia::getImageUrl($method) }}" alt="payment method" loading="lazy" /></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

     @if (is_plugin_active('ecommerce') && EcommerceHelper::isCartEnabled())
         <div id="remove-item-modal" class="modal" tabindex="-1" role="dialog">
             <div class="modal-dialog modal-dialog-centered" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ __('Warning') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
                     <div class="modal-body">
                         <p>{{ __('Are you sure you want to remove this product from cart?') }}</p>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-fill-out" data-dismiss="modal">{{ __('Cancel') }}</button>
                         <button type="button" class="btn btn-fill-line confirm-remove-item-cart">{{ __('Yes, remove it!') }}</button>
                     </div>
                 </div>
             </div>
         </div>
     @endif

    <a href="#" class="scrollup" style="display: none;"><i class="ion-ios-arrow-up"></i></a>
   
    <script src="https://arialessentials.com/themes/shopwise/js/magnific-popup.min.js" defer></script>
  {!! Theme::footer() !!}

    <script>
        window.trans = {
            "No reviews!": "{{ __('No reviews!') }}",
            "Days": "{{ __('Days') }}",
            "Hours": "{{ __('Hours') }}",
            "Minutes": "{{ __('Minutes') }}",
            "Seconds": "{{ __('Seconds') }}",
        };

        window.siteUrl = "{{ route('public.index') }}";
    </script>

 <script>
    var isloading = true;

    function removeLoading() {
        if (isloading) {
            var head = document.head;
            var body = document.body;

           
            var scripts = [
                "{{url('themes/shopwise/plugins/slick/slick.min.js')}}",
                "{{url('themes/shopwise/js/popper.min.js')}}",
                "{{url('themes/shopwise/plugins/bootstrap/js/bootstrap.min.js')}}",
           
                "{{url('themes/shopwise/js/waypoints.min.js?v=4.0.1')}}",
                "{{url('themes/shopwise/plugins/owlcarousel/js/owl.carousel.min.js')}}",
                "{{url('themes/shopwise/js/jquery.elevatezoom.js')}}",
                "{{url('themes/shopwise/js/jquery.countdown.min.js')}}",
                "{{url('themes/shopwise/js/scripts.js?v=1.39.1')}}",
                "{{url('themes/shopwise/js/backend.js?v=1.39.1')}}"
            ];

        

            scripts.forEach(function(src) {
                var script = document.createElement('script');
                script.src = src;
                script.defer = true;
                body.appendChild(script);
            });

           
            isloading = false;
        }
    }

    document.addEventListener('mousemove', removeLoading);
    document.addEventListener('scroll', removeLoading);
    document.addEventListener('touchmove', removeLoading);
    setInterval(removeLoading, 10000);
</script>


    @if (session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
        <script type="text/javascript">
            window.onload = function () {
                @if (session()->has('success_msg'))
                    window.showAlert('alert-success', '{{ session('success_msg') }}');
                @endif

                @if (session()->has('error_msg'))
                    window.showAlert('alert-danger', '{{ session('error_msg') }}');
                @endif

                @if (isset($error_msg))
                    window.showAlert('alert-danger', '{{ $error_msg }}');
                @endif

                @if (isset($errors))
                    @foreach ($errors->all() as $error)
                        window.showAlert('alert-danger', '{!! $error !!}');
                    @endforeach
                @endif
            };
        </script>
    @endif
<script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.4/dist/lazyload.min.js"></script>
<script>
   setTimeout(function() {
    var lazyLoadInstance = new LazyLoad({
      elements_selector: ".lazy",
      threshold: 0,
    });
  }, 1000);
</script>
    </body>
</html>
