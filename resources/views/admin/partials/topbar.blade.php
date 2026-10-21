@php

    /*
    |--------------------------------------------------------------------------
    | ADMIN DISPLAY NAME
    |--------------------------------------------------------------------------
    |
    | Supports both:
    |
    | first_name + last_name
    |
    | and:
    |
    | name
    |
    */

    $adminUser = session('admin_user', []);

    $firstName = data_get(
        $adminUser,
        'first_name',
        ''
    );

    $lastName = data_get(
        $adminUser,
        'last_name',
        ''
    );

    $storedName = data_get(
        $adminUser,
        'name',
        ''
    );

    $adminName = trim(
        $firstName . ' ' . $lastName
    );

    if ($adminName === '') {
        $adminName = $storedName ?: 'Administrator';
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN INITIAL
    |--------------------------------------------------------------------------
    */

    $adminInitial = strtoupper(
        substr(
            $adminName,
            0,
            1
        )
    );

@endphp


<header class="topbar">

    <!-- =========================================
         LEFT SPACER
    ========================================== -->

    <div class="topbar-spacer"></div>



    <!-- =========================================
         ADMIN PROFILE
    ========================================== -->

    <div class="topbar-admin">

        <div class="topbar-avatar">
            {{ $adminInitial }}
        </div>


        <div class="topbar-admin-info">

            <div class="topbar-admin-name">
                {{ $adminName }}
            </div>

            <div class="topbar-admin-role">
                Administrator
            </div>

        </div>

    </div>

</header>