<?php

namespace AppBundle\Factory;

use AppBundle\Service\Advertisers\AdvertiserInterface;
use AppBundle\Service\Advertisers\Airbnb;
use AppBundle\Service\Advertisers\Booking;
use AppBundle\Service\HTTPClient;

/**
 * Class Advertiser
 * @package AppBundle\Factory
 */
class AdvertiserFactory
{
    const TYPE_API_CONNECTOR = 'api';
    const ADVERTISERS = [
        self::TYPE_API_CONNECTOR => [
            Booking::class,
            Airbnb::class,
        ],
    ];

    /**
     * @var HTTPClient
     */
    private $httpClient;

    /**
     * Advertiser constructor.
     * @param HTTPClient $httpClient
     */
    public function __construct(HTTPClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return AdvertiserInterface[]
     */
    public function getAdvertisers(): array
    {
        static $cache;

        if (empty($cache)) {
            foreach (self::ADVERTISERS as $type => $classes) {
                switch ($type) {
                    case self::TYPE_API_CONNECTOR:
                        foreach ($classes as $class) {
                            $cache[$class] = new $class($this->httpClient);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        return $cache;
    }
}
