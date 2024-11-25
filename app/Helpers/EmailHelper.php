<?php

namespace App\Helpers;

class EmailHelper
{
    public static function maskEmail($email)
    {
        // Split the email into username and domain parts
        $parts = explode('@', $email);
        $username = $parts[0];
        $domain = $parts[1];

        // Ensure we have at least 4 characters to mask
        $usernameLength = strlen($username);

        // If username is longer than 4 characters, mask with exactly 4 asterisks
        if ($usernameLength > 4) {
            $maskedUsername = str_repeat('*', 4) . substr($username, -($usernameLength - 4));
        } else {
            // If the username is 4 or fewer characters, mask all but the last 1-2 characters
            $maskedUsername = str_repeat('*', max($usernameLength - 1, 0)) . substr($username, -1);
        }

        // Return the masked email
        return $maskedUsername . '@' . $domain;
    }
}
