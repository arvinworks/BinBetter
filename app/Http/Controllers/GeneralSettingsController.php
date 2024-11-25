<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CompanySetting;
use App\Http\Controllers\Controller;
use App\Models\JoinEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class GeneralSettingsController extends Controller
{
    public function index()
    {
        $page = 'General Setting';
        $user = User::getCurrentUser();

        $companySetting = CompanySetting::first();
        $accountSetting = User::where('id', $user->id)->first();

        $companySettingArray = $companySetting ? $companySetting->toArray() : [];

        $accountSettingArray = $accountSetting ? $accountSetting->toArray() : [];

        $myJoinEvents = JoinEvent::with(['user','event'])->where('user_id',  $user->id)->get();

        return view('pages.back.v_generalsettings', compact('page', 'companySetting', 'accountSetting', 'myJoinEvents'));
    }

    public function company(Request $request)
    {
        $request->validate([
            'company_email' => 'required|email',
            'company_phone' => ['required', 'regex:/^[\d\s\+\-]*$/'], // Only digits, spaces, plus, and hyphen are allowed
            'company_address' => 'required',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Retrieve the first CompanySetting or create a new one if it doesn't exist
        $companySetting = CompanySetting::firstOrNew();

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = 'assets/uploads/' . $filename;

            if ($companySetting->company_logo && file_exists(public_path($companySetting->company_logo))) {
                unlink(public_path($companySetting->company_logo));
            }

            $file->move(public_path('assets/uploads'), $filename);
            $companySetting->company_logo = $filePath;
        }

        $companySetting->company_email = $request->company_email;
        $companySetting->company_phone = $request->company_phone;
        $companySetting->company_address = $request->company_address;
        $companySetting->save();

        return response()->json([
            'success' => 'Company setting saved successfully',
            'companySetting' => $companySetting
        ]);
    }

    public function profile(Request $request)
    {

        $user = User::getCurrentUser();

        $request->validate([
            'account_profile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);


        $accountSetting = User::where('id', $user->id)->first();

        if ($request->hasFile('account_profile')) {
            $file = $request->file('account_profile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = 'assets/uploads/' . $filename;

            if ($accountSetting->profile && file_exists(public_path($accountSetting->profile))) {
                unlink(public_path($accountSetting->profile));
            }

            $file->move(public_path('assets/uploads'), $filename);

            $accountSetting->profile = $filePath;
            $accountSetting->save();
        }

        return response()->json([
            'success' => 'Account saved successfully',
            'accountSetting' => $accountSetting
        ]);
    }

    public function account(Request $request)
    {

        $user = User::getCurrentUser();

        if(Auth::user()->role === 'LGU' || Auth::user()->role === 'Superadmin' ){

        }

        $rules = [
            'account_username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username,' . $user->id
            ],
            'account_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id
            ],
        ];
        
        if (Auth::user()->role === 'LGU' || Auth::user()->role === 'Superadmin') {
            $rules['account_address'] = [
                'required',
                'string',
                'max:255'
            ];
        }
        
        
        if (Auth::user()->role === 'Resident') {
           $rules = [
            'age' => [
                'required',
                'integer'
            ],
            'gender' => [
                'required',
                'string',
                'max:20'
            ],
        ];
        }
        
        $request->validate($rules);


        $accountSetting = User::where('id', $user->id)->first();

        $accountSetting->username =  $request->account_username;
        $accountSetting->email = $request->account_email;
        $accountSetting->address = $request->account_address;
        $accountSetting->age = $request->age;
        $accountSetting->gender = $request->gender;
        $accountSetting->save();

        return response()->json([
            'success' => 'Account saved successfully',
            'accountSetting' => $accountSetting
        ]);
    }


    public function password(Request $request)
    {

        $user = User::getCurrentUser();

        $request->validate([
            'currentPassword' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Check if the current password matches the stored hash
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'currentNewPassword' => [
                'required',
                'min:8'
            ],
            'confirmNewpassword' => [
                'required',
                'same:currentNewPassword', // Must match the new password
            ],
        ]);

        $accountSetting = User::where('id', $user->id)->first();

        $accountSetting->password = Hash::make($request->input('currentNewPassword'));
        $accountSetting->save();

        return response()->json([
            'success' => 'Account password saved successfully',
            'accountSetting' => $accountSetting
        ]);
    }

    public function removeAccount(Request $request)
    {
        $user = User::getCurrentUser();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $myuser = User::where('id', $user->id)->first();
        $myuser->delete();

        Auth::logout();

        return response()->json([ 'type' => 'success', 'message' => 'Your account has been successfully removed.'], 200);
    }
}
