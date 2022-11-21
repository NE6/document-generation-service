<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPath implements Rule
{
    /**
     * Check if the rule passes
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (! ctype_print($value) || ! str_ends_with($value, '/')) {
            return false;
        }

        return true;
    }

    /**
     * Message on failure
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute is not a valid filepath.';
    }
}
