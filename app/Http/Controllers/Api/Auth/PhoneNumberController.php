<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use App\Models\User;

class PhoneNumberController extends Controller
{
    protected function verify(Request $request)
    {
        $data = $request->validate([
            'verification_code' => ['required', 'numeric'],
            'phone_number' => ['required', 'string'],
        ]);
        /* Get credentials from .env */
        $phone_number = "+".$request->phone_number;
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $verification = $twilio->verify->v2->services($twilio_verify_sid)
            ->verificationChecks
            ->create($request->verification_code, array('to' => $phone_number));
        if ($verification->valid) {
            $user = tap(User::where('phone_number', $phone_number))->update(['isVerified' => true]);
            /* Authenticate user */
            //Auth::login($user->first());
            return response([
                'message'=> 'Phone number verified'
            ]);
        }
        return response([
            'message'=> 'Invalid verification code entered!'
        ]);
    }

}
