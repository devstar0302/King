<?php

return [
    'custom' => [
        'name' => [
            'required' => 'חסר שם',
        ],
        'email' => [
            'required' => 'חסר מייל',
            'unique' => 'מייל כבר קיים במערכת',
            'email' => 'נא להזין מייל'
        ],
        'password' => [
            'required' => 'חסרה סיסמה',
        ]
    ],
];
