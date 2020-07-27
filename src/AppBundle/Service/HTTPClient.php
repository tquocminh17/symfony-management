<?php

namespace AppBundle\Service;

/**
 * Class HTTPClient
 * @package AppBundle\Service
 */
class HTTPClient
{
    /**
     * @param string $url
     * @param string $method
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function request(string $url, string $method, $data = []): string
    {
        $curl = curl_init();

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $errorCode = curl_errno($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $success = !$errorCode && $httpCode == 200;

        curl_close($curl);

        if ($success) {
            return $result;
        }

        throw new \Exception('Request failed', $errorCode);
    }
}
