<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <title>LimpiaTuRincon </title>
        <meta name="description" content="LimpiaTuRincon">

        <!--[if IE]> <meta http-equiv="X-UA-Compatible" content="IE=edge"> <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/revslider2.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

        
        <!-- Favicon and Apple Icons -->
        <link rel="icon" type="image/png" href="{{ asset('images/icons/icon.png') }}">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/icons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/icons/apple-icon-72x72.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


        <!--- jQuery -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/jquery-2.1.1.min.js"><\/script>')</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @livewireStyles
    </head>    
    <body>
    <div id="wrapper">
        <div id="sticky-header" class="fullwidth-menu header2" data-fixed="fixed"></div><!-- End #sticky-header -->

        @include('partials._header');

        <section id="content" role="main">
            
                {{ $slot }}

        </section>
    
            @include('partials._footer');
    </div><!-- End #wrapper -->

    <!-- scroltop -->
    <a href="#header" id="scroll-top" title="Go to top">Top</a>

    <!-- END -->

    <!-- Google map javascript api v3 -->
    <script src="//maps.googleapis.com/maps/api/js?sensor=false"></script>

    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/smoothscroll.js')}}"></script>
    <script src="{{ asset('js/smoothscroll.js')}}"></script>
    <script src="{{ asset('js/waypoints.js')}}"></script>
    <script src="{{ asset('js/waypoints-sticky.js')}}"></script>
    <script src="{{ asset('js/jquery.debouncedresize.js')}}"></script>
    <script src="{{ asset('js/retina.min.js')}}"></script>
    <script src="{{ asset('js/jquery.placeholder.js')}}"></script>
    <script src="{{ asset('js/jquery.hoverIntent.min.js')}}"></script>
    <script src="{{ asset('js/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('js/twitter/jquery.tweet.min.js')}}"></script>
    <script src="{{ asset('js/jquery.themepunch.tools.min.js')}}"></script>
    <script src="{{ asset('js/jquery.themepunch.revolution.min.js')}}"></script>
    <script src="{{ asset('js/jquery.stellar.min.js')}}"></script>
    <script src="{{ asset('js/maplabel.js')}}"></script>
    <script src="{{ asset('js/main.js')}}"></script>
    
@livewireScripts

    </body>    
</html>
