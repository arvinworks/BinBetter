<!DOCTYPE html>
<html lang="en">

<head>
    <title>BinBetter</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicons/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicons/favicon-16x16.png" />
    <link rel="manifest" href="assets/images/favicons/site.webmanifest" />
    <meta name="description" content="Wostin HTML Template For Business" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">

    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&amp;display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&amp;display=swap" rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('assets/front/vendors/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/animate/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/animate/custom-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/jarallax/jarallax.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/jquery-magnific-popup/jquery.magnific-popup.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/nouislider/nouislider.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/nouislider/nouislider.pips.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/odometer/odometer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/swiper/swiper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/wostin-icons/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/tiny-slider/tiny-slider.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/reey-font/stylesheet.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/owl-carousel/owl.theme.default.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/bxslider/jquery.bxslider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/bootstrap-select/css/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/vegas/vegas.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/jquery-ui/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/vendors/timepicker/timePicker.css') }}" />

    <!-- template styles -->
    <link rel="stylesheet" href="{{ asset('assets/front/css/wostin.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/front/css/wostin-responsive.css') }}" />
</head>

<body>
 
    <div class="preloader">
        <img class="preloader__image" width="60" src="{{ asset('assets/front/images/loader.png') }}" alt="" />
    </div>
    <!-- /.preloader -->

    <div class="page-wrapper">

    @include('layouts.front.header')

    @include('layouts.front.main-slider')

    @yield('content')

    @include('layouts.front.footer')

    </div>

    @include('layouts.front.mobilenav-wrapper')

    @include('layouts.front.search-popup')

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fa fa-angle-up"></i></a>

    <script src="{{ asset('assets/front/vendors/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jarallax/jarallax.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jquery-appear/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jquery-circle-progress/jquery.circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/odometer/odometer.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/swiper/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/tiny-slider/tiny-slider.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/wnumb/wNumb.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/wow/wow.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/isotope/isotope.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/countdown/countdown.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/bxslider/jquery.bxslider.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/vegas/vegas.min.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/jquery-ui/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/front/vendors/timepicker/timePicker.js') }}"></script>

    <!-- template js -->
    <script src="{{ asset('assets/front/js/wostin.js') }}"></script>


    @stack('scripts')

</body>

</html>