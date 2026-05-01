<?php

namespace Box\Trait;

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Psr\Log\LoggerInterface;

trait BoxLoggerTrait
{
    /**
     * used to throw exceptions that need to contain error information returned from Box
     *
     * @param array $data containing error and error_description keys
     * @param string|null $message
     * @param BoxResponseInterface|null $boxResponse
     *
     * @throws BoxException
     */
    public function error(array $data, ?string $message = null, ?BoxResponseInterface $boxResponse = null): void
    {
        $error = $data['error'] ?? 'unknown_error';
        $errorDescription = $data['error_description'] ?? $message ?? 'An unknown error occurred';

        $context = [
            'error' => $error,
            'error_description' => $errorDescription,
        ];

        if ($boxResponse instanceof BoxResponseInterface) {
            $context['http_status'] = $boxResponse->getStatusCode();
            $context['response_body'] = $boxResponse->getContent();
        }

        $exception = new BoxException($errorDescription, BoxException::BOX_API_ERROR);
        $exception->setStatus($error);

        if ($this->getLogger() instanceof LoggerInterface) {
            $loggerMessage = $error . "\n" . $exception->getTraceAsString() . "\n";

            $this->getLogger()->error($loggerMessage, $context);
        }

        throw $exception;
    }

    protected function parseResponse(BoxResponseInterface $response): array
    {
        $content = $response->getContent();
        $statusCode = $response->getStatusCode();

        if ($response->isClientError() || $response->isServerError()) {
            if (empty($content)) {
                $message = sprintf('Box API request failed with HTTP %d', $statusCode);
                $this->error([
                    'error' => 'http_error_' . $statusCode,
                    'error_description' => $message,
                ], $message, $response);
            }
        }

        if (empty($content)) {
            return [];
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $message = sprintf('Box API response JSON decode failed: %s', $e->getMessage());
            $this->error([
                'error' => 'json_decode_error',
                'error_description' => $content,
            ], $message, $response);
        }

        if (is_array($data) && array_key_exists('type', $data) && 'error' === $data['type']) {
            $this->error($data, $data['message'] ?? null, $response);
        }

        if (is_array($data) && array_key_exists('error', $data)) {
            $this->error($data, $data['error_description'] ?? null, $response);
        }

        return $data;
    }

    public function debug(string $message, array $context = []): void
    {
        if ($this->getLogger() instanceof LoggerInterface) {
            $this->getLogger()->debug($message, $context);
        }
    }

    /**
     * @return LoggerInterface|null
     */
    abstract public function getLogger(): ?LoggerInterface;
}
