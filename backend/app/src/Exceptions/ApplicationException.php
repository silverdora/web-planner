<?php

namespace App\Exceptions;

class ApplicationException extends \Exception
{
    /**
     * ApplicationException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}

