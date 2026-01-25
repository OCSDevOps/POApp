<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>CRM Dashboard</title>
        @yield('metatags')
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
        <meta content="Coderthemes" name="author">
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="light-style">
        <link href="{{asset('assets/css/app-dark.min.css')}}" rel="stylesheet" type="text/css" id="dark-style">

        @yield('css')

    </head>

    <body class="loading" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
        <!-- Begin page -->
        <div class="wrapper">

            {{-- Dashboard left nav component loaded --}}

            <x-DashboardLeftNav/>

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">   

                    {{-- Dashboard left nav component loaded --}}
            
                    <x-DashboardTopNav/>

                    {{-- content of dashboard pages goes here --}}
                    
                    @yield('content')

                </div> <!-- content -->

                {{-- Dashboard footer goes here --}}

                <x-DashboardFooter/>

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->

        {{-- Dashboard right bar goes here --}}

        <x-DashboardRightBar/>

        <!-- bundle -->
        <script src="{{asset('assets/js/vendor.min.js')}}"></script>
        <script src="{{asset('assets/js/app.min.js')}}"></script>

        {{-- addition js files if any required from content pages --}}
        @yield('js')
    </body>
</html>
