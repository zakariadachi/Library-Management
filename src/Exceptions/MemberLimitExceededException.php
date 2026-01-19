<?php

namespace Src\Exceptions;

use Exception;

class MemberLimitExceededException extends Exception
{
    protected $message = "Borrowing limit reached for this member type.";
}
