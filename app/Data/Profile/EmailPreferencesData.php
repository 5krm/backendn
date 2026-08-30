<?php

namespace App\Data\Profile;

use Spatie\LaravelData\Data;

class EmailPreferencesData extends Data
{
    public function __construct(
        public bool $receiveFollowupEmails = false,
        public bool $receiveNotificationEmails = false,
        public bool $receiveUpdatesEmails = false,
    ) {}
}
