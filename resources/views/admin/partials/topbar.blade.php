
@php

    /*
    |--------------------------------------------------------------------------
    | ADMIN DISPLAY NAME
    |--------------------------------------------------------------------------
    */

    $adminUser = session('admin_user', []);

    $firstName = data_get($adminUser, 'first_name', '');
    $lastName = data_get($adminUser, 'last_name', '');
    $storedName = data_get($adminUser, 'name', '');

    $adminName = trim($firstName . ' ' . $lastName);

    if ($adminName === '') {
        $adminName = $storedName ?: 'Administrator';
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN INITIAL
    |--------------------------------------------------------------------------
    */

    $adminInitial = strtoupper(
        substr($adminName, 0, 1)
    );

    /*
    |--------------------------------------------------------------------------
    | PRELOADED NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    $initialUnreadCount = max(
        0,
        (int) ($notificationUnreadCount ?? 0)
    );

    $preloadedNotifications = $initialNotifications ?? [];

    $preloadSuccessful = $notificationLoadSuccessful ?? false;

@endphp

<style>
    /*
    |--------------------------------------------------------------------------
    | NOTIFICATION BELL
    |--------------------------------------------------------------------------
    */

    .citypulse-notification-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        margin-right: 22px;
        flex-shrink: 0;
    }

    .citypulse-notification-button {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        padding: 0;
        border: none;
        border-radius: 12px;
        background: transparent;
        color: #475569;
        cursor: pointer;
        transition: background-color .2s ease;
    }

    .citypulse-notification-button:hover,
    .citypulse-notification-button:focus-visible {
        background: #f1f5f9;
    }

    .citypulse-notification-button svg {
        display: block;
        width: 23px;
        height: 23px;
        stroke: currentColor;
        stroke-width: 1.9;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /*
    |--------------------------------------------------------------------------
    | UNREAD COUNT BADGE
    |--------------------------------------------------------------------------
    */

    .citypulse-notification-badge {
        position: absolute;
        top: 1px;
        right: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 19px;
        height: 19px;
        padding: 0 4px;
        border: 2px solid #fff;
        border-radius: 999px;
        background: #dc2626;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        box-sizing: border-box;
        pointer-events: none;
    }

    .citypulse-notification-badge[hidden] {
        display: none !important;
    }

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN
    |--------------------------------------------------------------------------
    */

    .citypulse-notification-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: -15px;
        z-index: 1100;
        width: 365px;
        max-width: calc(100vw - 32px);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .14);
    }

    .citypulse-notification-dropdown[hidden] {
        display: none !important;
    }

    .citypulse-notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .citypulse-notification-title {
        margin: 0;
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
    }

    .citypulse-notification-unread-label {
        color: #64748b;
        font-size: 12px;
        white-space: nowrap;
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATION ITEMS
    |--------------------------------------------------------------------------
    */

    .citypulse-notification-list {
        max-height: 365px;
        overflow-y: auto;
    }

    .citypulse-notification-item {
        display: flex;
        width: 100%;
        gap: 12px;
        padding: 14px 16px;
        border: 0;
        border-bottom: 1px solid #f1f5f9;
        background: #fff;
        text-align: left;
        font-family: inherit;
        cursor: pointer;
        box-sizing: border-box;
    }

    .citypulse-notification-item:hover {
        background: #f8fafc;
    }

    .citypulse-notification-item.is-unread {
        background: #f0fdf4;
    }

    .citypulse-notification-item.is-unread:hover {
        background: #dcfce7;
    }

    .citypulse-notification-item:focus-visible {
        outline: 2px solid #15803d;
        outline-offset: -2px;
    }

    .citypulse-notification-item:disabled {
        cursor: wait;
        opacity: .65;
    }

    .citypulse-notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #e8f5ec;
        color: #15803d;
        font-size: 17px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .citypulse-notification-icon.is-incident {
        background: #fef2f2;
        color: #dc2626;
    }

    .citypulse-notification-icon.is-review {
        background: #fffbeb;
        color: #b45309;
    }

    .citypulse-notification-content {
        flex: 1;
        min-width: 0;
    }

    .citypulse-notification-item-title {
        display: block;
        margin-bottom: 4px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
    }

    .citypulse-notification-item-message {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .citypulse-notification-time {
        display: block;
        margin-top: 6px;
        color: #94a3b8;
        font-size: 11px;
    }

    .citypulse-notification-dot {
        width: 8px;
        height: 8px;
        margin-top: 7px;
        border-radius: 50%;
        background: #16a34a;
        flex-shrink: 0;
    }

    /*
    |--------------------------------------------------------------------------
    | EMPTY AND ERROR STATES
    |--------------------------------------------------------------------------
    */

    .citypulse-notification-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 35px 20px;
        text-align: center;
    }

    .citypulse-notification-empty-title {
        margin: 0;
        color: #334155;
        font-size: 14px;
        font-weight: 600;
    }

    .citypulse-notification-empty-description {
        margin: 0;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.5;
    }

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN FOOTER
    |--------------------------------------------------------------------------
    */

    .citypulse-notification-footer {
        padding: 11px 16px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
    }

    .citypulse-notification-footer[hidden] {
        display: none !important;
    }

    .citypulse-notification-mark-all {
        padding: 5px 8px;
        border: 0;
        background: transparent;
        color: #15803d;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .citypulse-notification-mark-all:hover {
        text-decoration: underline;
    }

    .citypulse-notification-mark-all:disabled {
        opacity: .5;
        cursor: wait;
    }

    @media (max-width: 480px) {
        .citypulse-notification-wrapper {
            margin-right: 12px;
        }

        .citypulse-notification-dropdown {
            right: -80px;
            width: 310px;
        }
    }
</style>

<header class="topbar">

    <div class="topbar-spacer"></div>

    <!-- NOTIFICATION BELL -->

    <div
        class="citypulse-notification-wrapper"
        id="citypulseNotificationWrapper"
    >

        <button
            type="button"
            class="citypulse-notification-button"
            id="citypulseNotificationButton"
            aria-label="{{ $initialUnreadCount > 0
                ? 'Open notifications, ' . $initialUnreadCount . ' unread'
                : 'Open notifications' }}"
            aria-haspopup="true"
            aria-expanded="false"
            aria-controls="citypulseNotificationDropdown"
        >

            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                <path d="M10 21h4"/>
            </svg>

            <span
                class="citypulse-notification-badge"
                id="citypulseNotificationBadge"
                @if($initialUnreadCount === 0) hidden @endif
            >{{ $initialUnreadCount > 99
                ? '99+'
                : $initialUnreadCount }}</span>

        </button>

        <!-- NOTIFICATION DROPDOWN -->

        <div
            class="citypulse-notification-dropdown"
            id="citypulseNotificationDropdown"
            hidden
        >

            <div class="citypulse-notification-header">

                <h3 class="citypulse-notification-title">
                    Notifications
                </h3>

                <span
                    class="citypulse-notification-unread-label"
                    id="citypulseNotificationUnreadLabel"
                >
                    {{ $initialUnreadCount }} unread
                </span>

            </div>

            <div
                class="citypulse-notification-list"
                id="citypulseNotificationList"
            >

                @if(count($preloadedNotifications) > 0)

                    @foreach($preloadedNotifications as $notification)

                        <button
                            type="button"
                            class="citypulse-notification-item {{ empty($notification['is_read']) ? 'is-unread' : '' }}"
                            data-notification-id="{{ $notification['id'] }}"
                        >

                            <span
                                class="citypulse-notification-icon
                                @if($notification['type'] === 'incident') is-incident
                                @elseif($notification['type'] === 'review') is-review
                                @endif"
                                aria-hidden="true"
                            >
                                @if($notification['type'] === 'incident')
                                    !
                                @elseif($notification['type'] === 'review')
                                    ★
                                @else
                                    +
                                @endif
                            </span>

                            <span class="citypulse-notification-content">

                                <span class="citypulse-notification-item-title">
                                    {{ $notification['title'] }}
                                </span>

                                <span class="citypulse-notification-item-message">
                                    {{ $notification['message'] }}
                                </span>

                                <span
                                    class="citypulse-notification-time"
                                    data-notification-date="{{ $notification['created_at'] }}"
                                >
                                    {{ $notification['created_at'] }}
                                </span>

                            </span>

                            @if(empty($notification['is_read']))
                                <span
                                    class="citypulse-notification-dot"
                                    aria-label="Unread"
                                ></span>
                            @endif

                        </button>

                    @endforeach

                @elseif($preloadSuccessful)

                    <div class="citypulse-notification-empty">

                        <p class="citypulse-notification-empty-title">
                            No notifications yet
                        </p>

                        <p class="citypulse-notification-empty-description">
                            New CityPulse activity will appear here.
                        </p>

                    </div>

                @else

                    <div class="citypulse-notification-empty">

                        <p class="citypulse-notification-empty-title">
                            Loading notifications...
                        </p>

                        <p class="citypulse-notification-empty-description">
                            Please check your connection if this continues.
                        </p>

                    </div>

                @endif

            </div>

            <div
                class="citypulse-notification-footer"
                id="citypulseNotificationFooter"
                @if($initialUnreadCount === 0) hidden @endif
            >

                <button
                    type="button"
                    class="citypulse-notification-mark-all"
                    id="citypulseNotificationMarkAll"
                >
                    Mark all as read
                </button>

            </div>

        </div>

    </div>

    <!-- EXISTING ADMIN PROFILE -->

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

<script>
(function () {
    'use strict';

    /*
    |--------------------------------------------------------------------------
    | PRELOADED STATE
    |--------------------------------------------------------------------------
    */

    const initialNotifications = @json($preloadedNotifications);
    const initialUnreadCount = @json($initialUnreadCount);

    const wrapper = document.getElementById(
        'citypulseNotificationWrapper'
    );

    const button = document.getElementById(
        'citypulseNotificationButton'
    );

    const dropdown = document.getElementById(
        'citypulseNotificationDropdown'
    );

    const badge = document.getElementById(
        'citypulseNotificationBadge'
    );

    const unreadLabel = document.getElementById(
        'citypulseNotificationUnreadLabel'
    );

    const list = document.getElementById(
        'citypulseNotificationList'
    );

    const footer = document.getElementById(
        'citypulseNotificationFooter'
    );

    const markAllButton = document.getElementById(
        'citypulseNotificationMarkAll'
    );

    if (
        !wrapper || !button || !dropdown ||
        !badge || !unreadLabel || !list ||
        !footer || !markAllButton
    ) {
        return;
    }

    const listUrl = @json(
        route('admin.notifications.index')
    );

    const syncUrl = @json(
        route('admin.notifications.sync')
    );

    const markAllUrl = @json(
        route('admin.notifications.read-all')
    );

    const readUrlBase = @json(
        url('/admin/notifications')
    );

    const csrfToken = @json(csrf_token());

    let notifications = Array.isArray(initialNotifications)
        ? initialNotifications
        : [];

    let unreadCount = initialUnreadCount;
    let loading = false;
    let syncing = false;

    /*
    |--------------------------------------------------------------------------
    | DROPDOWN OPEN / CLOSE
    |--------------------------------------------------------------------------
    */

    function setDropdownOpen(isOpen) {
        dropdown.hidden = !isOpen;

        button.setAttribute(
            'aria-expanded',
            isOpen ? 'true' : 'false'
        );
    }

    button.addEventListener('click', function () {
        const opening = dropdown.hidden;

        setDropdownOpen(opening);

        if (opening) {
            // Existing notifications are already rendered.
            // Fetch only to refresh the current list.
            refreshNotifications();
        }
    });

    document.addEventListener('click', function (event) {
        if (!wrapper.contains(event.target)) {
            setDropdownOpen(false);
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !dropdown.hidden) {
            setDropdownOpen(false);
            button.focus();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | BADGE COUNT
    |--------------------------------------------------------------------------
    */

    function updateUnreadCount(count) {
        const next = Math.max(0, Number(count) || 0);

        if (next === unreadCount) {
            return;
        }

        unreadCount = next;

        badge.hidden = next === 0;
        badge.textContent = next > 99 ? '99+' : String(next);

        unreadLabel.textContent = next + ' unread';
        footer.hidden = next === 0;

        button.setAttribute(
            'aria-label',
            next > 0
                ? 'Open notifications, ' + next + ' unread'
                : 'Open notifications'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DATES
    |--------------------------------------------------------------------------
    */

    function formatDate(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value);

        return Number.isNaN(date.getTime())
            ? ''
            : date.toLocaleString();
    }

    // Format the server-rendered dates without rebuilding the list.

    list.querySelectorAll(
        '[data-notification-date]'
    ).forEach(function (element) {
        element.textContent = formatDate(
            element.dataset.notificationDate
        );
    });

    /*
    |--------------------------------------------------------------------------
    | PLACEHOLDER / ERROR MESSAGE
    |--------------------------------------------------------------------------
    */

    function showMessage(title, description) {
        list.replaceChildren();

        const container = document.createElement('div');
        container.className = 'citypulse-notification-empty';

        const heading = document.createElement('p');
        heading.className = 'citypulse-notification-empty-title';
        heading.textContent = title;

        const details = document.createElement('p');
        details.className = 'citypulse-notification-empty-description';
        details.textContent = description;

        container.append(heading, details);
        list.appendChild(container);
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN NOTIFICATION
    |--------------------------------------------------------------------------
    */

    async function openNotification(notification, item) {
        if (!notification || !notification.id) {
            return;
        }

        item.disabled = true;

        try {
            const response = await fetch(
                readUrlBase + '/' +
                encodeURIComponent(notification.id) +
                '/read',
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Mark as read failed');
            }

            const data = await response.json();
            const target = data.url;

            if (
                typeof target === 'string' &&
                target.startsWith('/admin/') &&
                !target.startsWith('//')
            ) {
                window.location.assign(target);
                return;
            }

            notification.is_read = true;

            renderNotifications();
            await refreshNotifications();

        } catch (error) {
            item.disabled = false;
            console.error(
                'CityPulse notification open error:',
                error
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT DELEGATION
    |--------------------------------------------------------------------------
    |
    | Works with both server-rendered and dynamically
    | updated notification items.
    |
    */

    list.addEventListener('click', function (event) {
        const item = event.target.closest(
            '[data-notification-id]'
        );

        if (!item || !list.contains(item) || item.disabled) {
            return;
        }

        const notification = notifications.find(function (entry) {
            return String(entry.id) ===
                item.dataset.notificationId;
        });

        openNotification(notification, item);
    });

    /*
    |--------------------------------------------------------------------------
    | RENDER NOTIFICATION LIST
    |--------------------------------------------------------------------------
    */

    function renderNotifications() {
        list.replaceChildren();

        if (!notifications.length) {
            showMessage(
                'No notifications yet',
                'New CityPulse activity will appear here.'
            );

            return;
        }

        notifications.forEach(function (notification) {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'citypulse-notification-item';
            item.dataset.notificationId = notification.id;

            if (!notification.is_read) {
                item.classList.add('is-unread');
            }

            const icon = document.createElement('span');
            icon.className = 'citypulse-notification-icon';
            icon.setAttribute('aria-hidden', 'true');

            if (notification.type === 'incident') {
                icon.classList.add('is-incident');
                icon.textContent = '!';
            } else if (notification.type === 'review') {
                icon.classList.add('is-review');
                icon.textContent = '★';
            } else {
                icon.textContent = '+';
            }

            const content = document.createElement('span');
            content.className = 'citypulse-notification-content';

            const title = document.createElement('span');
            title.className = 'citypulse-notification-item-title';
            title.textContent = notification.title || '';

            const message = document.createElement('span');
            message.className = 'citypulse-notification-item-message';
            message.textContent = notification.message || '';

            const time = document.createElement('span');
            time.className = 'citypulse-notification-time';
            time.textContent = formatDate(notification.created_at);

            content.append(title, message, time);
            item.append(icon, content);

            if (!notification.is_read) {
                const dot = document.createElement('span');
                dot.className = 'citypulse-notification-dot';
                dot.setAttribute('aria-label', 'Unread');
                item.appendChild(dot);
            }

            list.appendChild(item);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FAST REFRESH — NO SOURCE TABLE SYNCHRONIZATION
    |--------------------------------------------------------------------------
    */

    async function refreshNotifications() {
        if (loading) {
            return;
        }

        loading = true;

        try {
            const response = await fetch(listUrl, {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(
                    'Notification request failed: ' + response.status
                );
            }

            const data = await response.json();

            const updatedNotifications = Array.isArray(
                data.notifications
            ) ? data.notifications : [];

            const previousState = JSON.stringify(notifications);
            const nextState = JSON.stringify(updatedNotifications);

            notifications = updatedNotifications;

            updateUnreadCount(data.unread_count);

            // Only redraw if data has changed.
            if (
                !dropdown.hidden &&
                previousState !== nextState
            ) {
                renderNotifications();
            }

        } catch (error) {
            console.error(
                'CityPulse notification refresh error:',
                error
            );

            // Keep previously loaded notifications visible.
            // Do not replace valid data with an error or
            // a misleading "No notifications" message.

            if (!notifications.length && !dropdown.hidden) {
                showMessage(
                    'Unable to refresh notifications',
                    'Please try opening the bell again.'
                );
            }

        } finally {
            loading = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONIZE NEW ACTIVITY
    |--------------------------------------------------------------------------
    */

    async function syncNotifications() {
        if (syncing || document.hidden) {
            return;
        }

        syncing = true;

        try {
            const response = await fetch(syncUrl, {
                method: 'POST',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(
                    'Notification sync failed: ' + response.status
                );
            }

            const data = await response.json();
            const nextCount = Math.max(
                0,
                Number(data.unread_count) || 0
            );

            const changed = nextCount !== unreadCount;

            updateUnreadCount(nextCount);

            // Refresh the full list if new unread
            // activity has been detected.
            if (changed) {
                await refreshNotifications();
            }

        } catch (error) {
            console.error(
                'CityPulse notification sync error:',
                error
            );

        } finally {
            syncing = false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MARK ALL AS READ
    |--------------------------------------------------------------------------
    */

    markAllButton.addEventListener('click', async function () {
        markAllButton.disabled = true;

        try {
            const response = await fetch(markAllUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Mark all as read failed');
            }

            notifications = notifications.map(function (notification) {
                return {
                    ...notification,
                    is_read: true
                };
            });

            updateUnreadCount(0);
            renderNotifications();

            await refreshNotifications();

        } catch (error) {
            console.error(
                'CityPulse mark all read error:',
                error
            );

        } finally {
            markAllButton.disabled = false;
        }
    });

    /*
    |--------------------------------------------------------------------------
    | INITIALIZATION
    |--------------------------------------------------------------------------
    |
    | Notifications are already in the initial HTML.
    | Opening the bell does not need to show a placeholder.
    |
    */

    syncNotifications();

    setInterval(function () {
        if (!document.hidden) {
            syncNotifications();
        }
    }, 15000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            syncNotifications();
        }
    });

})();
</script>
