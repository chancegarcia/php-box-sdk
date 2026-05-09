<?php

namespace Box\Http\Util;

class Redactor
{
    public const REDACTED = '[REDACTED]';

    private const SENSITIVE_KEYS = [
        'access_token',
        'refresh_token',
        'client_secret',
        'code',
        'auth_code',
        'client_id', // sometimes sensitive depending on context, but usually okay. Let's include it for safety if requested.
    ];

    /**
     * Redact sensitive information from an array of headers.
     */
    public function redactHeaders(array $headers): array
    {
        foreach ($headers as $name => $values) {
            if (strtolower($name) === 'authorization') {
                $headers[$name] = [self::REDACTED];
                continue;
            }

            if (is_array($values)) {
                foreach ($values as $k => $v) {
                    $headers[$name][$k] = $this->redactString($v);
                }
            } else {
                $headers[$name] = $this->redactString($values);
            }
        }

        return $headers;
    }

    /**
     * Redact sensitive information from an associative array (e.g., request body or context).
     */
    public function redactArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), self::SENSITIVE_KEYS, true)) {
                $data[$key] = self::REDACTED;
            } elseif (is_array($value)) {
                $data[$key] = $this->redactArray($value);
            }
        }

        return $data;
    }

    /**
     * Redact sensitive information from a string (e.g., URL with query params or message).
     */
    public function redactString(string $string): string
    {
        // Redact Bearer tokens in strings
        $string = preg_replace('/(Bearer\s+)[a-zA-Z0-9\._\-]+/i', '$1' . self::REDACTED, $string);

        // Redact common query parameters
        foreach (self::SENSITIVE_KEYS as $key) {
            $string = preg_replace('/(' . preg_quote($key, '/') . '=)[^& \n]*/i', '$1' . self::REDACTED, $string);
        }

        return $string;
    }
}
