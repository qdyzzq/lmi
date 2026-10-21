<?php

return [
    'url'      => env('SMS_URL', 'https://messagingsuite.smart.com.ph/cgphttp/servlet/sendmsg'),
    'username' => env('SMS_USERNAME'),
    'password' => env('SMS_PASSWORD'),

    // TLS certificate verification for the SMS gateway. Keep this true.
    // If PHP on the server has no CA bundle, point SMS_CA_BUNDLE at a cacert.pem
    // file (https://curl.se/docs/caextract.html) instead of disabling verification.
    'verify'   => env('SMS_CA_BUNDLE') ?: (bool) env('SMS_VERIFY_SSL', true),
];
