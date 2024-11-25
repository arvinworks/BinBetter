<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout=horizontal>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page }} | {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/back/images/favicon/favicon.ico') }}" />

    <!-- Color modes -->
    <script src="{{ asset('assets/back/js/vendors/color-modes.js') }}"></script>

    <!-- Libs CSS -->
    <link href="{{ asset('assets/back/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/back/libs/%40mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/back/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('assets/back/css/dataTables.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/responsive.dataTables.css') }}">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/back/css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/css/my-modified.css') }}">

    <style>
        .subscription-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 193, 7, 0.7);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            border-radius: 0.5rem;
            z-index: 1;
        }

        .btn.disabled,
        .btn:disabled {
            -webkit-filter: grayscale(100%);
            filter: grayscale(100%);
            opacity: 0.25;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>

<body>

    <div id="loading-container" class="d-none">
        <div id="loading-message">
            <img src="{{ asset('assets/back/images/loading/loading.gif') }}">
            <span id="loading-text mt-3" data-loading-text="Loading..."></span>
        </div>
    </div>

    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="google-thumb_up" viewBox="0 -960 960 960">
            <path d="M716-120H272v-512l278-288 39 31q6 5 9 14t3 22v10l-45 211h299q24 0 42 18t18 42v81.839q0 7.161 1.5 14.661T915-461L789-171q-8.878 21.25-29.595 36.125Q738.689-120 716-120Zm-384-60h397l126-299v-93H482l53-249-203 214v427Zm0-427v427-427Zm-60-25v60H139v392h133v60H79v-512h193Z"></path>
        </symbol>
        <symbol id="google-thumb_up-fill" viewBox="0 -960 960 960">
            <path d="M721-120H254v-512l278-288 33 26q11 8 14.5 18t3.5 23v10l-45 211h322q23 0 41.5 18.5T920-572v82q0 11-2.5 25.5T910-439L794-171q-9 21-29.5 36T721-120ZM194-632v512H80v-512h114Z"></path>
        </symbol>
        <symbol id="google-thumb_down" viewBox="0 -960 960 960">
            <path d="M242-840h444v512L408-40l-39-31q-6-5-9-14t-3-22v-10l45-211H103q-24 0-42-18t-18-42v-81.839Q43-477 41.5-484.5T43-499l126-290q8.878-21.25 29.595-36.125Q219.311-840 242-840Zm384 60H229L103-481v93h373l-53 249 203-214v-427Zm0 427v-427 427Zm60 25v-60h133v-392H686v-60h193v512H686Z"></path>
        </symbol>
        <symbol id="google-thumb_down-fill" viewBox="0 -960 960 960">
            <path d="M239-840h467v512L428-40l-33-26q-11-8-14.5-18t-3.5-23v-10l45-211H100q-23 0-41.5-18.5T40-388v-82q0-11 2.5-25.5T50-521l116-268q9-21 29.5-36t43.5-15Zm527 512v-512h114v512H766Z"></path>
        </symbol>
    </svg>

    <main id="main-wrapper" class="@auth main-wrapper @else container d-flex flex-column @endauth">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm d-none">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif

                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @auth
        @if(Auth::user()->email_verified_at)
        @include('layouts.back.header')
        @include('layouts.back.navbar')
        @endif
        @endauth

        <div id="app-content">
            @yield('content')
        </div>
    </main>

    <script src="{{ asset('assets/back/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/back/js/swal2.js') }}"></script>
    <script src="{{ asset('assets/back/js/global.js') }}"></script>
    <script src="{{ asset('assets/back/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/back/libs/feather-icons/dist/feather.min.js') }}"></script>
    <script src="{{ asset('assets/back/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://www.youtube.com/iframe_api"></script>

    <!-- DataTables JS -->
    <script src="{{ asset('assets/back/js/dataTables.js') }}"></script>
    <script src="{{ asset('assets/back/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('assets/back/js/responsive.dataTables.js') }}"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/back/js/theme.min.js') }}"></script>

    <script src="{{ asset('assets/back/libs/jsvectormap/dist/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/back/libs/jsvectormap/dist/maps/world.js') }}"></script>
    <script src="{{ asset('assets/back/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/back/js/vendors/chart.js') }}"></script>

    <script src="{{ asset('assets/back/js/tinymce/tinymce.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/dayjs/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs/plugin/relativeTime.js"></script>

    <script>
        // Enable relativeTime plugin
        dayjs.extend(dayjs_plugin_relativeTime);
    </script>

    <script>
        $(document).ready(function() {
            if (typeof tinymce !== 'undefined') {
                // Initialize TinyMCE for general textareas
                tinymce.init({
                    selector: 'textarea#description, textarea#re_description',
                    plugins: 'lists advlist', // Add advlist for advanced list options
                    toolbar: 'undo redo | formatselect | bold italic | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify',
                    menubar: false
                });

                tinymce.init({
                    selector: 'textarea#subscription_description',
                    plugins: 'lists',
                    toolbar: 'bullist',
                    menubar: false
                });
            } else {
                console.error('TinyMCE is not loaded');
            }
        });
    </script>


    @php
    $userProfile = Auth::check() ? Auth::user()->profile : '';
    $userUsername = Auth::check() ? Auth::user()->username : '';
    @endphp

    <script>
        const userProfile = '<?php echo $userProfile; ?>' || '<?php echo asset('assets/back/images/avatar/noprofile.webp ') ?>';
        const userUsername = '<?php echo $userUsername; ?>';

        $('#auth_user_username').text(userUsername);
        $('#auth_user_profile').attr('src', userProfile);
    </script>


    @stack('scripts')

</body>

</html>