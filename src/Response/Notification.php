<?php

namespace yasmuru\LaravelFedEx\Response;

use yasmuru\LaravelFedEx\Contract\Response\Notification as NotificationContract;

class Notification implements NotificationContract
{
    protected $severity = '',
              $source = '',
              $code = 0,
              $message = '',
              $message_parameters = [];

    public function __construct(string $severity, string $source, int $code, string $message)
    {
        $this->severity = $severity;
        $this->source = $source;
        $this->code = $code;
        $this->message = $message;
    }

    public function getSeverity(): string
    {
        return $this->serverity;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}

