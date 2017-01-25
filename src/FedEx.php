<?php

namespace yasmuru\LaravelFedEx;

use yasmuru\LaravelFedEx\Contract\Address;
use yasmuru\LaravelFedEx\Contract\Shipment;
use yasmuru\LaravelFedEx\Response\Contract as ResponseContract;
use yasmuru\LaravelFedEx\Request\ValidateAddress as ValidateAddressRequest;
use yasmuru\LaravelFedEx\Response\ValidateAddress as ValidateAddressResponse;
use yasmuru\LaravelFedEx\Request\Shipment\Track as TrackShipmentRequest;
use yasmuru\LaravelFedEx\Response\Shipment\Track as TrackShipmentResponse;
use yasmuru\LaravelFedEx\Request\Shipment\CustomsAndDuties as CustomsAndDutiesRequest;
use yasmuru\LaravelFedEx\Response\Shipment\CustomsAndDuties as CustomsAndDutiesResponse;

// Facade for FedEx requests
class FedEx
{
    public function __construct() {
        $this->fedex_key = env('FEDEX_KEY');
        $this->fedex_password = env('FEDEX_PASSWORD');
        $this->fedex_account_number = env('FEDEX_ACCOUNT_NUMBER');
        $this->fedex_meter_number = env('FEDEX_METER_NUMBER');
        if(!$this->fedex_key || !$this->fedex_password || !$this->fedex_account_number || !$this->fedex_meter_number) {
            throw new \InvalidArgumentException('Please set FEDEX environment variables.');
        }
    }
    public static function trackShipment(int $tracking_number): ResponseContract
    {
        return (new TrackShipmentRequest($tracking_number))
            ->setCredentials( $this->fedex_key, $this->fedex_password, $this->fedex_account_number, $this->fedex_meter_number)
            ->send(new TrackShipmentResponse);
    }
    
    public static function customsAndDuties(Shipment $shipment, Address $shipper)
    {
        return (new CustomsAndDutiesRequest($shipment, $shipper))
            ->setCredentials( $this->fedex_key, $this->fedex_password, $this->fedex_account_number, $this->fedex_meter_number)
            ->send(new CustomsAndDutiesResponse($shipment->getItems()));
    }

    public static function validateAddress(Address $address)
    {
        return (new ValidateAddressRequest($address))
            ->setCredentials( $this->fedex_key, $this->fedex_password, $this->fedex_account_number, $this->fedex_meter_number)
            ->send(new ValidateAddressResponse);
    }
}