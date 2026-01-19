<?php

namespace Src\Exceptions;

use Exception;

class LateFeeException extends Exception
{
    protected $message = "Action denied: Unpaid fines exceed the $10.00 limit.";
}
