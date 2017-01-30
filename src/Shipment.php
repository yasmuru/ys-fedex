<?php

namespace yasmuru\LaravelFedEx;

use yasmuru\LaravelFedEx\Contract\Shipment as Contract;
use yasmuru\LaravelFedEx\Contract\Shipment\Item;
use yasmuru\LaravelFedEx\Contract\Address as AddressContract;


class Shipment implements Contract
{
    public $items = [],
           $totalWeight = 0.00,
           $totalPrice = 0.00,
           $weightUnits = 'LB',
           $packageCount = 1,
           $destination;

    public function __construct(AddressContract $destination, array $items = [])
    {
        $this->destination = $destination;
        $this->addItems($items);
    }

    public function addItems(array $items): Shipment
    {
        foreach($items as $item)
            $this->addItem($item);

        return $this;
    }

    public function addItem(Item $item): Shipment
    {
        $this->items[] = $item;
        $this->totalWeight += $item->getWeight();
        $this->totalPrice += $item->getPrice();

        return $this;
    }

    public function getDestination(): AddressContract
    {
        return $this->destination;
    }

    public function getTotalWeight(): float
    {
        return $this->totalWeight;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getWeightUnits(): string
    {
        return $this->weightUnits;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getPackageCount(): int
    {
        return $this->packageCount;
    }
}