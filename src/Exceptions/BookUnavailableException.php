<?php

namespace Src\Exceptions;

use Exception;

class BookUnavailableException extends Exception
{
    protected $message = "Sorry, all copies of this book are currently unavailable in this branch.";
}
