<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedEmailDomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = strtolower((string) str($value)->afterLast('@'));
        $allowedDomains = config('identity.allowed_email_domains', []);

        if (! in_array($domain, $allowedDomains, true)) {
            $fail('Please use a supported personal email address such as Gmail, Yahoo, Outlook, iCloud, or Proton Mail.');
        }
    }
}
