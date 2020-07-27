<?php

namespace AppBundle\Service\Advertisers;

use AppBundle\Service\HTTPClient;
use Psr\Log\LoggerInterface;

/**
 * Class ApiConnectorAbstract
 * @package AppBundle\Service\Advertisers
 */
abstract class ApiConnectorAbstract extends AdvertiserAbstract
{
    const RETRY = 3;

    /**
     * @var HTTPClient
     */
    protected $httpClient;

    /**
     * @return string
     */
    protected $url;

    /**
     * @return string
     */
    protected $method;

    /**
     * ApiConnectorAbstract constructor.
     * @param HTTPClient $httpClient
     */
    public function __construct(HTTPClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritdoc
     */
    public function getAvailabilities(): array
    {
        $retry = 0;
        do {
            try {
                $response = json_decode($this->httpClient->request($this->url, $this->method), true);
                $hotels = $this->getHotels($response);

                $result = [];
                foreach ($hotels as $hotel) {
                    $result[$hotel['name']] = $hotel;
                }

                return $result;
            } catch (\Exception $exception) {
                $retry++;
            }
        } while ($retry < self::RETRY);

        throw $exception;
    }

    /**
     * @param array $response
     * @return array
     */
    protected function getHotels(array $response): array
    {
        return $response['hotels'];
    }
}
