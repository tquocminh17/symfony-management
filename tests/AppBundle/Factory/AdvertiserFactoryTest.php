<?php

namespace Tests\AppBundle\Factory;

use AppBundle\Factory\AdvertiserFactory;
use AppBundle\Service\HTTPClient;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class Advertiser
 * @package AppBundle\Factory
 */
class AdvertiserFactoryTest extends TestCase
{
    public function testGetAdvertisers()
    {
        $httpClient = $this->getMockBuilder(HTTPClient::class)
            ->setMethods(['request'])
            ->getMock();
        $httpClient->expects($this->never())->method('request');
        $factory = new AdvertiserFactory($httpClient);
        $advertisers = $factory->getAdvertisers();
        $this->assertTrue(count($advertisers) === 2);
    }
}
