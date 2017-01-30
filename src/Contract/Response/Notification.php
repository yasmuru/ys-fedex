<?php

namespace yasmuru\LaravelFedEx\Contract\Response;

interface Notification
{
    public function getSeverity(): string;
    public function getSource(): string;
    public function getCode(): int;
    public function getMessage(): string;
}