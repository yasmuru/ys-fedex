<?php

namespace yasmuru\LaravelFedEx;

use yasmuru\LaravelFedEx\Response\Contract as ResponseContract;
use yasmuru\LaravelFedEx\Request\Contract as RequestContract;
use yasmuru\LaravelFedEx\Response\Mixin\NotificationParser;

class Response implements ResponseContract
{
    use NotificationParser;

    protected $_raw;
    protected $_request;

    public function parse($response, RequestContract $request): ResponseContract
    {
        $this->_raw = $response;
        $this->_request = $request;

        $this->parseNotifications($response);

        return $this;
    }

    public function getRaw()
    {
        return $this->_raw;
    }

    public function getRequest(): RequestContract
    {
        return $this->_request;
    }
}