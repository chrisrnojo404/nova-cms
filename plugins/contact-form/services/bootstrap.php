<?php

use App\Models\Plugin;
use App\Support\PluginManager;

return function (PluginManager $pluginManager, Plugin $plugin): void {
    $pluginManager->registerAdminNavigationItem([
        'label' => 'Contact Forms',
        'route' => 'admin.plugins.contact-form.index',
        'pattern' => 'admin.plugins.contact-form.*',
        'order' => 80,
    ]);

    $pluginManager->addHook('dashboard.quick-actions', function (mixed $payload): mixed {
        if (! is_array($payload)) {
            return $payload;
        }

        $payload[] = [
            'label' => 'Open contact form plugin',
            'route' => 'admin.plugins.contact-form.index',
        ];

        return $payload;
    });
};
