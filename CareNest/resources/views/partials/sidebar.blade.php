@php
    $role = auth()->user()->role ?? 'admin';
    $prefix = $role === 'midwife' ? 'midwife.' : ($role === 'mother' ? 'mother.' : 'admin.');
@endphp
<aside class="sidebar">
    <a href="{{ route($prefix . 'dashboard') }}" class="sidebar-brand">
        <i class="fa-solid fa-leaf" style="color:#00c853;"></i>
        <span>CareNest</span>
    </a>

    <ul class="sidebar-menu">
        <li class="nav-item {{ request()->routeIs($prefix . 'dashboard') ? 'active' : '' }}">
            <a href="{{ route($prefix . 'dashboard') }}">
                <i class="fa-solid fa-table-cells-large"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if(in_array($role, ['provider', 'admin']))
            <li class="nav-item {{ request()->routeIs('admin.midwives.*', 'admin.midwife-requests.*') ? 'active' : '' }}">
                <a href="{{ route('admin.midwives.index') }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Midwife Management</span>
                </a>
            </li>
        @endif

        @if(in_array($role, ['provider', 'admin', 'midwife']))
            <li class="nav-item {{ request()->routeIs($prefix . 'mothers.*') ? 'active' : '' }}">
                <a href="{{ route($prefix . 'mothers.index') }}">
                    <i class="fa-solid fa-person-pregnant"></i>
                    <span>Mothers</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs($prefix . 'children.*') ? 'active' : '' }}">
                <a href="{{ route($prefix . 'children.index') }}">
                    <i class="fa-solid fa-baby"></i>
                    <span>Children</span>
                </a>
            </li>
        @endif

        @if($role === 'mother')
            <li class="nav-item {{ request()->routeIs('mother.profile') ? 'active' : '' }}">
                <a href="{{ route('mother.profile') }}">
                    <i class="fa-solid fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('mother.children.*') ? 'active' : '' }}">
                <a href="{{ route('mother.children.index') }}">
                    <i class="fa-solid fa-baby"></i>
                    <span>My Children</span>
                </a>
            </li>
        @endif

        <li class="nav-item {{ request()->routeIs($prefix . 'immunizations.*') ? 'active' : '' }}">
            <a href="{{ route($prefix . 'immunizations.index') }}">
                <i class="fa-solid fa-syringe"></i>
                <span>Immunizations</span>
            </a>
        </li>

        @if(in_array($role, ['provider', 'admin', 'midwife']))
            <li class="nav-item {{ request()->routeIs($prefix . 'alerts.*') ? 'active' : '' }}">
                <a href="{{ route($prefix . 'alerts.index') }}">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>High-Risk Alerts</span>
                </a>
            </li>
        @else
            <li class="nav-item {{ request()->routeIs('mother.notifications.index') ? 'active' : '' }}">
                <a href="{{ route('mother.notifications.index') }}">
                    <i class="fa-solid fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
        @endif
        
        <li class="nav-item">
            <a href="#attendances">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Clinic Attendances</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        @if(in_array($role, ['provider', 'admin', 'midwife']))
            <a href="{{ route($prefix . 'mothers.create') }}" class="btn-schedule" style="text-decoration:none;">
                <i class="fa-solid fa-user-plus"></i>
                <span>Register Mother</span>
            </a>
        @endif
    </div>
</aside>
