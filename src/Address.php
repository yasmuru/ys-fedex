<?php

namespace yasmuru\LaravelFedEx;

use yasmuru\LaravelFedEx\Contract\Address as Contract;

class Address implements Contract
{
    public $street = [],
           $city = '',
           $postCode = '',
           $countryCode = '',
           $stateCode = '';

    public function __construct($source, array $map = [])
    {
        if(!(is_array($source) || is_object($source)))
            throw new \InvalidArgumentException("You must pass an array or object as the source data for an address.");

        foreach($source as $key => $val)
        {
            if(array_key_exists($key, $map))
                $key = $map[$key];

            if(property_exists($this, $key))
                $this->$key = $val;
        }
    }

    public function getStreet(): array
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getStateCode(): string
    {
        return $this->stateCode;
    }
}