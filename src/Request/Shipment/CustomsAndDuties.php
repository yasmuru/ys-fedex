<?php
namespace yasmuru\LaravelFedEx\Request\Shipment;
use yasmuru\LaravelFedEx\Request;
use yasmuru\LaravelFedEx\Contract\Address;
use yasmuru\LaravelFedEx\Contract\Shipment;
use yasmuru\LaravelFedEx\Contract\Shipment\Item;
class CustomsAndDuties extends Request
{
    public $_version = '18.0.0';
    public $_serviceId = 'crs';
    public $_wsdl = 'RateService_v18.wsdl';
    public $_soapMethod = 'getRates';
    
    public function __construct(Shipment $shipment, Address $shipper)
    {
        $this->data = [
            'ReturnTransitAndCommit' => true,
            'RequestedShipment' => [
                'EdtRequestType'    => 'ALL',
                'ShipTimestamp'     => date('c'),
                'DropoffType'       => 'REGULAR_PICKUP',
                'RateRequestTypes'  => 'LIST',
                'PreferredCurrency' => 'USD',
                'ShippingChargesPayment' => [
                    'PaymentType' => 'SENDER',
                    'Payor' => [
                        'ResponsibleParty' => [
                            'AccountNumber' => null,
                            'CountryCode' => 'US'
                        ]
                    ]
                ],
                'Shipper' => [
                    'Address' => [
                        'StreetLines'           => $shipper->getStreet(),
                        'City'                  => $shipper->getCity(),
                        'PostalCode'            => $shipper->getPostCode(),
                        'CountryCode'           => $shipper->getCountryCode(),
                        'StateOrProvinceCode'   => $shipper->getStateCode()
                    ]
                ],
                'Recipient' => [
                    'Address' => [
                        'StreetLines'   => $shipment->getDestination()->getStreet(),
                        'City'          => $shipment->getDestination()->getCity(),
                        'PostalCode'    => $shipment->getDestination()->getPostCode(),
                        'CountryCode'   => $shipment->getDestination()->getCountryCode(),
                    ]
                ],
                'CustomsClearanceDetail' => [
                    'DutiesPayment' => [
                        'PaymentType' => 'SENDER',
                        'Payor' => [
                            'ResponsibleParty' => [
                                'AccountNumber' => null,
                                'CountryCode'   => 'US',
                            ]
                        ]
                    ],
                    'DocumentContent' => 'NON_DOCUMENTS',
                    'CustomsValue' => [
                        'Currency' => 'USD',
                        'Amount'   => $shipment->getTotalPrice()
                    ],
                    'Commodities'   => $this->parseShipmentItemsToRequestCommodities($shipment)
                ],
                'RequestedPackageLineItems' => $this->parseShipmentPackageLineItems($shipment),
                'PackageCount'  => $shipment->getPackageCount(),
                'PackageDetail' => 'INDIVIDUAL_PACKAGES',
            ]
        ];
    }
    protected function parseShipmentPackageLineItems(Shipment $shipment): array
    {
        return [ 0 => [
            'SequenceNumber' => 1,
            'GroupPackageCount' => 1,
            'Weight' => [
                'Value' => $shipment->getTotalWeight(),
                'Units' => $shipment->getWeightUnits(),
            ]
        ]];
    }
    protected function parseShipmentItemsToRequestCommodities(Shipment $shipment): array
    {
        $commodities = [];
        foreach($shipment->getItems() as $item)
        {
            $commodities[] = [
                'Name'           => $item->getId(),
                'NumberOfPieces' => $item->getNumberOfPieces(),
                'Description'    => $item->getDescription(),
                'CountryOfManufacture'  => $item->getCountryOfManufacture(),
                'Weight' => [
                    'Units' => $shipment->getWeightUnits(),
                    'Value' => $item->getWeight(),
                ],
                'Quantity' => $item->getQtyOrdered(),
                'QuantityUnits' => 'EA',
                'UnitPrice' => [
                    'Currency' => 'USD',
                    'Amount'   => $item->getPrice(),
                ],
                'CustomsValue' => [
                    'Currency' => 'USD',
                    'Amount' => $item->getPrice(),
                ],
                'HarmonizedCode' => $item->getHTSCode()
            ];
        }
        return $commodities;
    }
    public function setCredentials(string $key, string $password, string $accountNumber, string $meterNumber)
    {
        $this->_data['RequestedShipment']['ShippingChargesPayment']['Payor']['ResponsibleParty']['AccountNumber'] = $accountNumber;
        $this->_data['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']['AccountNumber'] = $accountNumber;
        return parent::setCredentials($key, $password, $accountNumber, $meterNumber);
    }
}
