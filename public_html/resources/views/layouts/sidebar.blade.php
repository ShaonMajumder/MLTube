<nav class="sidebar">
    <ul class="nav flex-column">
        @foreach(menu()->getMenus() as $menuKey => $menu)

            @php
                $childrens = $menu['childrens'];
                $menuRouteName = $menu['route'] ?? '';
                $menuRouteParameters = $menu['route_parameters'] ?? [];
                $isDropdownVisible = has_any_active_child_route($childrens) ? 'show' : '';
                $activeMenuClass = (has_any_active_child_route($childrens) or is_current_route_name($menuRouteName)) ? 'active' : '';
                $hasChildren = !empty($childrens);
                $isDropdownClass = $hasChildren ? 'dropdown' : '';
                $dropdownToggleClass = $hasChildren ? 'dropdown-toggle' : '';
                $collapseAttributes = $hasChildren ? 'data-toggle=collapse aria-expanded=false' : '';
                $menuRoute = !empty($menuRouteName) ? route($menuRouteName, $menuRouteParameters ?? []) : '';
                $submenuId = "{$menuKey}Submenu";
                $isActive = isActiveRoute($menuRouteName);
                $isActiveClass = $isActive ? 'active' : '';
                $middleware = $menu['middleware'] ?? [];
                $canAccess = canAccessRouteWithMiddleware($menuRouteName, $middleware, $menuRouteParameters);
            @endphp

            @if (!$canAccess && $middleware != [])
                @continue
            @endif

            @permission($menu['permissions'])
                <li class="nav-item {{ $isDropdownClass }}">
                    <a class="nav-link {{ "$dropdownToggleClass $activeMenuClass" }}" 
                        href="{{ $hasChildren ? "#$submenuId" : $menuRoute }}" 
                        {{ $collapseAttributes }}
                    >
                        @if(isset($menu['icon']))
                            <i class="{{ $menu['icon'] }}"></i>
                        @endif
                        <span>{{ $menu['label'] }}</span>
                    </a>
                    @if ($hasChildren)
                        <ul class="collapse list-unstyled {{ $isDropdownVisible }}" id="{{ $submenuId }}">
                            @foreach($childrens as $childKey => $child)

                                @php
                                    $childRoute =  !empty($child['route']) ? route($child['route']) : '';
                                    $isChildRouteActive = is_current_route_name($child['route']) ? 'active' : '';
                                    $childMiddleware = $child['middleware'] ?? [];
                                @endphp

                                @if (in_array('auth', $childMiddleware) && !Auth::check())
                                    @continue
                                @endif
                                
                                @permission($child['permissions'])
                                    <li class="nav-item">
                                        <a class="nav-link {{ $isChildRouteActive }}" 
                                            href="{{ $childRoute }}"
                                        >
                                            <i class="{{ $child['icon'] }}"></i>
                                            <span>{{ $child['label'] }}</span>
                                        </a>
                                    </li>
                                @endpermission
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endpermission
            
        @endforeach
    </ul>
</nav>