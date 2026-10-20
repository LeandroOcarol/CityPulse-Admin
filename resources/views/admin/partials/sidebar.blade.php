<aside class="sidebar">

    <div class="sidebar-brand">

        <div class="brand-logo">
            C
        </div>

        <div>

            <div class="brand-name">
                CityPulse
            </div>

            <div class="brand-subtitle">
                Municipal Admin
            </div>

        </div>

    </div>


    <nav class="sidebar-menu">

        <a
            href="{{ route('admin.dashboard') }}"
            class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>


        <a
            href="{{ route('admin.tourist-accounts.index') }}"
            class="menu-item {{ request()->routeIs('admin.tourist-accounts.*') ? 'active' : '' }}"
        >
            Tourist Accounts
        </a>


        <a
            href="{{ route('admin.tourist-spots.index') }}"
            class="menu-item {{ request()->routeIs('admin.tourist-spots.*') ? 'active' : '' }}"
        >
            Tourist Spots
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Incident Reports
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Crime Records
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Safety Alerts
        </a>


        <div class="menu-title">
            Analytics
        </div>


        <a
            href="#"
            class="menu-item"
        >
            Live Tourist Map
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Safety Heat Map
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Geofence Zones
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Map Routes
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Predictive Analytics
        </a>


        <div class="menu-title">
            System
        </div>


        <a
            href="#"
            class="menu-item"
        >
            Ratings & Feedback
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Reports
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Settings
        </a>


        <form
            method="POST"
            action="{{ route('admin.logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="menu-item logout-item"
            >
                Logout
            </button>

        </form>

    </nav>

</aside>