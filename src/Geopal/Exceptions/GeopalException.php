<?php

namespace Geopal\Exceptions;

class GeopalException extends \Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function noPropertyFound()
    {
        echo "No property found";
    }

    public function noSuccessProperty()
    {
        echo "There is no success property";
    }
}
