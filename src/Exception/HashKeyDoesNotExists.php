<?php

namespace WabLab\Collection\Exception;


use Throwable;

class HashKeyDoesNotExists extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
