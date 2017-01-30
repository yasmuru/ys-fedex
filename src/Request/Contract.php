<?php

namespace yasmuru\LaravelFedEx\Request;

use yasmuru\LaravelFedEx\Response\Contract as Response;
use SoapClient;

interface Contract
{
    public function setCredentials(string $key, string $password, string $accountNumber, string $meterNumber);
    public function send(Response $response_parser = null);
    public function getSoap(): SoapClient;
}