<?php

declare(strict_types=1);

namespace Shared\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class UserIdMatchesAuth implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if ($value === auth()->user()?->id) {
            return;
        }

        $fail('The :attribute must match the authenticated user ID.');
    }
}
