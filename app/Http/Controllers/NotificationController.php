<?php namespace App\Http\Controllers;

use Controller;
use Validator;
use Crypt;
use Illuminate\Http\Request;
use App\Email;
use App\Plate;
use App\Exceptions\ApiErrorException;

class Notification extends Controller {
    public function subscribe(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'unique:emails'
            ],
            'plate' => [
                'required',
                'max:20'
            ]
        ]);

        if ($validator->fails()) {
            throw new ApiErrorException($validator->errors()->first(), 500);
        } else {
            $email = new Email;

            $email->email = $request->input('email');
            $email->enabled = true;
            // Just need a random string for the email confirmation token
            $email->token = Crypt::encrypt($request->input('email'));

            $email->save();

            $plate = new Plate;

            $plate->state = 'WA';
            $plate->plate = $request->input('plate');

            $plate->save();

            $email->plates()->save($plate);

            return [
                'type' => 'success',
                'message' => 'Successfully subscribed to notifications.  Please check your inbox to confirm your email address'
            ];
        }
    }

    // https://mailtrap.io/
}