<?php

return [
    'default' => env('PAYMENT_PROVIDER', 'garanti'),

    'providers' => [
        'garanti' => [
            'name' => 'Garanti',
            'description' => 'Garanti BBVA Payment Provider',
            'service_url' => env('GARANTI_SERVICE_URL'),
            'callback_url' => config('app.url') . '/api/payment/callback',
        ],
    ],

    'md_status_messages' => [
        1 => 'Tam doğrulama',
        2 => 'Kart sahibi veya bankası sisteme kayıtlı değil',
        3 => 'Kartın bankası sisteme kayıtlı değil',
        4 => 'Doğrulama denemesi, kart sahibi sisteme daha sonra kayıt olmayı seçmiş',
        5 => 'Doğrulama yapılamıyor',
        6 => '3D Secure hatası',
        7 => 'Sistem hatası',
        8 => 'Bilinmeyen kart no',
        0 => '3D Secure imzası geçersiz, doğrulama yapılamıyor, SMS şifresi yanlış veya kullanıcı iptal',
    ]
];
