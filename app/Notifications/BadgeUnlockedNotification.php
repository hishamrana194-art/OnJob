<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BadgeUnlockedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $badgeType,
        public string $badgeName,
        public ?string $badgeDescription = null,
        public ?string $courseTitle = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $locale = $notifiable->locale ?? app()->getLocale() ?? 'ar';

        // Simple localized copy (UI already supports Arabic/English).
        $title = $locale === 'ar' ? '🎉 حصلت على شارة جديدة' : '🎉 New badge unlocked';
        // Avoid PHP string interpolation syntax errors.
        $body = $this->badgeName;
        if (!empty($this->courseTitle)) {
            $body = $locale === 'ar'
                ? ($this->badgeName . ' • ضمن مسار: ' . $this->courseTitle)
                : ($this->badgeName . ' • in path: ' . $this->courseTitle);
        }

        return [
            'type' => 'badge',
            'badge_type' => $this->badgeType,
            'title' => $title,
            'body' => $body,
            'description' => $this->badgeDescription,
            'course_title' => $this->courseTitle,
        ];
    }
}
