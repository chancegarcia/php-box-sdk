<?php

namespace Box\Model;

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Psr\Log\LoggerInterface;

trait ModelTrait
{
    public function classArray(): array
    {
        $aModel = get_object_vars($this);
        $aArray = array();

        foreach ($aModel as $k => $v)
        {
            $sKey = $this->toBoxVar($k);
            $aArray[ $sKey ] = $v;
        }

        return $aArray;
    }

    public function toBoxArray(): array
    {
        $arr = $this->classArray();

        return $this->removeEmpty($arr);
    }

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
     * @param string $class
     * @param string $classType
     *
     * @throws \Box\Exception\BoxException
     * @return bool returns true if validation passes. Throws exception if unable to validate or validation doesn't pass
     */
    public function validateClass(string $class, string $classType): bool
    {
        if (!class_exists($class))
        {
            throw new BoxException("Unable to find class", BoxException::UNKNOWN_CLASS);
        }

        if (!is_subclass_of($class, $classType) && $class !== $classType)
        {
            throw new BoxException("Invalid Connection Class", BoxException::INVALID_CLASS_TYPE);
        }

        return true;
    }

    /**
     * @param array $params
     * @param string $numericPrefix
     *
     * @return string
     */
    public function buildQuery(array $params, string $numericPrefix = ''): string
    {
        return http_build_query($params, $numericPrefix, '&', PHP_QUERY_RFC3986);
    }

    public function getNewClass(?string $className = null, mixed $classConstructorOptions = null): mixed
    {
        if (null === $className)
        {
            throw new BoxException('undefined class name', BoxException::INVALID_INPUT);
        }

        $sMethod = 'get' . ucfirst($className) . 'Class';

        $sClass = $this->$sMethod();

        $instance = new $sClass($classConstructorOptions);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }
}
