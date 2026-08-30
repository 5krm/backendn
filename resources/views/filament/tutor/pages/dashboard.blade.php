<x-filament-panels::page>
    {{-- Stats Overview - Full Width --}}
    <div class="dashboard-stats">
        @livewire(\App\Filament\Tutor\Widgets\TutorStatsOverview::class)
    </div>

    {{-- Two Column Layout --}}
    <div class="dashboard-grid gap-y-3 custom-widget-spacing">
        {{-- Left Column --}}
        <div class="dashboard-column custom-widget-spacing">
            @livewire(\App\Filament\Tutor\Widgets\UpcomingMilestonesWidget::class)
            @livewire(\App\Filament\Tutor\Widgets\TopCoursesWidget::class)
        </div>

        {{-- Right Column --}}
        <div class="dashboard-column custom-widget-spacing">
            @livewire(\App\Filament\Tutor\Widgets\RecentNotificationsWidget::class)
            @livewire(\App\Filament\Tutor\Widgets\RecentCommentsWidget::class)
        </div>
    </div>
</x-filament-panels::page>
