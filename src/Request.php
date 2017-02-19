<?php

namespace yasmuru\LaravelFedEx;

\ini_set("soap.wsdl_cache_enabled", "0");

use yasmuru\LaravelFedEx\Request\Contract as RequestContract;
use yasmuru\LaravelFedEx\Response\Contract as ResponseContract;
use SoapClient;

abstract class Request implements RequestContract
{
    protected $_version;
    protected $_serviceId;
    protected $_wsdl;
    protected $_soapMethod;
    protected $_soapInstance;

    public $data = [];

    protected $_credentials = [
        'WebAuthenticationDetail' => [
            'UserCredential' => [
                'Key'      => null,
                'Password' => null,
            ]
        ],
        'ClientDetail'   => [
            'AccountNumber' => null,
            'MeterNumber'   => null,
        ],
        'Version' => [
            'ServiceId'    => null,
            'Major'        => null,
            'Intermediate' => null,
            'Minor'        => null,
        ]
    ];

    public function setCredentials(string $key, string $password, string $accountNumber, string $meterNumber)
    {
        $this->_credentials['WebAuthenticationDetail']['UserCredential']['Key'] = $key;
        $this->_credentials['WebAuthenticationDetail']['UserCredential']['Password'] = $password;
        $this->_credentials['ClientDetail']['AccountNumber'] = $accountNumber;
        $this->_credentials['ClientDetail']['MeterNumber'] = $meterNumber;

        $this->_credentials['Version']['ServiceId'] = $this->_serviceId;

        list($major, $intermediate, $minor) = explode('.', $this->_version);
        $this->_credentials['Version']['Major'] = $major;
        $this->_credentials['Version']['Intermediate'] = $intermediate;
        $this->_credentials['Version']['Minor'] = $minor;

        return $this;
    }

    public function send(ResponseContract $response_parser = null): ResponseContract
    {
        $soap = $this->getSoap();

        $method = $this->_soapMethod;
        $data = array_merge($this->_credentials, $this->data);

        $result = $soap->$method($data);

        if(!$response_parser)
            $response_parser = new Response;

        return $response_parser->parse($result, $this);
    }

    public function getSoap(): SoapClient
    {
        if(!($this->_soapInstance instanceof SoapClient)) {
            if(env('FEDEX_TEST', 'FALSE') == 'TRUE') {
                $location = 'https://wsbeta.fedex.com:443/web-services/track';
            } else {
                $location = 'https://ws.fedex.com:443/web-services/track';
            }
            $this->_soapInstance = new SoapClient($this->_getWsdl(), ['trace' => 1, 'location' => $location]);
        }
        return $this->_soapInstance;
    }

    protected function _getWsdl(): string
    {
        return dirname(__FILE__).'/wsdl/'.$this->_wsdl;
    }

}