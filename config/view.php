<?php

use App\Providers\LivewireShellServiceProvider;
use Illuminate\Foundation\Bootstrap\RegisterProviders;

$servicesCache = 'storage/framework/cache/services.php';

if (! is_dir(base_path('storage/framework/cache'))) {
    mkdir(base_path('storage/framework/cache'), 0755, true);
}

putenv('APP_SERVICES_CACHE='.$servicesCache);
$_ENV['APP_SERVICES_CACHE'] = $servicesCache;
$_SERVER['APP_SERVICES_CACHE'] = $servicesCache;

RegisterProviders::merge([
    LivewireShellServiceProvider::class,
]);

return [

  'paths' => [
    resource_path('views/overrides'),
    resource_path('views'),
  ],

  'compiled' => env(
    'VIEW_COMPILED_PATH',
    realpath(storage_path('framework/views'))
  ),

];
