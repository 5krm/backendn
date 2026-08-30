<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\TutorPanelProvider;
use App\Providers\HorizonServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Vimeo\Laravel\VimeoServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    TutorPanelProvider::class,
    HorizonServiceProvider::class,
    TelescopeServiceProvider::class,
    VimeoServiceProvider::class,
];
