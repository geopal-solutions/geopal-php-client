<?php

namespace Geopal\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

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
     * @var Client
     */
    private $guzzleClient;

    /**
     * Geopal API Url
     */
    const API_URL = 'https://app.geopalsolutions.com/';

    /**
     * @param $employeeId
     * @param $privateKey
     * @param null|GuzzleClient $guzzleClient
     */
    public function __construct($employeeId, $privateKey, $guzzleClient = null)
    {
        $this->employeeId = $employeeId;
        $this->privateKey = $privateKey;
        if (is_null($guzzleClient)) {
            $this->guzzleClient = new GuzzleClient(['base_uri' => self::API_URL]);
        } else {
            $this->guzzleClient = $guzzleClient;
        }
    }

    /**
     * @param $uri
     * @param array $params
     * @return Response
     */
    public function get($uri, $params = array())
    {
        $options = ['headers' => $this->getHeaders('get', $uri)];
        return $this->guzzleClient->get($uri . '?' . http_build_query($params), $options);
    }

    /**
     * @param $uri
     * @param array $params
     * @return Response
     */
    public function post($uri, $params = array())
    {
        $options = ['headers' => $this->getHeaders('post', $uri)];
        return $this->guzzleClient->post($uri, $options, $params);
    }

    /**
     * @param $uri
     * @param array $params
     * @return Response
     */
    public function put($uri, $params = array())
    {
        return $this->guzzleClient->put($uri, $this->getHeaders('put', $uri), $params);
    }

    /**
     * @param $verb
     * @param $uri
     * @return array
     */
    public function getHeaders($verb, $uri)
    {
        $timestamp = $this->getTimeStamp();
        $headers = array();
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
