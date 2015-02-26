# Rego Checker

This tool allows you to check the current status of your vehicles registration in Western Australia.

## Birth

This idea came to light when I missed a registration from the Department of Transport (WA) with my registration renewal.  Since DoT no longer issue registration stickers, I wasn't aware of the expiry.  Needless to say, my car was unregistered for a few days which could have caused me grief, except for the fact I rarely drive so it wasn't a problem.

I'm not aware of the DoT having a notification service, so I'm making one instead.

## Plans

I plan to get this working for all states of Australia that offer an online tool to check your registration.

## Notes
To get this up and running on your own system, you'll need to do the following:

1. `npm install` to download Grunt etc
2. `bower install` to download other project dependencies
3. `composer install` to download Laravel
4. Have [Compass](http://compass-style.org/) installed
5. Run `grunt build` to build all dependencies
6. Run `php artisan serve` and visit `http://localhost:8000` to check it out!