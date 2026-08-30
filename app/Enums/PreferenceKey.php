<?php

namespace App\Enums;

enum PreferenceKey: string
{
    case DisplayLanguage = 'display_language';
    case LearningLanguage = 'learning_language';
    case FollowupEmail = 'followup_email';
    case UpdateEmail = 'update_email';
    case NotificationEmail = 'notification_email';
    case AccountSetupEmail = 'account_setup_email';
}
