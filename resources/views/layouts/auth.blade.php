<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Log In</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="light-style" />
        <link href="assets/css/app-dark.min.css" rel="stylesheet" type="text/css" id="dark-style" />

        {{-- Additional Css Files --}}

        @yield('css')

    </head>

    <body class="authentication-bg pb-0" data-layout-config='{"darkMode":false}'>

        {{-- All Auth Pages content goes here --}}

        @yield('content')

        <!-- bundle -->
        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/js/app.min.js"></script>

        {{-- Additional Js files --}}

        @yield('js')

    </body>

</html>