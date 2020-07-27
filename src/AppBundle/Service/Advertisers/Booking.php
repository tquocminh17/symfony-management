<?php

namespace AppBundle\Service\Advertisers;

/**
 * Class Booking
 * @package AppBundle\Service\Advertisers
 */
class Booking extends ApiConnectorAbstract
{
    protected $name = 'Booking';
    protected $url = 'https://f704cb9e-bf27-440c-a927-4c8e57e3bad1.mock.pstmn.io/s1/availability';
    protected $method = 'GET';
}
