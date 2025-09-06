<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', config('app.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Pritish" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/select2-Dropdown/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/preloader.min.css') }}" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/timeline.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/pace-js/themes/purple/minimal.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/Uploader/dist/image-uploader.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/FileDrag/fileUpload/fileUpload.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/dist/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/intl-tel-input-master/build/css/intlTelInput.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/aks-file-upload/dist/aksFileUpload.min.css') }}">







    <style>
        /* Default theme to prevent flickering */
        body[data-bs-theme='dark'] {
            background-color: #333;
            color: #fff;
            transition: background-color 0s ease-in-out, color 0s ease-in-out;
        }

        body[data-bs-theme='light'] {
            background-color: #fff;
            color: #000;
            transition: background-color 0s, color 0s;
        }

        /* Hide content until theme is applied */
        body {
            visibility: hidden;
        }

        .oneLine {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            max-width: 100px;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    @yield('style')
</head>

<body data-bs-theme="dark" data-topbar="dark" data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('Admin.layout.navbar')

        <!-- ========== Left Sidebar Start ========== -->
        @include('Admin.layout.sidebar')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content" id="miniaresult">
            @yield('content')
        </div>

        @include('Admin.layout.footer')

        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    @include('Admin.layout.rightsidebar')
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->

    <script src="{{ asset('assets/Menu-Editor/dist/menu-editor.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/FileDrag/fileUpload/fileUpload.js') }}"></script>
    <script src="{{ asset('assets/flatpickr/dist/flatpickr.min.js') }}"></script>


    <!-- pace js -->

    <script src="{{ asset('assets/libs/pace-js/pace.min.js') }}"></script>


    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/JqueryValidation/dist/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/Uploader/dist/image-uploader.min.js') }}"></script>
    <script src="{{ asset('assets/select2-Dropdown/dist/js/select2.min.js')}}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-autofill/js/dataTables.autoFill.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-autofill-bs4/js/autoFill.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/Text-Editor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/intl-tel-input-master/build/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('assets/js/progress.js') }}"></script>
    <script src="{{ asset('assets/aks-file-upload/dist/aksFileUpload.min.js') }}"></script>

    <!-- Inline JavaScript for immediate theme application -->
    <script>
        (function() {
            // Function to set the theme
            function setTheme(theme) {
                document.body.setAttribute('data-bs-theme', theme);
                document.body.setAttribute('data-topbar', theme);
                document.body.setAttribute('data-sidebar', theme);
                localStorage.setItem('theme', theme);
                document.body.style.visibility = 'visible'; // Show body after theme is applied
            }

            // Check localStorage for theme preference
            var savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
        })();
    </script>

    <script>
        Pace.options = {
            elements: true,
            ajax: true,
            document: true,
            eventLag: true
        };
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Add event listener for button click
            $('#mode-setting-btn').click(function() {
                var currentTheme = $('body').attr('data-bs-theme');
                var newTheme = currentTheme === 'light' ? 'dark' : 'light';
                setTheme(newTheme);
            });

            // Function to set the theme
            function setTheme(theme) {
                $('body').attr('data-bs-theme', theme);
                $('body').attr('data-topbar', theme);
                $('body').attr('data-sidebar', theme);
                localStorage.setItem('theme', theme);
            }
        });
    </script>

    @yield('script')
    @stack('scripts')
</body>

</html>