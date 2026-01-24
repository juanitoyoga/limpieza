<div class="container" data-clone="sticky">
    <div class="row">
        <div class="col-md-4">
            <ul class="menu left-menu clearfix">
                <li><a href="#">Ordenanza 322</a>
                    <ul>
                        <li><a href="#">Documento</a></li>
                        <li><a href="#">Infracciones</a></li>
                        <li><a href="#">Legal</a></li>
                        <li><a href="#">Multas</a></li>
                        <li><a href="#">Obras Barriales</a></li>
                        <li><a href="#">Consulta Ingresos/Egresos</a></li>
                        <li><a href="#">Noticias</a></li>
                        <li><a href="#">Modificaciones</a></li>
                        <li><a href="#">Otros</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Operaciones</a>
                    
                    @if(empty($menuItems))
                        <ul>
                            <li>Usuario sin funciones</li>
                        </ul>
                    @else
                        @php
                            $rootItems = $menuItems->where('parent_menu_id', $menuItems->first()->menu_item_id ?? null);
                        @endphp
                        
                        @if($rootItems->isNotEmpty())
                            <ul>
                                @foreach($rootItems as $item)
                                    @php
                                        $subItems = $menuItems->where('parent_menu_id', $item->menu_item_id);
                                    @endphp
                                    
                                    <li>
                                        <!-- Item principal -->
                                        <a href="{{ $item->menu_url ? route($item->menu_url) : '#' }}"
                                           class="block px-4 py-2 text-gray-800 hover:bg-blue-100 hover:text-blue-900 transition">
                                            {{ $item->menu_label }}
                                        </a>
                                        
                                        <!-- Subitems nivel 1 -->
                                        @if($subItems->isNotEmpty())
                                            <ul>
                                                @foreach($subItems as $child)
                                                    @php
                                                        $subSubItems = $menuItems->where('parent_menu_id', $child->menu_item_id);
                                                    @endphp
                                                    
                                                    <li>
                                                        <a href="{{ $child->menu_url ? route($child->menu_url) : '#' }}"
                                                           class="block px-4 py-2 text-gray-800 hover:bg-blue-100 hover:text-blue-900 transition">
                                                            {{ $child->menu_label }}
                                                        </a>
                                                        
                                                        <!-- Subitems nivel 2 -->
                                                        @if($subSubItems->isNotEmpty())
                                                            <ul>
                                                                @foreach($subSubItems as $subChild)
                                                                    <li>
                                                                        <a href="{{ $subChild->menu_url ? route($subChild->menu_url) : '#' }}"
                                                                           class="block px-4 py-1 text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition">
                                                                            {{ $subChild->menu_label }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <ul>
                                <li>No hay funciones disponibles</li>
                            </ul>
                        @endif
                    @endif
                </li>
            </ul>
        </div>
        <div class="col-md-4 logo-container">
            <h1 class="logo clearfix">
                <a href="#" title="¡Sé un héroe de la limpieza!">
                    <span style="color: #008000; font-weight: bold;">Limpia</span> 
                    <span style="color: #32CD32; font-weight: bold;">Tu</span> 
                    <span style="color: #4682B4; font-weight: bold;">Rincon</span>
                </a>
            </h1>
        </div><!-- End .md-md-4-->

        <div class="col-md-4 clearfix">
            <nav>
                <div id="responsive-nav">
                    <a id="responsive-btn" href="#">
                        <span class="responsive-btn-icon">
                            <span class="responsive-btn-block"></span>
                            <span class="responsive-btn-block"></span>
                            <span class="responsive-btn-block last"></span>
                        </span>
                        <span class="responsive-btn-text visible-sm-inline-block visible-xs-inline-block">Menu</span>
                    </a><!-- responsive-nav-button -->
                    <div id="responsive-menu-container">
                        
                    </div><!-- End #responsive-menu-container -->
                </div><!-- End .responsive-nav -->        
                   
                    <ul class="menu right-menu clearfix">
                        <li class="megamenu-container"><a href="#">Beneficios</a>
                            <div class="megamenu">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <a href="#" class="megamenu-title">Como Funciona</a>
                                            <ul>
                                                <li><a href="#">El Sistema</a></li>
                                                <li><a href="#">Aplicacion Movil</a></li>
                                                <li><a href="#">Dashboard</a></li>
                                                <li><a href="#">Distribucion Multas</a></li>
                                                <li><a href="#">Obras Barriales</a></li>
                                                <li><a href="#">Otros</a></li>
                                                
                                            </ul>
                                        </div><!-- End .col-md-2 -->
                                        <div class="col-md-2">
                                            <a href="#" class="megamenu-title">Participacion</a>
                                            <ul>
                                                <li><a href="#">Ciudadana</a></li>
                                                <li><a href="#">Barrial</a></li>
                                                <li><a href="#.html">DMQ</a></li>
                                                
                                            </ul>
                                        </div><!-- End .col-md-2 -->
                                        <div class="col-md-2">
                                            <a href="#" class="megamenu-title">Varios</a>
                                            <ul>
                                                <li><a href="#">Varios 1</a></li>
                                                <li><a href="#">Tabs</a></li>
                                                <li><a href="#">Collapses</a></li>
                                                <li><a href="#">Form Elements</a></li>
                                                <li><a href="#">Buttons</a></li>
                                                <li><a href="#">Grid System</a></li>
                                            </ul>
                                        </div><!-- End .col-md-2 -->


                                        <div class="col-md-4 menu-banner">
                                            <a href="#" class="banner text-left">
                                                <img src="{{ asset('images/limpieza2.jpg') }}" alt="Banner">
                                            <div class="banner-container text-center text-uppercase text-info">
                                                <h5>¡Limpito está aquí para ayudarte! </h5>
                                                <h4>LimpiaTuRincón</h4>
                                                <h5><span>¡Sé un héroe de la limpieza!</span></h5>
                                                </div><!-- End .banner-container -->
                                            </a><!-- End .banner -->
                                        </div><!-- End .col-md-4 -->
                                    </div><!-- End .row -->
                                </div><!-- End .container -->
                            </div><!-- End.megamenu -->
                        </li>

                        <li class="reverse"><a href="#">Contactos</a>
                            <ul>
                                <li><a href="#">Limpiaturincon</a></li>
                                <li><a href="#">DMQ</a></li>
                            </ul>
                        </li>                            
                    </ul>
            </nav>
        </div>
    </div>
</div>
