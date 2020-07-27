<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Factory\AdvertiserFactory;
use AppBundle\Service\Advertisers\AdvertiserAbstract;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AvailabilityControllerTest
 * @package AppBundle\Controller\Api\v1
 */
class AvailabilityControllerTest extends WebTestCase
{
    /**
     * @dataProvider availabilitiesActionProvider
     *
     * @param array $availabilities1
     * @param array $availabilities2
     * @param array $expected
     */
    public function testAvailabilitiesAction(array  $availabilities1, array $availabilities2, array $expected)
    {
        $advertiser1 = $this->getMockBuilder(AdvertiserAbstract::class)
            ->setMethods(['getAvailabilities'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $advertiser1->expects($this->once())->method('getAvailabilities')
            ->willReturn($availabilities1);

        $advertiser2 = $this->getMockBuilder(AdvertiserAbstract::class)
            ->setMethods(['getAvailabilities'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $advertiser2->expects($this->once())->method('getAvailabilities')
            ->willReturn($availabilities2);

        $factory = $this->getMockBuilder(AdvertiserFactory::class)
            ->setMethods(['getAdvertisers'])
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects($this->once())->method('getAdvertisers')->willReturn([$advertiser1, $advertiser2]);

        $client = static::createClient();
        $client->getContainer()->set(AdvertiserFactory::class, $factory);

        $client->request('GET', '/api/v1/availabilities');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expected), $response->getContent());
    }

    /**
     * @dataProvider availabilitiesActionWithExceptionProvider
     *
     * @param array $availabilities1
     * @param array $expected
     */
    public function testAvailabilitiesActionWithException(array  $availabilities1, array $expected)
    {
        $advertiser1 = $this->getMockBuilder(AdvertiserAbstract::class)
            ->setMethods(['getAvailabilities'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $advertiser1->expects($this->once())->method('getAvailabilities')
            ->willReturn($availabilities1);

        $advertiser2 = $this->getMockBuilder(AdvertiserAbstract::class)
            ->setMethods(['getAvailabilities', 'getName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $advertiser2->expects($this->once())->method('getAvailabilities')
            ->willThrowException(new \Exception('Internal exception'));
        $advertiser2->expects($this->once())->method('getName')
            ->willReturn('Advertiser ABC');

        $factory = $this->getMockBuilder(AdvertiserFactory::class)
            ->setMethods(['getAdvertisers'])
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects($this->once())->method('getAdvertisers')->willReturn([$advertiser1, $advertiser2]);

        $client = static::createClient();
        $client->getContainer()->set(AdvertiserFactory::class, $factory);

        $client->request('GET', '/api/v1/availabilities');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode($expected), $response->getContent());
    }

    /**
     * @return array
     */
    public function availabilitiesActionProvider(): array
    {
        return [
            [
                'availabilities1' => [
                    'hotel A' => [
                        'name' => 'hotel A',
                        'stars' => 4,
                        'rooms' => [
                            [
                                'code' => 'code1',
                                'total' => 16,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 5,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code3',
                                'total' => 10.1,
                                'net_price' => 9,
                                'taxes' => [
                                    'amount' => 1.1,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code4',
                                'total' => 11.9,
                                'net_price' => 10,
                                'taxes' => [
                                    'amount' => 1.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                        ],
                    ],
                ],
                'availabilities2' => [
                    'hotel A' => [
                        'name' => 'hotel A',
                        'stars' => 4,
                        'rooms' => [
                            [
                                'code' => 'code1',
                                'total' => 13,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 2,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code4',
                                'total' => 10.9,
                                'net_price' => 10,
                                'taxes' => [
                                    'amount' => 0.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code5',
                                'total' => 11.9,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 0.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    [
                        'name' => 'hotel A',
                        'stars' => 4,
                        'rooms' => [
                            [
                                'code' => 'code3',
                                'total' => 10.1,
                                'net_price' => 9,
                                'taxes' => [
                                    'amount' => 1.1,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code4',
                                'total' => 10.9,
                                'net_price' => 10,
                                'taxes' => [
                                    'amount' => 0.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code5',
                                'total' => 11.9,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 0.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code1',
                                'total' => 13,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 2,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                        ],
                        'cheapestPrice' => 10.1,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function availabilitiesActionWithExceptionProvider(): array
    {
        return [
            [
                'availabilities1' => [
                    'hotel A' => [
                        'name' => 'hotel A',
                        'stars' => 4,
                        'rooms' => [
                            [
                                'code' => 'code1',
                                'total' => 16,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 5,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code3',
                                'total' => 10.1,
                                'net_price' => 9,
                                'taxes' => [
                                    'amount' => 1.1,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code4',
                                'total' => 11.9,
                                'net_price' => 10,
                                'taxes' => [
                                    'amount' => 1.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                        ],
                    ],
                ],
                'expected' => [
                    [
                        'name' => 'hotel A',
                        'stars' => 4,
                        'rooms' => [
                            [
                                'code' => 'code3',
                                'total' => 10.1,
                                'net_price' => 9,
                                'taxes' => [
                                    'amount' => 1.1,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code4',
                                'total' => 11.9,
                                'net_price' => 10,
                                'taxes' => [
                                    'amount' => 1.9,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                            [
                                'code' => 'code1',
                                'total' => 16,
                                'net_price' => 11,
                                'taxes' => [
                                    'amount' => 5,
                                    'currency' => 'EUR',
                                    'type' => 'TAXESANDFEES',
                                ],
                            ],
                        ],
                        'cheapestPrice' => 10.1,
                    ],
                ]
            ],
        ];
    }
}
