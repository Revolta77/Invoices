<?php

$available = array_filter(array_map('trim', explode(',', (string) env('APP_AVAILABLE_LOCALES', 'sk,en'))));

$definitions = [
    'sk' => [
        'name' => 'Slovenčina',
        'flag' => '🇸🇰',
        'flag_icon' => 'images/flags/sk.svg',
        'native' => 'SK',
    ],
    'en' => [
        'name' => 'English',
        'flag' => '🇬🇧',
        'flag_icon' => 'images/flags/gb.svg',
        'native' => 'EN',
    ],
];

return [
    'available' => array_intersect_key($definitions, array_flip($available)),
];
