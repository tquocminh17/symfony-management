<?php

namespace AppBundle\Service\Advertisers;

/**
 * Interface AvailabilityInterface
 * @package AppBundle\Service\Advertisers
 */
interface AdvertiserInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     * @throws \Exception
     */
    public function getAvailabilities(): array;
}
