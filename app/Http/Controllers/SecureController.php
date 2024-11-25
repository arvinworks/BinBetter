<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SecureController extends Controller
{
    public function serveJsFile(Request $request, $filename)
    {
        // Define the secure JavaScript file paths
        $jsFiles = [
            'login' => resource_path('function/login.js'),
            'register' => resource_path('function/register.js'),
            'forgot' => resource_path('function/forgotpw.js'),
            'reset' => resource_path('function/resetpw.js'),
            'verify' => resource_path('function/verify.js'),
            'company' => resource_path('function/company.js'),
            'account' => resource_path('function/account.js'),
            'message' => resource_path('function/message.js'),
            'lgu' => resource_path('function/lgu.js'),
            'ngo' => resource_path('function/ngo.js'),
            'resident' => resource_path('function/resident.js'),
            'service' => resource_path('function/service.js'),
            'subscriptionsettings' => resource_path('function/subscriptionsettings.js'),
            'postreport' => resource_path('function/postreport.js'),
            'event' => resource_path('function/event.js'),
            'eventattendance' => resource_path('function/eventattendace.js'),
            'subscription' => resource_path('function/subscription.js'),
            'garbageschedules' => resource_path('function/garbageschedule.js'),
            'managereward' => resource_path('function/managereward.js'),
            'garbagetips' => resource_path('function/garbagetips.js'),
            'gcash' => resource_path('function/gcash.js'),
            'payments' => resource_path('function/payment.js'),
        ];

        if (!array_key_exists($filename, $jsFiles) || !file_exists($jsFiles[$filename])) {
            abort(404); // Handle the case where the file is not found
        }

        $content = file_get_contents($jsFiles[$filename]);
        return response($content)->header('Content-Type', 'application/javascript');
    }
}
