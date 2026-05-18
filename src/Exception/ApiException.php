<?php

namespace Box\Exception;

use Box\Http\Response\BoxResponseInterface;
use Throwable;

/**
 * Exception thrown when the Box API returns a non-successful HTTP status code.
 */
class ApiException extends BoxResponseException
{
    public function __construct(
        string $message = "",
        mixed $code = 0,
        ?Throwable $previous = null,
        ?BoxResponseInterface $response = null
    ) {
        // Map HTTP status code to exception code if not provided
        if (0 === $code && $response instanceof BoxResponseInterface) {
            $code = $response->getStatusCode();
        }

        parent::__construct($message, $code, $previous, $response);
    }
}
