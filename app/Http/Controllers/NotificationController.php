<?php namespace App\Http\Controllers;

use Controller;
use Validator;
use Crypt;
use Log;
use Illuminate\Http\Request;
use App\Email;
use App\Plate;
use App\Exceptions\ApiErrorException;
use Debugbar;

class Notification extends Controller {
    public function subscribe(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email'
            ],
            'plate' => [
                'required',
                'max:20'
            ]
        ]);

        if ($validator->fails()) {
            throw new ApiErrorException($validator->errors()->first(), 500);
        } else {
            // Get all email addresses that have the license plate attached through a relation
            $emailPlatesMatch = Email::whereHas('plates', function($query) use ($request) {
                // Will need to factor in state region
                $query->where('plate', '=', $request->input('plate'));
            })->get();

            Debugbar::info('Email/Plate whereHas Count: ' . count($emailPlatesMatch));

            if (count($emailPlatesMatch) > 0) {
                // Filter through the emails and pluck out all emails that don't match the input
                $sameEmail = $emailPlatesMatch->filter(function($email) use ($request) {
                    if ($email->email === $request->input('email')) {
                        return $email;
                    }
                });

                Debugbar::info('sameEmail: ' . count($sameEmail));

                // If the email address exists in that list, they have already subscribed to this plate/email alert
                if (count($sameEmail) > 0) {
                    throw new ApiErrorException('This email address and plate are already subscribed.', 500);
                }
            } else {
                $email = Email::where('email', '=', $request->input('email'))->first();

                Debugbar::info('Email exists: ' . count($email));
                Debugbar::info($email);

                if (count($email) > 0) {
                    Debugbar::info('Email exists, assign further work to first email');
                } else {
                    Debugbar::info('Email does not exist, let\'s create the model');
                    $email = new Email;

                    $email->email = $request->input('email');
                    $email->enabled = true;
                    // Just need a random string for the email confirmation token
                    $email->token = Crypt::encrypt($request->input('email'));

                    // Save the Email Model
                    $email->save();
                }
            }

            // If we get this far, the plate doesn't exist, so let's create it
            $plate = new Plate;

            $plate->state = 'WA';
            $plate->plate = $request->input('plate');

            // Save the Plate Model
            $plate->save();

            // Link Plate to Email
            $email->plates()->save($plate);

            Log::info('Saved email and plate to database');

            return [
                'type' => 'success',
                'message' => 'Successfully subscribed to notifications.  Please check your inbox to confirm your email address'
            ];
        }
    }

    // https://mailtrap.io/
}