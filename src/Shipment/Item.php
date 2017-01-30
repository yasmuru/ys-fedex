<?php

namespace yasmuru\LaravelFedEx\Shipment;

use yasmuru\LaravelFedEx\Contract\Shipment\Item as Contract;

class Item implements Contract
{
    public $id = '',
           $description = '',
           $price = 0.00,
           $qtyOrdered = 1,
           $weight = 0.00,
           $weightUnits = 'LB',
           $numberOfPieces = 1,
           $countryOfManufacture = 'US',
           $htsCode = '',
           $taxes = [];

    public function __construct($source, array $map = [])
    {
        if(!(is_array($source) || is_object($source)))
            throw new \InvalidArgumentException("You must pass an array or object as the source data for a shipment item.");

        foreach($source as $key => $val)
        {
            if(array_key_exists($key, $map))
                $key = $map[$key];

            if(property_exists($this, $key))
                $this->$key = $val;
        }
    }
    
    public function getId(): string
    {
        return $this->id;
    }

    public function getNumberOfPieces(): int
    {
        return $this->numberOfPieces;
    }

    public function getQtyOrdered(): int
    {
        return $this->qtyOrdered;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCountryOfManufacture(): string
    {
        return $this->countryOfManufacture;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getWeightUnits(): float
    {
        return $this->weightUnits;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getHtsCode(): string
    {
        return $this->htsCode;
    }

    public function getTaxes(): array
    {
        return $this->taxes;
    }
}