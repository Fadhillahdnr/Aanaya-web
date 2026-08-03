<?php

$defaultEmailDomains = [
    'gmail.com',
    'googlemail.com',
    'yahoo.com',
    'yahoo.co.id',
    'ymail.com',
    'outlook.com',
    'hotmail.com',
    'live.com',
    'msn.com',
    'icloud.com',
    'me.com',
    'mac.com',
    'proton.me',
    'protonmail.com',
    'aol.com',
    'zoho.com',
    'zohomail.com',
];

return [
    'allowed_email_domains' => array_values(array_unique(array_filter(array_map(
        static fn (string $domain): string => strtolower(trim($domain)),
        explode(',', env('AUTH_ALLOWED_EMAIL_DOMAINS', implode(',', $defaultEmailDomains)))
    )))),
];
