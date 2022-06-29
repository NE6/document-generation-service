<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class ValidPath implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (!ctype_print($value) || !str_ends_with($value, '/')) {
            $fail('The :attribute is not a valid filepath.');
        }
    }
}
