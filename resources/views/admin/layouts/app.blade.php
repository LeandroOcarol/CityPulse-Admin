
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'CityPulse Admin')
    </title>

    <link rel="stylesheet" href="/css/variables.css">
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/layout.css">

    @stack('styles')
</head>

<body class="@yield('body-class')">

    @include('admin.partials.sidebar')

    <main class="main">

        @php
            /*
            |--------------------------------------------------------------------------
            | CITYPULSE PRELOADED ADMIN NOTIFICATIONS
            |--------------------------------------------------------------------------
            |
            | Load the unread count and recent notification
            | records before rendering the topbar.
            |
            | The notification dropdown will not need to
            | wait for another HTTP request when opened.
            |
            */

            $notificationUnreadCount = 0;
            $initialNotifications = [];
            $notificationLoadSuccessful = false;

            $currentAdmin = session('admin_user', []);

            $currentAdminId = data_get(
                $currentAdmin,
                'id'
            );

            $currentAdminRole = strtolower(
                (string) data_get(
                    $currentAdmin,
                    'role',
                    ''
                )
            );

            if (
                is_string($currentAdminId) &&
                \Illuminate\Support\Str::isUuid($currentAdminId) &&
                $currentAdminRole === 'admin'
            ) {
                try {
                    $notificationUnreadCount =
                        \Illuminate\Support\Facades\DB::table(
                            'admin_notifications'
                        )
                        ->where('admin_id', $currentAdminId)
                        ->whereNull('read_at')
                        ->count();

                    $initialNotifications =
                        \Illuminate\Support\Facades\DB::table(
                            'admin_notifications'
                        )
                        ->where('admin_id', $currentAdminId)
                        ->orderByDesc('created_at')
                        ->orderByDesc('id')
                        ->limit(30)
                        ->get([
                            'id',
                            'source_type',
                            'title',
                            'message',
                            'target_url',
                            'created_at',
                            'read_at',
                        ])
                        ->map(function ($notification) {
                            return [
                                'id' => $notification->id,
                                'type' => $notification->source_type,
                                'title' => $notification->title,
                                'message' => $notification->message,
                                'url' => $notification->target_url,
                                'created_at' =>
                                    \Illuminate\Support\Carbon::parse(
                                        $notification->created_at
                                    )->toIso8601String(),
                                'is_read' =>
                                    $notification->read_at !== null,
                            ];
                        })
                        ->values()
                        ->all();

                    $notificationLoadSuccessful = true;

                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning(
                        'CityPulse notification preload failed',
                        [
                            'exception' => $e->getMessage(),
                        ]
                    );
                }
            }
        @endphp

        @include(
            'admin.partials.topbar',
            [
                'notificationUnreadCount' =>
                    $notificationUnreadCount,

                'initialNotifications' =>
                    $initialNotifications,

                'notificationLoadSuccessful' =>
                    $notificationLoadSuccessful,
            ]
        )

        <div class="content">

            @yield('content')

        </div>

    </main>

    @stack('scripts')

</body>

</html>
