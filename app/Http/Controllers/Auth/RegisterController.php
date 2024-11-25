<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Rules\NoSpecialCharacters;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'barangay' => ['required'],
            'age' => ['required','integer'],
            'gender' => ['required','string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'username' => ['required', 'string', 'max:255', 'unique:users', new NoSpecialCharacters],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'address' =>  'CEBU CITY, ' . $data['barangay'],
            'email' => $data['email'],
            'age' => $data['age'],
            'gender' => $data['gender'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function showRegistrationForm()
    {
        $page = 'Sign Up';

        return view('auth.register', compact('page'));
    }

    public function getMunicipalities()
    {
        // Load the JSON file
        $json = File::get(public_path('assets/back/cebu-city.json'));
        $data = json_decode($json, true);

        // Extract the municipalities
        $municipalities = array_keys($data['CEBU']['municipality_list']);

        return response()->json(['municipalities' => $municipalities]);
    }

  
}
