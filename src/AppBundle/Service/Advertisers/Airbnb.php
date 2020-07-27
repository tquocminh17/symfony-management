<?php

namespace AppBundle\Service\Advertisers;

/**
 * Class Airbnb
 * @package AppBundle\Service\Advertisers
 */
class Airbnb extends ApiConnectorAbstract
{
    protected $name = 'Airbnb';
    protected $url = 'https://f704cb9e-bf27-440c-a927-4c8e57e3bad1.mock.pstmn.io/s2/availability';
    protected $method = 'GET';

    /**
     * @inheritdoc
     */
    protected function getHotels(array $response): array
    {
        $hotels = [];
        foreach ($response['hotels'] as $hotel) {
            $rooms = [];
            foreach ($hotel['rooms'] as $room) {
                $rooms[] = [
                    'code' => $room['code'],
                    'net_price' => $room['net_rate'],
                    'taxes' => $room['taxes'],
                    'total' => $room['totalPrice'],
                ];
            }
            $hotel['rooms'] = $rooms;
            $hotels[] = $hotel;
        }

        return $hotels;
    }
}
