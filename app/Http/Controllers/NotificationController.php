<?php namespace App\Http\Controllers;

use Log;
use Mail;
use Crypt;
use Debugbar;
use Validator;
use App\Email;
use App\Plate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Exceptions\ApiErrorException;

/**
 * Class NotificationController
 * @package App\Http\Controllers
 */
class NotificationController extends Controller {
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws ApiErrorException
     */
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

            if (count($emailPlatesMatch) > 0) {
                // Filter through the emails and pluck out all emails that match the input
                $sameEmail = $emailPlatesMatch->filter(function($email) use ($request) {
                    if ($email->email === $request->input('email')) {
                        return $email;
                    }
                });

                Log::info('sameEmail: ' . count($sameEmail));
                Log::info($sameEmail);

                // If the email address exists in that list, they have already subscribed to this plate/email alert
                if (count($sameEmail) > 0) {
                    throw new ApiErrorException('This email address and plate are already subscribed', 500);
                }
            }

            // Email address hasn't been used in association with the supplied plate
            $email = Email::where('email', '=', $request->input('email'))->first();

            Log::info('Email exists: ' . count($email));
            Log::info($email);

            if (count($email) > 0) {
                Log::info('Email exists, assign further work to first email');
            } else {
                Log::info('Email does not exist, let\'s create the model');
                $email = new Email;

                $email->email = $request->input('email');
                $email->enabled = true;
                // Just need a random string for the email confirmation token
                $email->token = substr(Crypt::encrypt($request->input('email')), 0, 40);

                // Save the Email Model
                $email->save();
            }

            // See if plate is already in database
            $plates = Plate::where('state', '=', 'WA', 'and')->where('plate', '=', $request->input('plate'))->get();

            if (count($plates) > 0) {
                // Plate is already in database, use it from now on
                Log::info('Plate exists, using result from Plate::where query');
                $plate = $plates[0];
            } else {
                // If we get this far, the plate doesn't exist, so let's create it
                $plate = new Plate;

                $plate->state = 'WA';
                $plate->plate = $request->input('plate');

                // Save the Plate Model
                $plate->save();
                Log::info('Plate created and saved');
            }

            // Link Plate to Email
            $email->plates()->save($plate);

            // If the email address already exists and is confirmed, don't send another confirmation email
            if ($email->isConfirmed()) {
                Log::info('Email exists, but saved plate to database');

                return response()->json([
                    'type' => 'success',
                    'message' => sprintf('Successfully enabled notifications for plate %s', $plate->plate)
                ]);
            } else {
                // Get this queued up for email
                $this->_queueMail($email->email, $email->token);

                Log::info('Saved email and plate to database');

                return response()->json([
                    'type' => 'success',
                    'message' => 'Successfully subscribed to notifications.  Please check your inbox to confirm your email address'
                ]);
            }
        }
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function confirm(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required'
            ],
            'token' => [
                'required'
            ]
        ]);

        if ($validator->fails()) {
            return view('confirm')->withErrors($validator);
        } else {
            $confirmation = Email::where('email', '=', Crypt::decrypt($request->input('email')), 'and')->where('token', '=', $request->input('token'))->first();

            if (count($confirmation) > 0) {
                if ($confirmation->confirmed) {
                    return view('confirm')->with('warning', 'Email address already confirmed');
                } else {
                    // The user and token match, set them as confirmed
                    $confirmation->confirmed = true;

                    $confirmation->save();

                    return view('confirm')->with('success', 'Successfully confirmed token and email address');
                }
            } else {
                return view('confirm')->with('error', 'Token does not match email address');
            }
        }
    }

    /**
     * @param $expiry
     * @return bool
     */
    public function checkDates($expiry) {
        $now = Carbon::now();
        $expiryNormalised = Carbon::createFromFormat('d/m/Y', $expiry);

        // Replace below with return true; to test the expiry emails
        return ($expiryNormalised->diffInDays($now) < 31) ? true : false;
    }

    /**
     * @param Plate $plate
     */
    public function sendAlertEmail(Plate $plate) {
        $emails = Email::whereHas('plates', function($query) use ($plate) {
            $query->where('plate', '=', $plate->plate);
        })->get();

        $emails = $emails->filter(function($email) {
            return $email->isEnabledAndConfirmed();
        });

        $emails->each(function($email) use ($plate) {
            Log::info(sprintf('Sending alert to %s', $email->email));

            Mail::queue('emails.expiring', ['plate' => $plate->plate], function($message) use ($email) {
                $message->to($email->email)->subject('Expiring Plate');
            });
        });
    }

    /**
     * @param $email
     * @param $token
     */
    public function _queueMail($email, $token) {
        $data = [
            'email' => Crypt::encrypt($email),
            'token' => $token
        ];

        Mail::queue('emails.confirm', $data, function($message) use ($email) {
            $message->to($email)->subject('Confirm your Email');
        });
    }

    // https://mailtrap.io/
}