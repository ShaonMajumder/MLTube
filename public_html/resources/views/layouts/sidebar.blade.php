<nav class="sidebar">
    <ul class="nav flex-column">
        @foreach(menu()->getMenus() as $menuKey => $menu)

            @php
                $submenuId = "{$menuKey}Submenu";
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
                $isActive = isActiveRoute($menuRouteName);
                $isActiveClass = $isActive ? 'active' : '';
                $middleware = $menu['middleware'] ?? [];
                $permissions = $menu['permissions'] ?? null;

                if(empty($menuRouteName)){
                    if (in_array('auth', $middleware) && !auth()->check()) {
                        continue;
                    }
                } else if ( !empty($middleware) && !empty($menuRouteName) ) {
                    $canAccess = canAccessRouteWithMiddleware($menuRouteName, $middleware, $menuRouteParameters);
                    if((!$canAccess)){
                        continue;
                    }
                } else if(  !empty($permissions) && !app('laratrust')->isAbleTo($permissions) ){
                    continue;
                }
                if( !empty($permissions) && !app('laratrust')->isAbleTo($permissions) ){
                    continue;
                }
            @endphp

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
                                $childRoute = $child['route'];
                                $isChildRouteActive = is_current_route_name($childRoute) ? 'active' : '';
                                $childRouteParameters = $child['route_parameters'];
                                $childRoute =  !empty($childRoute) ? route($childRoute, $childRouteParameters ?? []) : '';
                                $childMiddleware = $child['middleware'] ?? [];
                            @endphp

                            @if (in_array('auth', $childMiddleware) && !Auth::check())
                                @continue
                            @endif
                            
                            @permission($child['permissions'])
                                <li class="nav-item pl-2">
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
            
        @endforeach
    </ul>
</nav>