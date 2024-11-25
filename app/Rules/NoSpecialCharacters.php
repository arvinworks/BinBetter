<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoSpecialCharacters implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Only allow alphanumeric characters and underscores
        return !preg_match('/[^a-zA-Z0-9_]/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute may only contain letters, numbers, and underscores.';
    }
}
