<header id="header" class="fullwidth-menu header2">
    <div id="header-top">
        <div class="container clearfix">
            <div class="left-side">
                <ul class="header-links">
                    
                    @if (Route::has('login'))
                    @auth
                        {{-- Opciones según el rol --}}
                            <li>
                                <a href="{{ route('dashboard.home') }}">
                                        <i class="far fa-table" style="color: #46B2B4"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                    
                            {{-- Opciones según el rol --}}
                            @can('admin-access')
                            <li>
                                <a href="{{ route('admin.home') }}">
                                    <i class="fas fa-tasks" style="color: #46B2B4"></i>
                                    <span>Administración</span>
                                </a>
                            </li>
                            @endcan                            


                        {{-- Logout --}}
                        <li>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('form-id').submit();">
                                <span class="header-links-icon icon-account"></span>
                                <span>Logout</span>
                            </a>
                        </li>
                
                        <form id="form-id" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        {{-- Login y registro --}}
                        <li>
                            <a href="{{ route('login') }}">
                                <span class="header-links-icon icon-account"></span>
                                <span>Login</span>
                            </a>
                        </li>
                        @if (Route::has('register'))
                            <li>
                                <a href="{{ route('register') }}">
                                    <span class="header-links-icon icon-account"></span>
                                    <span>Registrarse</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                @endif
                                          
                    

                </ul>
            </div>
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
            <a title="Language" class="dropdown-toggle" data-toggle="dropdown"><span class="long-name">Español</span><span class="short-name">ES</span><span class="dropdown-arrow"></span></a>

            <ul class="dropdown-menu">
                <li><a href="#"><span class="long-name">Español</span><span class="short-name">ES</span> <img src="{{ asset('images/flags/spain.jpg') }}" alt="Spanish"></a></li>                
                <li><a href="#"><span class="long-name">English</span><span class="short-name">Eng</span><img src="{{ asset('images/flags/england.jpg') }}" alt="English"></a></li>
                
                <li><a href="#"><span class="long-name">French</span><span class="short-name">Fre</span> <img src="{{ asset('images/flags/france.jpg') }}" alt="French"></a></li>
                <li><a href="#"><span class="long-name">German</span><span class="short-name">Ger</span> <img src="{{ asset('images/flags/germany.jpg') }}" alt="German"></a></li>
                <li><a href="#"><span class="long-name">Italian</span><span class="short-name">Ita</span> <img src="{{ asset('images/flags/italy.jpg') }}" alt="Italian"></a></li>
            </ul>
        </div><!-- End .language-dropdown -->
    </div>
  </div>
</div><!-- End #header-top -->

<livewire:public.izquierdo></livewire:public.izquierdo>


</header><!-- End #header -->