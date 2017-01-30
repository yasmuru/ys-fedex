<?php

namespace yasmuru\LaravelFedEx\Response;

use yasmuru\LaravelFedEx\Response;
use yasmuru\LaravelFedEx\Request\Contract as RequestContract;
use yasmuru\LaravelFedEx\Response\Contract as ResponseContract;

class ValidateAddress extends Response
{
    public $address_classification;
    public $is_valid = 1;
    public $validation_messages = [];

    public function parse($response, RequestContract $request): ResponseContract
    {
        parent::parse($response, $request);

        $this->address_classification = strtolower($response->AddressResults->Classification);

        foreach($response->AddressResults->Attributes as $attribute)
            $this->processValidationAttribute($attribute->Name, ($attribute->Value == 'true'));

        return $this;
    }

    public function processValidationAttribute(string $key, bool $value)
    {
        switch($key)
        {
            case 'CountrySupported':
                if($value !== true) $this->invalidate('Could not validate address because country is not supported by the service.');
                break;
            case 'Resolved':
                if($value !== true) $this->invalidate('Could not resolve address.');
                break;
            case 'SuiteRequiredButMissing':
                if($value === true) $this->invalidate('A suite number is required with this address.');
                break;
            case 'InvalidSuiteNumber':
                if($value === true) $this->invalidate('The suite number is invalid.');
                break;
            case 'StreetAddress':
                if($value !== true) $this->invalidate('The street address could not be validated');
                break;
            case 'DPV':
                if($value !== true) $this->invalidate('The address is not a valid for delivery.');
                break;
            case 'PostalValidated':
                if($value !== true) $this->invalidate('Could not validate post code against with address data.');
                break;
        }
    }

    public function invalidate(string $message)
    {
        $this->is_valid = false;
        $this->validation_messages[] = $message;
    }

    public function isValid(): bool
    {
        return $this->is_valid;
    }

    public function getValidationMessages(): array
    {
        return $this->validation_messages;
    }
}