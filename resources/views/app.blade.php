<!doctype html>
    <html>
        <head>

        </head>

        <body>
            @yield('content')

            <script data-main="{{ asset('dist/js/main.min.js') }}" src="{{ asset('dist/js/_require.min.js') }}"></script>
        </body>
    </html>