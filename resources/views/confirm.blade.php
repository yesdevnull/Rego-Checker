<!doctype html>
    <html lang="en">
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
                <section id="view">
                    <header>
                        <h1>Confirm your email address</h1>
                    </header>

                    <section class="content">
                        <div class="box">
                            @if ($errors->first('email'))
                                {!! $errors->first('email', '<div class="message text-center error"><strong>Error:</strong> :message</div>') !!}
                            @endif

                            {{-- This could be done better... --}}

                            @if (isset($error))
                                <div class="message text-center error">
                                    <strong>Error: </strong> {{ $error }}
                                </div>
                            @endif

                            @if (isset($warning))
                                <div class="message text-center warning">
                                    <strong>Warning: </strong> {{ $warning }}
                                </div>
                            @endif

                            @if (isset($success))
                                <div class="message text-center success">
                                    {{ $success }}
                                </div>
                            @endif
                        </div>
                    </section>

                    <footer>
                        <p>
                            Brought to you by <a href="https://www.yesdevnull.net">Dan Barrett</a> &middot; <a href="https://github.com/yesdevnull/Rego-Checker">View on GitHub</a>
                        </p>
                    </footer>
                </section>
            </div>
        </body>
    </html>