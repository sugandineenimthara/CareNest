@php
    $cUser = Auth::user();
    $cRole = $cUser ? $cUser->role : 'admin';
    $displayRole = in_array($cRole, ['provider', 'admin']) ? 'Admin' : ucfirst($cRole);
    $displayName = $cUser ? $cUser->name : 'Administrator';
@endphp

<div class="user-badge-wrapper" style="position: relative; display: inline-block;">
    <div class="user-badge-container" id="userBadgeTrigger" onclick="toggleUserDropdown(event)" title="User Options">
        <span class="user-role-label">{{ $displayRole }}</span>
        <div class="user-avatar">
            <i class="fa-regular fa-user"></i>
        </div>
        <i class="fa-solid fa-chevron-down user-dropdown-arrow"></i>
    </div>

    <!-- Dropdown Menu -->
    <div class="user-dropdown-menu" id="userDropdownMenu">
        <div class="dropdown-header">
            <div class="dropdown-name">{{ $displayName }}</div>
            <div class="dropdown-role-tag">{{ $displayRole }}</div>
        </div>
        
        <div class="dropdown-divider"></div>

        <a href="{{ route('password.change') }}" class="dropdown-item">
            <i class="fa-solid fa-key"></i>
            <span>Change Password</span>
        </a>

        <div class="dropdown-divider"></div>

        <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
            @csrf
            <button type="submit" class="dropdown-item dropdown-logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</div>

<style>
    .top-header {
        min-height: 76px !important;
        box-sizing: border-box !important;
    }

    .header-right {
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
        margin-left: auto !important;
    }

    .user-badge-wrapper {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .user-badge-container {
        display: inline-flex !important;
        align-items: center !important;
        gap: 10px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid #e2e8f0 !important;
        padding: 6px 14px 6px 16px !important;
        border-radius: 9999px !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
        cursor: pointer !important;
        user-select: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        height: 44px !important;
        box-sizing: border-box !important;
    }

    .user-badge-container:hover {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-1px) !important;
    }

    .user-badge-container .user-role-label {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        letter-spacing: 0.3px !important;
        line-height: 1 !important;
    }

    .user-badge-container .user-avatar {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        border-radius: 50% !important;
        background: #e8f5e9 !important;
        color: #00c853 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
    }

    .user-badge-container:hover .user-avatar {
        background: #00c853 !important;
        color: #ffffff !important;
    }

    .user-dropdown-arrow {
        font-size: 11px;
        color: #94a3b8;
        transition: transform 0.2s ease;
    }

    .user-badge-wrapper.open .user-dropdown-arrow {
        transform: rotate(180deg);
    }

    .user-dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 220px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
        padding: 8px;
        z-index: 9999;
        display: none;
        animation: userDropdownAnim 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .user-dropdown-menu.show {
        display: block;
    }

    @keyframes userDropdownAnim {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(0.96);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dropdown-header {
        padding: 10px 14px 8px;
    }

    .dropdown-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dropdown-role-tag {
        font-size: 11px;
        font-weight: 700;
        color: #00c853;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    .dropdown-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 6px 0;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.15s ease;
        border: none;
        background: transparent;
        width: 100%;
        cursor: pointer;
        text-align: left;
        box-sizing: border-box;
    }

    .dropdown-item:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .dropdown-item i {
        font-size: 14px;
        width: 18px;
        text-align: center;
        color: #64748b;
        transition: color 0.15s;
    }

    .dropdown-item:hover i {
        color: #00c853;
    }

    .dropdown-item.dropdown-logout:hover {
        background: #fef2f2;
        color: #ef4444;
    }

    .dropdown-item.dropdown-logout:hover i {
        color: #ef4444;
    }
</style>

<script>
    if (typeof window.initUserDropdownAttached === 'undefined') {
        window.initUserDropdownAttached = true;

        window.toggleUserDropdown = function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const wrapper = document.querySelector('.user-badge-wrapper');
            const menu = document.getElementById('userDropdownMenu');
            if (menu && wrapper) {
                const isOpen = menu.classList.contains('show');
                if (isOpen) {
                    menu.classList.remove('show');
                    wrapper.classList.remove('open');
                } else {
                    menu.classList.add('show');
                    wrapper.classList.add('open');
                }
            }
        };

        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.user-badge-wrapper');
            const menu = document.getElementById('userDropdownMenu');
            if (menu && wrapper && !wrapper.contains(e.target)) {
                menu.classList.remove('show');
                wrapper.classList.remove('open');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const wrapper = document.querySelector('.user-badge-wrapper');
                const menu = document.getElementById('userDropdownMenu');
                if (menu) menu.classList.remove('show');
                if (wrapper) wrapper.classList.remove('open');
            }
        });
    }
</script>
