<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CityPulse | Admin Login</title>

    <link rel="stylesheet" href="/css/variables.css">
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/pages/login.css">
</head>

<body class="login-page">

    <main class="login-wrapper">

        <section class="login-card">

            <!-- BRAND -->

            <div class="login-brand">

                <div class="login-logo-box">
                    <div class="login-logo">
                        C
                    </div>
                </div>

                <h1>CityPulse</h1>

                <div class="login-subtitle">
                    Administrator Dashboard
                </div>

                <div class="login-helper">
                    Smart Tourism & Safety Management
                </div>

            </div>

            <div class="login-divider"></div>


            <!-- ERROR MESSAGE -->

            @if ($errors->any())

                <div class="alert-error">
                    {{ $errors->first() }}
                </div>

            @endif


            <!-- LOGIN FORM -->

            <form
                method="POST"
                action="{{ route('admin.login.submit') }}"
            >

                @csrf


                <!-- EMAIL -->

                <div class="form-group">

                    <label
                        for="email"
                        class="form-label"
                    >
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="Enter administrator email"
                        autocomplete="email"
                        required
                        autofocus
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label
                        for="password"
                        class="form-label"
                    >
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle"
                            aria-label="Show password"
                            title="Show password"
                        >
                            Show
                        </button>

                    </div>

                </div>


                <!-- SIGN IN -->

                <button
                    type="submit"
                    class="login-button"
                >
                    Sign In
                </button>

            </form>


            <!-- SECURITY NOTICE -->

            <div class="security-note">
                Authorized CityPulse administrators only.
            </div>


            <!-- FOOTER -->

            <div class="login-footer">
                CityPulse Administration Management System
            </div>

        </section>

    </main>


    <!-- SHOW / HIDE PASSWORD -->

    <script>
        const passwordInput =
            document.getElementById('password');

        const togglePassword =
            document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function () {

            const isHidden =
                passwordInput.type === 'password';

            passwordInput.type =
                isHidden ? 'text' : 'password';

            togglePassword.textContent =
                isHidden ? 'Hide' : 'Show';

            togglePassword.setAttribute(
                'aria-label',
                isHidden
                    ? 'Hide password'
                    : 'Show password'
            );

            togglePassword.setAttribute(
                'title',
                isHidden
                    ? 'Hide password'
                    : 'Show password'
            );

        });
    </script>

</body>

</html>