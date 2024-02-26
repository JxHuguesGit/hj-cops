<?php
namespace src\Exception;

class KeyHasUseException extends \Exception
{
    public function __construct($key)
    {
        throw new \Exception("Key $key already in use.");
    }
}
