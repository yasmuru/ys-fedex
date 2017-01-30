<?php

namespace yasmuru\LaravelFedEx\Contract\Shipment;

interface Item
{
    public function getId(): string;
    public function getNumberOfPieces(): int;
    public function getQtyOrdered(): int;
    public function getDescription(): string;
    public function getCountryOfManufacture(): string;
    public function getWeight(): float;
    public function getWeightUnits(): float;
    public function getPrice(): float;
    public function getHtsCode(): string;
    public function getTaxes(): array;
}