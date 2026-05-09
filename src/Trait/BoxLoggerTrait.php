<?php

namespace Box\Trait;

use Box\Exception\BoxException;
use Box\Exception\BoxResponseException;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Util\Redactor;
use Psr\Log\LoggerInterface;

trait BoxLoggerTrait
{
    protected ?Redactor $redactor = null;

    public function getRedactor(): Redactor
    {
        if (null === $this->redactor) {
            $this->redactor = new Redactor();
        }

        return $this->redactor;
    }

    public function setRedactor(?Redactor $redactor): void
    {
        $this->redactor = $redactor;
    }

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

            $redactedContext = $this->getRedactor()->redactArray($context);

            $this->getLogger()->error($loggerMessage, $redactedContext);
        }

        throw $exception;
    }

    protected function parseResponse(BoxResponseInterface $response): array
    {
        if ($response->isClientError() || $response->isServerError()) {
            throw new BoxResponseException('Box API request failed', $response->getStatusCode(), null, $response);
        }

        $content = $response->getContent();

        if (empty($content)) {
            return [];
        }

        try {
            $data = $response->json(true);
        } catch (\JsonException | \TypeError $e) {
            // fallback for older tests or non-standard response objects
            $data = json_decode((string)$content, true);
        }

        if (!is_array($data)) {
            // final fallback if json() returned null but content is not empty
            $data = json_decode((string)$content, true);
        }

        if (!is_array($data)) {
            return [];
        }

        if (array_key_exists('type', $data) && 'error' === $data['type']) {
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
