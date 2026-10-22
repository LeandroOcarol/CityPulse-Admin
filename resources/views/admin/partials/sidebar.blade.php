<aside class="sidebar">

    <!-- Brand -->

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


    <!-- Navigation -->

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
            href="{{ route('admin.incident-reports.index') }}"
            class="menu-item {{ request()->routeIs('admin.incident-reports.*') ? 'active' : '' }}"
        >
            Incident Reports
        </a>


        <a
            href="{{ route('admin.crime-records.index') }}"
            class="menu-item {{ request()->routeIs('admin.crime-records.*') ? 'active' : '' }}"
        >
            Crime Records
        </a>


        <a
            href="{{ route('admin.safety-alerts.index') }}"
            class="menu-item {{ request()->routeIs('admin.safety-alerts.*') ? 'active' : '' }}"
        >
            Safety Alerts
        </a>


        <!-- Analytics -->

        <div class="menu-title">
            Analytics
        </div>


        <a
            href="{{ route('admin.live-tourist-map.index') }}"
            class="menu-item {{ request()->routeIs('admin.live-tourist-map.*') ? 'active' : '' }}"
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


        <!-- System -->

        <div class="menu-title">
            System
        </div>


        <a
            href="{{ route('admin.ratings-feedback.index') }}"
            class="menu-item {{ request()->routeIs('admin.ratings-feedback.*') ? 'active' : '' }}"
        >
            Ratings & Feedback
        </a>


        <a
            href="{{ route('admin.reports.index') }}"
            class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
        >
            Reports
        </a>


        <a
            href="#"
            class="menu-item"
        >
            Settings
        </a>


        <!-- Logout -->

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