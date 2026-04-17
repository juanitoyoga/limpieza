<nav class="flex flex-col gap-1">

    {{-- Información General --}}
    @if($menuItems->isEmpty())
        <div class="px-2 py-1 rounded bg-red-100 text-red-700 text-center text-sm font-medium">
            Usuario sin funciones
        </div>
    @else
        @php
            $primerRegistroId = $menuItems->first()->menu_item_id ?? null;
        @endphp

        @foreach($menuItems as $item)
            @if($item->parent_menu_id == $primerRegistroId)
                <!-- Menú principal -->
                <div x-data="{ open: false }" class="relative">
                    <button @mouseenter="open = true" @mouseleave="open = false"
                            class="flex items-center gap-2 w-full px-2 py-1 rounded transition text-sm text-gray-700 hover:bg-gray-100">
                        <i class="{{ $item->menu_icon }} text-xs"></i>

                        <span class="whitespace-normal break-words">{{ $item->menu_label }}</span>

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-auto text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    @php
                        $subItems = $menuItems->where('parent_menu_id', $item->menu_item_id);
                    @endphp

                    @if($subItems->isNotEmpty())
                        <!-- Submenú flyout -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:leave="transition ease-in duration-150"
                             @mouseenter="open = true" @mouseleave="open = false"
                             class="absolute left-full top-0 mt-0 w-64 bg-white shadow-md rounded border border-gray-200 z-50">
                        
                            
                            @foreach($subItems as $child)
                                <a href="{{ route($child->menu_url) }}"
                                    class="block px-4 py-1 text-sm whitespace-normal break-words text-gray-700 hover:bg-gray-100 transition">
                                    {{ $child->menu_label }}
                                </a>

                                @php
                                    $subSubItems = $menuItems->where('parent_menu_id', $child->menu_item_id);
                                @endphp

                                @if($subSubItems->isNotEmpty())
                                    <div class="ml-3">
                                        @foreach($subSubItems as $subChild)
                                        <a href="{{ route($subChild->menu_url) }}"
                                            class="block px-4 py-1 text-xs whitespace-normal break-words text-gray-600 hover:bg-gray-50 transition">
                                             {{ $subChild->menu_label }}
                                         </a>
                                        @endforeach
                                    </div>
                                @endif

                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    @endif
</nav>
