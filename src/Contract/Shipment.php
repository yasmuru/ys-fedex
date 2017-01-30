<?php

namespace yasmuru\LaravelFedEx\Contract;

use yasmuru\LaravelFedEx\Contract\Address;

interface Shipment
{
    public function getDestination(): Address;
    public function getTotalWeight(): float;
    public function getTotalPrice(): float;
    public function getWeightUnits(): string;
    public function getItems(): array;
    public function getPackageCount(): int;
}