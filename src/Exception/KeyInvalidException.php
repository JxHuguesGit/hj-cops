<?php
namespace src\Exception;

class KeyInvalidException extends \Exception
{
    public function __construct($key)
    {
        throw new \Exception("Invalid key $key.");
    }
}
