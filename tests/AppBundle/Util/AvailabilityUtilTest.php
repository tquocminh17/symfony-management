<?php

namespace Tests\AppBundle\Util;

use AppBundle\Util\AvailabilityUtil;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * Class AvailabilityUtilTest
 * @package Tests\AppBundle\Util
 */
class AvailabilityUtilTest extends TestCase
{
    /**
     * @dataProvider roomsProvider
     *
     * @param array $rooms1
     * @param array $rooms2
     * @param array $expected
     */
    public function testMergeAndDeduplicateRoomsByPrice(array $rooms1, array $rooms2, array $expected)
    {
        $this->assertEquals($expected, AvailabilityUtil::mergeAndDeduplicateRoomsByPrice($rooms1, $rooms2));
    }

    /**
     * @return array
     */
    public function roomsProvider(): array
    {
        return [
            [
                'rooms1' => [
                    [
                        'code' => 'code1',
                        'total' => 12.9,
                    ],
                    [
                        'code' => 'code2',
                        'total' => 13.9,
                    ],
                    [
                        'code' => 'code3',
                        'total' => 11.9,
                    ],
                ],
                'rooms2' => [
                    [
                        'code' => 'code1',
                        'total' => 16,
                    ],
                    [
                        'code' => 'code3',
                        'total' => 10.1,
                    ],
                    [
                        'code' => 'code4',
                        'total' => 11.9,
                    ],
                ],
                'expected' => [
                    [
                        'code' => 'code1',
                        'total' => 12.9,
                    ],
                    [
                        'code' => 'code2',
                        'total' => 13.9,
                    ],
                    [
                        'code' => 'code3',
                        'total' => 10.1,
                    ],
                    [
                        'code' => 'code4',
                        'total' => 11.9,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider roomsSortProvider
     *
     * @param array $rooms
     * @param array $expected
     */
    public function testSortArrayByCheapestPrice(array $rooms, array $expected)
    {
        AvailabilityUtil::sortArrayByCheapestPrice($rooms);
        $this->assertEquals($expected, $rooms);
    }

    /**
     * @return array
     */
    public function roomsSortProvider(): array
    {
        return [
            [
                [
                    [
                        'code' => 'code1',
                        'total' => 12.9,
                    ],
                    [
                        'code' => 'code2',
                        'total' => 13.9,
                    ],
                    [
                        'code' => 'code3',
                        'total' => 11.9,
                    ],
                ],
                [
                    [
                        'code' => 'code3',
                        'total' => 11.9,
                    ],
                    [
                        'code' => 'code1',
                        'total' => 12.9,
                    ],
                    [
                        'code' => 'code2',
                        'total' => 13.9,
                    ],
                ],
            ],
        ];
    }
}
