<?php

namespace yasmuru\LaravelFedEx\Response\Mixin;

use yasmuru\LaravelFedEx\Response\Notification;

trait NotificationParser
{
    public function parseNotifications($response)
    {
        $this->notifications_highest_severity = $response->HighestSeverity;
        $this->notifications = [];

        $notifications = is_array($response->Notifications) ? $response->Notifications : [$response->Notifications];

        foreach($notifications as $note)
        {
            $notification = new Notification($note->Severity, $note->Source, $note->Code, $note->Message);
            $this->notifications[] = $notification;
        }

        return $this;
    }

    public function getNotifications(): array
    {
        return $this->notifications;
    }

    public function getNotificationHighestSeverity(): string
    {
        return $this->notifications_highest_severity;
    }
}