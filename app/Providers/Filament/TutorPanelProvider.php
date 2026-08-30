<?php

namespace App\Providers\Filament;

use App\Filament\Tutor\Pages\Dashboard;
use App\Filament\Tutor\Widgets\RecentCommentsWidget;
use App\Filament\Tutor\Widgets\RecentNotificationsWidget;
use App\Filament\Tutor\Widgets\TutorStatsOverview;
use App\Http\Middleware\EnsureTutor;
use App\Http\Middleware\SetLocale;
use App\Providers\Filament\Navigation\UserMenuActions;
use EslamRedaDiv\TimezoneDetector\TimezoneDetectorPlugin;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TutorPanelProvider extends PanelProvider
{
    public function boot(): void {}

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('tutor')
            ->path('tutor')
            ->login()
            ->brandName('Tutor Panel')
            ->brandLogo(asset('assets/svg\logo\ngo-academy-logo-en.svg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('ngo-academy-icon.svg'))
            ->darkMode(false)
            ->defaultAvatarProvider(UiAvatarsProvider::class)
            ->globalSearch(false)
            ->plugin(TimezoneDetectorPlugin::make())
            ->colors([
                'primary' => '#00cc99',
                'secondary' => '#000033',
                'accent' => '#e9f3ff',
            ])
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                NavigationGroup::make('Content Management')
                    ->label(fn () => __('tutor.nav.content_management'))
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible(),
                NavigationGroup::make('Reports and Analytics')
                    ->label(fn () => __('tutor.nav.reports_analytics'))
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible(),
                NavigationGroup::make('Settings')
                    ->label(fn () => __('tutor.nav.settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),
            ])
            ->discoverResources(in: app_path('Filament/Tutor/Resources'), for: 'App\Filament\Tutor\Resources')
            ->discoverPages(in: app_path('Filament/Tutor/Pages'), for: 'App\Filament\Tutor\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Tutor/Widgets'), for: 'App\Filament\Tutor\Widgets')
            ->widgets([
                TutorStatsOverview::class,
                RecentNotificationsWidget::class,
                RecentCommentsWidget::class,
                AccountWidget::class,
            ])
            ->databaseNotificationsPolling('30s')
            ->renderHook(
                'panels::global-search.after',
                fn () => view('filament.tutor.components.impersonation-banner')
            )
            ->renderHook(
                'panels::global-search.after',
                fn () => view('filament.tutor.components.language-switcher')
            )
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.tutor.components.dashboard-styles')
            )
            ->authMiddleware([
                Authenticate::class,
                EnsureTutor::class,
            ])
            ->authGuard('web')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->userMenuItems(UserMenuActions::getItems());
    }
}
