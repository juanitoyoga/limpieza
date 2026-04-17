        <header id="header" class="fullwidth-menu header2">
            <div id="header-top">
                <div class="container clearfix">
                    <div class="left-side">
                        <ul class="header-links">
                            <li><a href="#"><span class="header-links-icon icon-account"></span><span>Login</span></a></li>
                            <li><a href="#"><span class="header-links-icon icon-checkout"></span><span>Registrarse</span></a></li>

                        </ul>

                        <div class="user-dropdown dropdown visible-sm visible-xs">
                            <a title="My Account" class="dropdown-toggle" data-toggle="dropdown"><span class="header-links-icon icon-account"></span><span class="user-text">Usuarios</span><span class="dropdown-arrow"></span></a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#"><span class="header-links-icon icon-account"></span><span>Login</span></a></li>
                                <li><a href="#"><span class="header-links-icon icon-checkout"></span><span>Registrarse</span></a></li>

                            </ul>
                        </div><!-- End .user-dropdown -->
                    </div><!-- End .left-side -->
                    <div class="right-side">
                        <div class="search-container">
                            <form action="#" class="search-form">
                                <input type="search" name="s" class="s" placeholder="Search entry site here...">
                                <a href="#" title="Close Search" class="search-close-btn"></a>
                                <input type="submit" value="Submit" class="search-submit-btn">  
                            </form>
                        </div><!-- End .search-container -->

                        <a href="#" class="header-search-btn" title="Search"><span class="hidden-sm hidden-xs">Search</span></a>


                        
                        <div class="currency-dropdown dropdown">
                            <a title="Currenct" class="dropdown-toggle" data-toggle="dropdown"><span class="long-name">US Dollar</span><span class="short-name">USD</span><span class="dropdown-arrow"></span></a>

                            <ul class="dropdown-menu">
                                <li><a href="#"><span class="long-name">US Dollar</span><span class="short-name">USD</span></a></li>
                                <li><a href="#"><span class="long-name">Euro</span><span class="short-name">EUR</span></a></li>
                                <li><a href="#"><span class="long-name">Pound St.</span><span class="short-name">GBT</span></a></li>
                            </ul>
                        </div><!-- End .currency-dropdown -->

                        <div class="language-dropdown dropdown">
                            <a title="Language" class="dropdown-toggle" data-toggle="dropdown"><span class="long-name">English</span><span class="short-name">Eng</span><span class="dropdown-arrow"></span></a>

                            <ul class="dropdown-menu">
                                <li><a href="#"><span class="long-name">English</span><span class="short-name">Eng</span><img src="{{ asset('images/flags/england.jpg') }}" alt="English"></a></li>
                                <li><a href="#"><span class="long-name">Spanish</span><span class="short-name">Spa</span> <img src="{{ asset('images/flags/spain.jpg') }}" alt="Spanish"></a></li>
                                <li><a href="#"><span class="long-name">French</span><span class="short-name">Fre</span> <img src="{{ asset('images/flags/france.jpg') }}" alt="French"></a></li>
                                <li><a href="#"><span class="long-name">German</span><span class="short-name">Ger</span> <img src="{{ asset('images/flags/germany.jpg') }}" alt="German"></a></li>
                                <li><a href="#"><span class="long-name">Italian</span><span class="short-name">Ita</span> <img src="{{ asset('images/flags/italy.jpg') }}" alt="Italian"></a></li>
                            </ul>
                        </div><!-- End .language-dropdown -->
                    </div><!-- End .right-side -->
                </div>
            </div><!-- End #header-top -->
            <div class="container" data-clone="sticky">
                <div class="row">
                    @include('partials._izquierdo');

                    <div class="col-md-4 logo-container">
                        <h1 class="logo clearfix">
                            <a href="" title="¡Sé un héroe de la limpieza!">  <img src="{{ asset('images/limpiaturincon_letra26.jpg') }}" alt="LimpiaTuRincon"></a>
                        </h1>
                    </div><!-- End .md-md-4-->

                    @include('partials._derecho');

                </div><!-- End .row -->
            </div><!-- End .container -->
        </header><!-- End #header -->