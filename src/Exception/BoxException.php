<?php

namespace Box\Exception;

use Box\Http\Response\BoxResponseInterface;
use Box\Http\Util\Redactor;
use stdClass;

class BoxException extends \Exception
{
    public const string INVALID_CLASS_TYPE = "Invalid Class Type";
    public const string UNKNOWN_CLASS = "Unknown Class";
    public const string INVALID_CLASS = "Invalid Class";
    public const string INVALID_INPUT = "Invalid Input";
    public const string MISSING_ID = "Missing Id";
    public const string BOX_API_ERROR = "Box API Error";
    public const string INVALID_JSON = "Invalid JSON";

    // mixed: Box API error payloads can be string, array, or stdClass depending on the endpoint
    protected mixed $error = null;
    // mixed: Box API error descriptions can be string, array, or stdClass depending on the endpoint
    protected mixed $errorDescription = null;
    protected array $context = [];
    protected int|string|null $boxCode = null;
    // int|string: normally an HTTP status int, but BoxApiErrorTrait also passes a string error code
    protected int|string|null $status = null;

    protected static ?Redactor $redactor = null;

    public function __construct(string $message = "", int|string $code = 0, ?\Throwable $previous = null)
    {
        $message = $this->redact($message);
        if (is_int($code)) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, 0, $previous);
            $this->boxCode = $code;
        }
    }

    protected function getRedactor(): Redactor
    {
        if (null === self::$redactor) {
            self::$redactor = new Redactor();
        }

        return self::$redactor;
    }

    protected function redact(string $string): string
    {
        return $this->getRedactor()->redactString($string);
    }

    /**
     * @var null|BoxResponseInterface
     */
    protected ?BoxResponseInterface $boxResponse = null;

    public function setError(mixed $error = null): void
    {
        $this->error = $error;
    }

    public function getError(): mixed
    {
        return $this->error;
    }

    public function setErrorDescription(mixed $errorDescription = null): void
    {
        $this->errorDescription = $errorDescription;
    }

    public function getErrorDescription(): mixed
    {
        return $this->errorDescription;
    }

    public function addContext(mixed $contextInformation = null, ?string $key = null): void
    {
        $contextInformation = $this->sanitize($contextInformation);
        if (is_array($contextInformation)) {
            $contextInformation = $this->getRedactor()->redactArray($contextInformation);
        }

        if (is_string($key)) {
            $finalKey = $key;
            // if we have duplicate key for some reason, make it unique
            if (array_key_exists($key, $this->context)) {
                do {
                    $finalKey = uniqid($key . "_", true);
                } while (array_key_exists($finalKey, $this->context));
            }

            $this->context[$finalKey] = $contextInformation;
        } else {
            $this->context[] = $contextInformation;
        }
    }

    protected function sanitize(mixed $data): mixed
    {
        if (is_string($data)) {
            // Mask common tokens and secrets
            $patterns = [
                '/(access_token|refresh_token|client_secret|code)(=|":\s*")([^"&\s,]+)/i',
                '/(Bearer\s+)([^"&\s,]+)/i'
            ];
            foreach ($patterns as $pattern) {
                $data = preg_replace_callback($pattern, function ($matches) {
                    $secret = $matches[count($matches) - 1];
                    $masked = substr($secret, 0, 4) . '...' . substr($secret, -4);
                    if (strlen($secret) <= 8) {
                        $masked = '********';
                    }
                    // Reconstruct with the prefix groups
                    $prefix = '';
                    for ($i = 1; $i < count($matches) - 1; $i++) {
                        $prefix .= $matches[$i];
                    }
                    return $prefix . $masked;
                }, $data);
            }
        } elseif (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_string($k) && preg_match('/(token|secret|code)/i', $k)) {
                    $data[$k] = '********';
                } else {
                    $data[$k] = $this->sanitize($v);
                }
            }
        } elseif ($data instanceof stdClass) {
            foreach (get_object_vars($data) as $k => $v) {
                if (preg_match('/(token|secret|code)/i', $k)) {
                    $data->$k = '********';
                } else {
                    $data->$k = $this->sanitize($v);
                }
            }
        }

        return $data;
    }

    public function getContext(?string $key = null): mixed
    {
        // make sure we have a key value and avoid false negative; allow null to returned on non-existent key
        if (!is_null($key)) {
            if (array_key_exists($key, $this->context)) {
                return $this->context[$key];
            }

            return null;
        }

        // if provided a null key, we return full context
        return $this->context;
    }

    public function getBoxCode(): int|string|null
    {
        return $this->boxCode;
    }

    public function setBoxCode(int|string|null $boxCode = null): void
    {
        $this->boxCode = $boxCode;
    }

    public function getBoxResponse(): ?BoxResponseInterface
    {
        return $this->boxResponse;
    }

    public function setBoxResponse(BoxResponseInterface $boxResponse): void
    {
        $this->boxResponse = $boxResponse;
    }

    public function setStatus(int|string|null $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): int|string|null
    {
        return $this->status;
    }
}
