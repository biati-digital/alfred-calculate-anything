<?php
namespace Olifolkerd\Convertor\Exceptions;

use Exception;
use Throwable;

class FileNotFoundException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}