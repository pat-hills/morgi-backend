<?php

return [

    'morgi' => [
        'CLIENT_ACCNUM' => env("MORGI_CLIENT_ACCNUM"),
        'CLIENT_SUBACC' => env("MORGI_CLIENT_SUBACC"),
        'CCBILL_SALT' => env("MORGI_CCBILL_SALT"),
        'CCBILL_FORM_NAME' => env("MORGI_CCBILL_FORM_NAME"),
        'CCBILL_USERNAME' => env("MORGI_CCBILL_USERNAME"),
        'CCBILL_PASSWORD' => env("MORGI_CCBILL_PASSWORD")
    ],

    'micromorgi' => [
        'CLIENT_ACCNUM' => env("MICROMORGI_CLIENT_ACCNUM"),
        'CLIENT_SUBACC' => env("MICROMORGI_CLIENT_SUBACC"),
        'CCBILL_SALT' => env("MICROMORGI_CCBILL_SALT"),
        'CCBILL_FORM_NAME' => env("MICROMORGI_CCBILL_FORM_NAME"),
        'CCBILL_USERNAME' => env("MICROMORGI_CCBILL_USERNAME"),
        'CCBILL_PASSWORD' => env("MICROMORGI_CCBILL_PASSWORD")
    ],

    'credit_card' => [
        'CLIENT_ACCNUM' => env("CREDIT_CARD_CLIENT_ACCNUM") ?? env("MICROMORGI_CLIENT_ACCNUM"),
        'CLIENT_SUBACC' => env("CREDIT_CARD_CLIENT_SUBACC") ?? env("MICROMORGI_CLIENT_SUBACC"),
        'CCBILL_SALT' => env("CREDIT_CARD_CCBILL_SALT") ?? env("MICROMORGI_CCBILL_SALT"),
        'CCBILL_FORM_NAME' => env("CREDIT_CARD_CCBILL_FORM_NAME") ?? env("MICROMORGI_CCBILL_FORM_NAME"),
        'CCBILL_USERNAME' => env("CREDIT_CARD_CCBILL_USERNAME") ?? env("MICROMORGI_CCBILL_USERNAME"),
        'CCBILL_PASSWORD' => env("CREDIT_CARD_CCBILL_PASSWORD") ?? env("MICROMORGI_CCBILL_PASSWORD")
    ]

];
