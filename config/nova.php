<?php

return [
    'name' => env('APP_NAME', 'Nova CMS'),
    'admin_path' => env('NOVA_ADMIN_PATH', 'admin'),
    'panel_roles' => [
        'super-admin',
        'admin',
        'editor',
        'author',
    ],
    'default_theme' => 'default',
    'default_plugin_state' => false,
];
