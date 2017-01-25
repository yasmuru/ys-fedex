<?php

namespace yasmuru\LaravelFedEx\Response\Shipment;

use Carbon\Carbon;
use yasmuru\LaravelFedEx\Request;
use yasmuru\LaravelFedEx\Response;
use yasmuru\LaravelFedEx\Request\Contract as RequestContract;
use yasmuru\LaravelFedEx\Response\Contract as ResponseContract;

class Track extends Response
{
    const STATUS_ERROR = -1;
    const STATUS_READY_TO_SHIP = 0;
    const STATUS_PICKED_UP = 0.1;
    const STATUS_IN_TRANSIT = 1;
    const STATUS_DELIVERED = 2;

    public static $statusCodes = [
        -1 => 'Error',
        0 => 'Ready to Ship',
        0.1 => 'Picked Up',
        1 => 'In Transit',
        2 => 'Delivered'
    ];

    public function __construct()
    {
        $this->raw = null;
        $this->success = null;
        $this->failed = null;
        $this->delivered = null;
        $this->tracking_number = null;
        $this->delivery_address = (object) ['city' => null, 'state' => null, 'postcode' => null];
        $this->ship_date = Carbon::now();
        $this->delivery_date = null;
        $this->_status = 0;
        $this->status = static::$statusCodes[$this->_status];
        $this->events = [];
        $this->last_update = Carbon::now();
    }

    public function isInTransit(): bool
    {
        return $this->_status == static::STATUS_IN_TRANSIT;
    }

    public function isReadyToShip(): bool
    {
        return $this->_status == static::STATUS_READY_TO_SHIP;
    }

    public function isDelivered(): bool
    {
        return $this->_status == static::STATUS_DELIVERED;
    }

    public function parse($response, RequestContract $request): ResponseContract
    {
        parent::parse($response, $request);

        $this->failed = 1;

        if($response->CompletedTrackDetails->TrackDetails->Notification->Severity != 'SUCCESS')
            return $this;

        $detail = $response->CompletedTrackDetails->TrackDetails;
        $this->failed = 0;
        $this->success = 1;
        $this->delivered = 0;
        $this->tracking_number = $detail->TrackingNumber;
        $this->ship_date = Carbon::parse($detail->ShipTimestamp);

        if(isset($detail->ActualDeliveryTimestamp))
        {
            $this->delivered = 1;
            $this->delivery_date = Carbon::parse($detail->ActualDeliveryTimestamp);
        }
        else if(isset($detail->EstimatedDeliveryTimestamp))
        {
            $this->delivery_date = Carbon::parse($detail->EstimatedDeliveryTimestamp);
        }

        $this->delivery_address = (object) ['city'  => $detail->DestinationAddress->City,
                                            'state' => $detail->DestinationAddress->StateOrProvinceCode,
                                            'country_code' => $detail->DestinationAddress->CountryCode,
                                            'country' => $detail->DestinationAddress->CountryName
        ];

        $this->last_update = Carbon::parse($detail->StatusDetail->CreationTime);
        $this->status = $detail->StatusDetail->Description;
        $this->current_location = (object) [
            'city' => $detail->StatusDetail->Location->City,
            'state' => $detail->StatusDetail->Location->StateOrProvinceCode,
            'country_code' =>$detail->StatusDetail->Location->CountryCode,
            'country' => $detail->StatusDetail->Location->CountryName,
        ];

        $events = $detail->Events;

        if(!is_array($events))
            $events = [$events];

        foreach($events as $event)
        {
            $this->events[] = (object) [
                'timestamp' => Carbon::parse($event->Timestamp),
                'description' => $event->EventDescription,
                'has_address' => isset($event->Address->CountryCode),
                'address' => (object) [
                    'city' => @$event->Address->City,
                    'state' => @$event->Address->StateOrProvinceCode,
                    'country_code' => @$event->Address->CountryCode,
                    'country' => @$event->Address->CountryName,
                ],
            ];
        }

        $this->last_update = Carbon::parse($this->events[0]->timestamp);

        if(count($events) == 1)
            $this->_status = static::STATUS_PICKED_UP;

        if(count($events) > 1)
            $this->_status = static::STATUS_IN_TRANSIT;

        if($this->delivered)
            $this->_status = static::STATUS_DELIVERED;

        $this->status = static::$statusCodes[$this->_status];

        return $this;
    }
}