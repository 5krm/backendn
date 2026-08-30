<?php

namespace App\Traits;

use App\Enums\FollowupEmailType;
use App\Models\FollowupEmail;
use Carbon\Carbon;

trait HasFollowupEmailCheck
{
    private function getConfig(): array
    {
        $configKey = match ($this->followupEmailType) {
            FollowupEmailType::LessonTracking => 'mail.followup',
            FollowupEmailType::ExamReminder => 'mail.exam-reminder',
        };

        return config($configKey);
    }

    private function isTimeToSend(Carbon $lastActivityDate, ?FollowupEmail $lastEmail, int $sentEmailCount): bool
    {
        $config = $this->getConfig();
        if ($lastActivityDate->diffInDays(now()) < $config['after_days']) {
            return false;
        }

        if ($lastEmail && $lastEmail->sent_at->diffInDays(now()) < $config['interval_days']) {
            return false;
        }

        if ($sentEmailCount >= $config['max_emails']) {
            return false;
        }

        return true;
    }
}
