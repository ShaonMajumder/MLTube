{{-- <nav class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="{{ url('/dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/profile') }}">
                <i class="fas fa-user"></i> <span>Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" href="#settingsSubmenu" data-toggle="collapse" aria-expanded="false">
                <i class="fas fa-cog"></i> <span>Role Permissions</span>
            </a>
            <ul class="collapse list-unstyled" id="settingsSubmenu">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/settings/general') }}">
                        <i class="fas fa-cogs"></i> <span>Permissions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/settings/security') }}">
                        <i class="fas fa-shield-alt"></i> <span>Roles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/settings/notifications') }}">
                        <i class="fas fa-bell"></i> <span>Role Assignment</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav> --}}


{{-- <nav class="sidebar">
    <ul class="nav flex-column">
        <li class="nav-item ">
            <a href="http://localhost:8000" class="nav-link ">
                <i class="fas fa-tachometer-alt"></i> 
                <span>Dashboard</span>
            </a>
        </li> 
        <li class="nav-item ">
            <a href="http://localhost:8000" class="nav-link ">
                <i class="fas fa-user"></i> 
                <span>Profile</span>
            </a>
        </li> 
        <li class="nav-item ">
            <a href="#role_permissionsSubmenu" data-toggle="collapse" aria-expanded="false" class="nav-link dropdown-toggle">
                <i class="fas fa-cog"></i> 
                <span>Role Permissions</span>
            </a> 
            <ul id="role_permissionsSubmenu" class="collapse list-unstyled">
                <li class="nav-item">
                    <a href="http://localhost:8000/settings/general" class="nav-link">
                        <span>Permissions</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a href="http://localhost:8000/settings/security" class="nav-link">
                        <span>Roles</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a href="http://localhost:8000/settings/notifications" class="nav-link">
                        <span>Role Assignment</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav> --}}

<nav class="sidebar">
    <ul class="nav flex-column">
        @foreach(menu()->getMenus() as $menuKey => $menu)
            <li class="nav-item {{ !empty($menu['childrens']) ? 'dropdown' : '' }}">
                <a class="nav-link {{ !empty($menu['childrens']) ? 'dropdown-toggle' : '' }}" 
                   href="{{ !empty($menu['childrens']) ? '#'.$menuKey.'Submenu' : url($menu['route'] ?? '/') }}" 
                   {{ !empty($menu['childrens']) ? 'data-toggle=collapse aria-expanded=false' : '' }}>
                    @if(isset($menu['icon']))
                        <i class="{{ $menu['icon'] }}"></i>
                    @endif
                    <span>{{ $menu['label'] }}</span>
                </a>
                @if (!empty($menu['childrens']))
                    <ul class="collapse list-unstyled" id="{{ $menuKey }}Submenu">
                        @foreach($menu['childrens'] as $childKey => $child)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url($child['route']) }}">
                                    <i class="{{ $child['icon'] }}"></i>
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
