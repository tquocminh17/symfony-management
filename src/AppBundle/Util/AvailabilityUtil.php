<?php

namespace AppBundle\Util;

/**
 * Class AvailabilityUtil
 * @package AppBundle\Util
 */
class AvailabilityUtil
{
    /**
     * @param array $rooms1
     * @param array $rooms2
     * @return array
     */
    public static function mergeAndDeduplicateRoomsByPrice(array $rooms1, array $rooms2): array
    {
        $result = [];
        foreach ($rooms1 as $room1) {
            $key = self::findRoomByCode($rooms2, $room1['code']);

            if ($key === false) {
                $result[] = $room1;
                continue;
            }

            $result[] = $room1['total'] < $rooms2[$key]['total'] ? $room1 : $rooms2[$key];
            unset($rooms2[$key]);
        }

        $result = array_merge($result, $rooms2);

        return $result;
    }

    /**
     * @param array $rooms
     * @param string $code
     * @return bool|int|string
     */
    private static function findRoomByCode(array $rooms, string $code)
    {
        foreach ($rooms as $key => $room) {
            if ($code === $room['code']) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param array $rooms
     * @param string $field
     * @return array
     */
    public static function sortArrayByCheapestPrice(array &$rooms, string $field = 'total'): array
    {
        usort($rooms, function (array $a, array $b) use ($field) {
            return $a[$field] > $b[$field];
        });

        return $rooms;
    }
}
