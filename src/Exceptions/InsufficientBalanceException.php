<?php
namespace Mdayo\Wallet\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $code = 1002;  // your custom error code

    public function __construct($message = "Insufficient balance.")
    {
        parent::__construct($message, $this->code);
    }
}
