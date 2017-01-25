<?php

namespace yasmuru\LaravelFedEx\Request\Shipment;

use yasmuru\LaravelFedEx\Request;

class Track extends Request
{
    public $_version = '10.0.0';
    public $_serviceId = 'trck';
    public $_wsdl = 'TrackService_v10.wsdl';
    public $_soapMethod = 'track';

    public function __construct(int $tracking_number)
    {
        $this->data = [
            'SelectionDetails' => [
                'PackageIdentifier' => [
                    'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
                    'Value' => $tracking_number
                ]
            ],
            'ProcessingOptions' => 'INCLUDE_DETAILED_SCANS'
        ];
    }

}