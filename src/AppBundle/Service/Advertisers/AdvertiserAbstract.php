<?php

namespace AppBundle\Service\Advertisers;

/**
 * Class AdvertiserAbstract
 * @package AppBundle\Service\Advertisers
 */
abstract class AdvertiserAbstract implements AdvertiserInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
