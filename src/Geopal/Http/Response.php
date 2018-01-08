<?php

namespace Geopal\Http;

use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Response constructor.
     * @param ResponseInterface $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function json()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}