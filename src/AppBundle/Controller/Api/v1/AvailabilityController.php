<?php

namespace AppBundle\Controller\Api\v1;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Factory\AdvertiserFactory;
use AppBundle\Util\AvailabilityUtil;

/**
 * Class AvailabilityController
 * @package AppBundle\Controller\Api\v1
 */
class AvailabilityController extends Controller
{
    /**
     * @Route("/api/v1/availabilities")
     * @Method("GET")
     *
     * @param AdvertiserFactory $advertiserFactory
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function availabilitiesAction(AdvertiserFactory $advertiserFactory, LoggerInterface $logger)
    {
        $advertisers = $advertiserFactory->getAdvertisers();

        $result = [];
        foreach ($advertisers as $advertiser) {
            try {
                $hotels = $advertiser->getAvailabilities();

                foreach ($hotels as $key => $hotel) {
                    if (!empty($result[$key])) {
                        $result[$key]['rooms'] = AvailabilityUtil::mergeAndDeduplicateRoomsByPrice(
                            $result[$key]['rooms'],
                            $hotel['rooms']
                        );
                    } else {
                        $result[$key] = $hotel;
                    }
                }
            } catch (\Exception $exception) {
                $logger->error('Failed to get availabilities', ['advertiser' => $advertiser->getName()]);
            }
        }

        foreach ($result as &$hotel) {
            AvailabilityUtil::sortArrayByCheapestPrice($hotel['rooms']);
            $hotel['cheapestPrice'] = $hotel['rooms'][0]['total'] ?? 0;
        }
        AvailabilityUtil::sortArrayByCheapestPrice($result, 'cheapestPrice');

        return new JsonResponse($result);
    }
}
