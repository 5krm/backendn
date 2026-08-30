<?php

namespace App\Traits;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\App;

trait HasDuration
{
    protected bool $only_en = false;

    /**
     * @return Attribute<string, never>
     */
    protected function textDuration(): Attribute
    {
        $arabic = App::isLocale('ar') && !$this->only_en;
        return Attribute::make(
            get: fn() => $this->formatDuration($arabic)
        );
    }

    private function formatDuration($arabic): string
    {
        if ($this->duration == 0)
            return "🗕";

        $formatted = '';
        $interval = CarbonInterval::minutes($this->duration)->cascade();
        $hours = (int)$interval->totalHours;
        if ($hours > 0) {
            $interval = $interval->subHours($hours);
            $formatted .=  $hours . ($arabic ? 'س ' : 'h ');
        }

        $minutes = (int)$interval->totalMinutes;
        if ($minutes > 0) {
            $interval = $interval->subMinutes($minutes);
            $formatted .= $minutes . ($arabic ? 'د ' : 'm ');
        }

        $seconds = (int)$interval->totalSeconds;
        if ($seconds > 0) {
            $interval = $interval->subSeconds($seconds);
            $formatted .= $seconds . ($arabic ? 'ث ' : 's ');
        }

        return $formatted;
    }
}
