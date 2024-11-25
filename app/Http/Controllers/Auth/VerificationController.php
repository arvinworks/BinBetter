<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Auth; 

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show(Request $request)
    {   

        $page = 'Verify Email';

        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('auth.verify', compact('page'));
    }

    public function otp_verify(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);
    
        $user = User::getCurrentUser();
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        if (now()->greaterThan($user->otp_expire)) {
            return response()->json(['message' => 'Verification code has expired.'], 422);
        }
    
        if (trim($request->input('code')) !== trim($user->otp_code)) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }

        $authuser = User::where('email', $user->email)->first();
        $authuser->email_verified_at = now();
        $authuser->save(); 
    
        return response()->json([
            'message' => 'Account Verified!',
            'URL' => "/home"
        ], 200);
    }
    

}
