<!doctype html>
    <html lang="en" data-framework="react">
        <head>
            <meta charset="utf-8" />
            @if (isset($encrypted_csrf_token))
                <meta name="csrf_token" content="{{ $encrypted_csrf_token }}" />
            @endif

            <link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,600,700' rel='stylesheet' type='text/css'>
            <link href="{{ asset('dist/css/screen.css') }}" media="screen, projection" rel="stylesheet" type="text/css" />
        </head>

        <body>
            <div class="wrap">
                <section id="view"></section>
            </div>

            <script data-main="{{ asset('dist/js/main.js') }}" src="{{ asset('dist/js/require.min.js') }}"></script>
        </body>
    </html>