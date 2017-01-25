<?php

namespace yasmuru\LaravelFedEx\Response\Shipment;

use yasmuru\LaravelFedEx\Response;
use yasmuru\LaravelFedEx\Request\Shipment\CustomsAndDuties as Request;
use yasmuru\LaravelFedEx\Request\Contract as RequestContract;
use yasmuru\LaravelFedEx\Response\Contract as ResponseContract;

class CustomsAndDuties extends Response
{
    protected $_items = [];
    protected $_totalDuties = 0.00;

    public function __construct($items = [])
    {
        $this->_items = $items;
    }

    public function parse($response, RequestContract $request): ResponseContract
    {
        parent::parse($response, $request);

        $check_types = ['INTERNATIONAL_PRIORITY', 'INTERNATIONAL_ECONOMY'];

        foreach($response->RateReplyDetails as $shipping_method)
        {
            if(!in_array($shipping_method->ServiceType, $check_types) || $this->_totalDuties > 0) continue;
            foreach($shipping_method->RatedShipmentDetails as $detail)
            {
                if($this->_totalDuties > 0 ||
                    !isset($detail->ShipmentRateDetail) ||
                    !isset($detail->ShipmentRateDetail->TotalDutiesAndTaxes) ||
                    !isset($detail->ShipmentRateDetail->DutiesAndTaxes)) continue;

                $this->_totalDuties = floatval($detail->ShipmentRateDetail->TotalDutiesAndTaxes->Amount);

                $item_duties = !is_array($detail->ShipmentRateDetail->DutiesAndTaxes) ? [$detail->ShipmentRateDetail->DutiesAndTaxes] : $detail->ShipmentRateDetail->DutiesAndTaxes;
                $this->parseItemDuties($item_duties);
            }
        }

        return $this;
    }

    public function parseItemDuties($items = [])
    {
        foreach($items as $i => $details)
        {
            foreach($details->Taxes as $j => $tax)
            {
                $parsed = [
                    'tax_type'       => $tax->TaxType,
                    'description'    => $tax->Description,
                    'formula'        => $tax->Formula,
                    'effective_date' => $tax->EffectiveDate,
                    'name'           => $tax->Name,
                    'taxable_value'  => floatval($tax->TaxableValue->Amount),
                    'tax_value'      => floatval($tax->Amount->Amount),
                ];

                $this->_items[$i]->taxes[] = $parsed;
            }
        }
    }

    public function getItems(): array
    {
        return $this->_items;
    }

    public function getTotalCustomsAndDuties(): float
    {
        return $this->_totalDuties;
    }

}