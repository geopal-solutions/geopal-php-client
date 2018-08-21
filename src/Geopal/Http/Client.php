<?php

namespace Geopal\Http;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * @var int
     */
    private $employeeId;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * Default Geopal API Url
     */
    const DEFAULT_API_URL = 'https://app.geopalsolutions.com/';

    /**
     * GeoPal API URL
     * @var null|string
     */
    private $apiUrl;

    /**
     * @var float
     */
    private $apiTimeout = 30.0;

    /**
     * @param $employeeId
     * @param $privateKey
     * @param null|GuzzleClient $guzzleClient
     * @param $apiUrl
     */
    public function __construct($employeeId, $privateKey, $guzzleClient = null, $apiUrl = null)
    {
        $this->employeeId = $employeeId;
        $this->privateKey = $privateKey;
        $this->apiUrl = is_null($apiUrl) ? self::DEFAULT_API_URL : $apiUrl;
        if (is_null($guzzleClient)) {
            $this->guzzleClient = new GuzzleClient([
                // Base URI is used with relative requests
                'base_uri' => $this->apiUrl,
                // You can set any number of default request options.
                'timeout'  => $this->apiTimeout,
            ]);
        } else {
            $this->guzzleClient = $guzzleClient;
        }
    }

    /**
     * @param $uri
     * @param array $params
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($uri, $params = array())
    {
        $response = $this->guzzleClient->request(
            'GET',
            $uri . '?' . http_build_query($params),
            ['headers' => $this->getHeaders('get', $uri)]
        );
        return new Response($response);
    }

    /**
     * @param $uri
     * @param array $params
     * @param string|null $file The file path of the file to upload or null
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($uri, $params = array(), $file = null)
    {
        if (is_null($file)) {
            $response = $this->guzzleClient->request(
                'POST',
                $uri,
                [
                    'headers' => $this->getHeaders('post', $uri),
                    'form_params' => $params
                ]
            );
        } else {
            $paramsMultiPart = [];
            foreach ($params as $paramName => $paramValue) {
                $paramsMultiPart[] = ['name' => $paramName, 'contents' => $paramValue];
            }
            $response = $this->guzzleClient->request(
                'POST',
                $uri,
                [
                    'headers' => $this->getHeaders('post', $uri),
                    'multipart' => array_merge(
                        [
                            [
                                'name' => 'file2upload',
                                'contents' => fopen($file, 'r')
                            ]
                        ],
                        $paramsMultiPart
                    )
                ]
            );
        }
        return new Response($response);
    }

    /**
     * @param $uri
     * @param array $params
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($uri, $params = array())
    {
        $response = $this->guzzleClient->request(
            'PUT',
            $uri,
            [
                'headers' => $this->getHeaders('put', $uri),
                'form_params' => $params
            ]
        );
        return new Response($response);
    }

    /**
     * @param $verb
     * @param $uri
     * @return array
     */
    public function getHeaders($verb, $uri)
    {
        $timestamp = $this->getTimeStamp();
        $headers = [];
        $headers['GEOPAL_SIGNATURE'] = $this->getSignature($verb, $uri, $timestamp);
        $headers['GEOPAL_TIMESTAMP'] = $timestamp;
        $headers['GEOPAL_EMPLOYEEID'] = $this->employeeId;
        return $headers;
    }

    /**
     * @param $verb
     * @param $uri
     * @param $timestamp
     * @return string
     */
    public function getSignature($verb, $uri, $timestamp)
    {
        $sigText = $verb.$uri.$this->employeeId.$timestamp;
        return base64_encode(hash_hmac('sha256', $sigText, $this->privateKey));
    }

    /**
     * @param $employeeId
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @param $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * gets the current RFC 2822 formatted date
     *
     * @return string
     */
    private function getTimeStamp()
    {
        return date('r');
    }
}
