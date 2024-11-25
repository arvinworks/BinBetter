<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionExpiration;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $page = 'Sign In';
        return view('auth.login', compact('page'));
    }

   public function ajaxLogin(Request $request)
{
   
    // Validate the input
    $validator = Validator::make($request->all(), [
        'identifier' => 'required|string',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Get the identifier and determine if it is an email or username
    $identifier = $request->input('identifier');
    $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    // Check if the user exists and is not disabled
    $user = User::where($field, $identifier)->first();

    if (!$user) {
        return response()->json(['errors' => ['identifier' => ['User not found']]], 422);
    }

    if ($user->isDisable == 1) {
        return response()->json(['errors' => ['identifier' => ['Your account is disabled. Please contact support.']]], 403);
    }

    // Attempt to log in
    $credentials = $this->credentials($request);

    if (Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login successful'], 200);
    }

    return response()->json(['errors' => ['password' => ['Invalid credentials']]], 422);
}


    /**
     * Override the credentials method to support both email and username.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $identifier = $request->input('identifier');

        // Determine if the identifier is an email or a username
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // $user = User::where($field, $identifier)->first();

        // if (!$user) {
        //     return response()->json(['errors' => ['identifier' => ['Invalid credentials']]], 422);
        // } else {

        //     if ($user->email_verified_at === null) {
        //         $user->sendEmailVerificationNotification();
        //     }
        // }

        return [
            $field => $identifier,
            'password' => $request->input('password'),
        ];
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */

    protected function authenticated(Request $request, $user)
    {
          
        $user->isLogin = 1;
        $user->save();
        
        $today = Carbon::today();

        $rewards = SubscriptionExpiration::with('subscriptions')
            ->whereHas('subscriptions', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'Approved');
            })
            ->where('status', 'To-Claim')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();

        foreach ($rewards as $reward) {
            if ($today->eq(Carbon::parse($reward->expiration_date))) {

                Subscription::where('id', $reward->subscription_id)
                    ->update(['status' => 'Expired']);

                SubscriptionExpiration::where('id', $reward->id)
                    ->update(['status' => 'Expired']);
            }
        }

        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && $user->email_verified_at === null) {
            $user->sendEmailVerificationNotification();
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        $finduser = User::where('id', $user->id)->first();

        if ($finduser) {
            $finduser->isLogin = 0;
            $finduser->save();
        }

        // Perform the default logout process
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }

    public function username()
    {
        return 'identifier';
    }
}
