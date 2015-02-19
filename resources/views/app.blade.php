<!doctype html>
    <html lang="en" data-framework="react">
        <head>
            <meta charset="utf-8" />
            <meta name="csrf_token" content="{{ csrf_token() }}" />
        </head>

        <body>
            <section id="view">

            </section>

            <script data-main="{{ asset('dist/js/main.min.js') }}" src="{{ asset('dist/js/_require.min.js') }}"></script>
        </body>
    </html>