<?php

namespace App\Exceptions;

class UserAlreadyExistsException extends ApplicationException
{
    /**
     * UserAlreadyExistsException constructor.
     *
     * @param string $message
     */
    public function __construct(string $message = "User with this email already exists")
    {
        parent::__construct($message);
    }
}

