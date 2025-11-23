<?php

declare(strict_types=1);

namespace Shared\Exceptions;

use Exception;

/**
 * Class for exception with code 500.
 * Internal server error.
 */
class ServerErrorException extends Exception
{
    /**
     * Construct exception
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct(string $message = 'Server error.', int $code = 500)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
