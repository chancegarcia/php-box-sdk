<?php

namespace Box\Trait;

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Box\Http\Util\Redactor;
use Psr\Log\LoggerInterface;

trait BoxApiErrorTrait
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
     * Build and throw a BoxException from a Box API error data array.
     *
     * @param array $data containing at minimum an 'error' key
     * @param string|null $message override for the exception message
     * @param BoxResponseInterface|null $boxResponse attach HTTP context to the exception
     *
     * @throws BoxException
     */
    public function error(array $data, ?string $message = null, ?BoxResponseInterface $boxResponse = null): never
    {
        $error = $data['error'] ?? 'unknown_error';
        if (!is_string($message)) {
            $message = $error;
        }
        $errorDescription = $data['error_description'] ?? $message;

        $exception = new BoxException($message);
        $exception->setError($error);
        $exception->setErrorDescription($errorDescription);
        $exception->setStatus($error);

        if (array_key_exists('code', $data)) {
            $exception->setBoxCode($data['code']);
        }

        foreach ($data as $k => $v) {
            if ($k !== 'error' && $k !== 'error_description') {
                $exception->addContext($v, $k);
            }
        }

        if ($this->getLogger() instanceof LoggerInterface) {
            $context = [
                'error' => $error,
                'error_description' => $errorDescription,
                'trace' => $exception->getTraceAsString(),
            ];

            if ($boxResponse instanceof BoxResponseInterface) {
                $context['http_status'] = $boxResponse->getStatusCode();
                $context['response_body'] = $boxResponse->getContent();
            }

            $this->getLogger()->error($message, $this->getRedactor()->redactArray($context));
        }

        throw $exception;
    }

    abstract public function getLogger(): ?LoggerInterface;
}
