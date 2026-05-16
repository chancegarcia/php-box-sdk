<?php

namespace Box\Exception;

use Box\Http\Response\BoxResponseInterface;
use Box\Http\Response\ResponseParser;
use Exception;

class BoxResponseException extends BoxException
{
    /**
     * create constants based on the possible returns for oauth
     * https://developers.box.com/oauth/
     */

    protected ?BoxResponseInterface $response = null;

    public function __construct(string $message = "", int|string $code = 0, ?Exception $previous = null, ?BoxResponseInterface $response = null)
    {
        parent::__construct($message, $code, $previous);

        if ($response instanceof BoxResponseInterface) {
            $this->response = $response;

            $wwwAuthenticationHeaderLine = $response->getHeaderLine('WWW-Authenticate');
            $parsedLine = ResponseParser::parseWwwAuthenticateHeader($wwwAuthenticationHeaderLine);

            if (array_key_exists('error', $parsedLine)) {
                $this->error = $this->boxCode = $this->sanitize($parsedLine['error']);
            }

            if (array_key_exists('error_description', $parsedLine)) {
                $this->errorDescription = $this->sanitize($parsedLine['error_description']);
            }

            // attempt to parse response body for error details
            $content = $response->getContent();
            if (!empty($content)) {
                try {
                    $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    $decoded = null;
                }
                if (is_array($decoded)) {
                    if (isset($decoded['code'])) {
                        $this->boxCode = $this->sanitize($decoded['code']);
                    }
                    if (isset($decoded['message'])) {
                        $sanitizedMessage = $this->sanitize($decoded['message']);
                        $this->errorDescription = $this->errorDescription ? $this->errorDescription . " | " . $sanitizedMessage : $sanitizedMessage;
                    }
                    if (isset($decoded['context_info'])) {
                        $this->addContext($decoded['context_info']);
                    }
                }
            }
        }
    }

    public function getResponse(): ?BoxResponseInterface
    {
        return $this->response;
    }

    public function setResponse(?BoxResponseInterface $response = null): void
    {
        $this->response = $response;
    }
}
