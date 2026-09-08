<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    Laravel\Sanctum\SanctumServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
];
