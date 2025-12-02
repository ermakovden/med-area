<?php

declare(strict_types=1);

namespace Shared\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class UserIdMatchesUrlUserId implements ValidationRule
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

        if ($value === request()->route('userId')) {
            return;
        }

        $fail('The :attribute must match in the url user ID.');
    }
}
