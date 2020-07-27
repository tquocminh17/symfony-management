<?php

namespace Tests\AppBundle\Service\Advertisers;

use AppBundle\Service\Advertisers\ApiConnectorAbstract;
use AppBundle\Service\HTTPClient;
use AppBundle\Service\Advertisers\Booking;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class BookingTest
 * @package Tests\AppBundle\Service\Advertisers
 */
class BookingTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $httpClient;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->httpClient = $this->getMockBuilder(HTTPClient::class)
            ->setMethods(['request'])
            ->getMock();
    }

    /**
     * @dataProvider responseProvider
     *
     * @param array $response
     * @param array $expected
     */
    public function testGetAvailabilities(array $response, array $expected)
    {
        $this->httpClient->expects($this->once())->method('request')->willReturn(json_encode($response));

        $booking = new Booking($this->httpClient);
        $this->assertEquals($booking->getAvailabilities(), $expected);
        $this->assertEquals($booking->getName(), 'Booking');
    }

    public function testGetAvailabilitiesException()
    {
        $this->httpClient->expects($this->exactly(ApiConnectorAbstract::RETRY))
            ->method('request')
            ->willThrowException(new \Exception('Internal error'));

        $booking = new Booking($this->httpClient);
        $this->expectException('Exception');
        $booking->getAvailabilities();
    }

    /**
     * @return array
     */
    public function responseProvider(): array
    {
        return [
            [
                'response' => [
                    'hotels' => [
                        [
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
                ],
                'expected' => [
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
            ],
        ];
    }
}
