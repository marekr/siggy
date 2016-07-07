<?php

namespace SimpleCrest\Exceptions;

class Exception extends \Exception
{
    /**
     * @var string
     */
    protected $response;

    /**
     * Exception constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct($message = "", $code = 405, Exception $previous = null)
    {
        $this->response = $message;
        $this->message = $this->errorMessage();
    }

    /**
     * @return string
     */
    protected function errorMessage()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}