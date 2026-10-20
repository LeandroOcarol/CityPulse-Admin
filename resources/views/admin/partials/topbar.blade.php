<header class="topbar">

    <div class="search-box">

        <input
            type="search"
            placeholder="Search accounts, spots, or reports..."
            aria-label="Search CityPulse"
        >

    </div>


    <div class="admin-info">

        <div class="admin-avatar">

            {{
                strtoupper(
                    substr(
                        session(
                            'admin_user.first_name',
                            'A'
                        ),
                        0,
                        1
                    )
                )
            }}

        </div>


        <div>

            <div class="admin-name">

                {{ session('admin_user.first_name') }}
                {{ session('admin_user.last_name') }}

            </div>

            <div class="admin-role">
                Administrator
            </div>

        </div>

    </div>

</header>