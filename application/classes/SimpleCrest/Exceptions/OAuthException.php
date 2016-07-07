<?php

namespace SimpleCrest\Exceptions;

class OAuthException extends \Exception
{
    /**
     * @var string
     */
    protected $message = "OAuth Access Token not provided, but required for this request!";
}