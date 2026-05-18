<?php

namespace Box\Service;

use Box\Contract\JsonFormatterInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleOutputFormatter
{
    public function __construct(
        private JsonFormatterInterface $jsonFormatter
    ) {
    }

    public function formatMasked(SymfonyStyle $io, array $data, bool $isJson = false): void
    {
        $maskedData = $this->maskSensitiveData($data);

        if ($isJson) {
            $io->writeln($this->jsonFormatter->format($maskedData));
            return;
        }

        foreach ($maskedData as $key => $value) {
            if (is_array($value)) {
                $io->writeln(sprintf('<info>%s</info>:', $key));
                foreach ($value as $subKey => $subValue) {
                    $io->writeln(sprintf('  <info>%s</info>: %s', $subKey, (string)$subValue));
                }
            } else {
                $io->writeln(sprintf('<info>%s</info>: %s', $key, (string)$value));
            }
        }
    }

    public function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'access_token',
            'refresh_token',
            'client_secret',
            'code',
            'assertion',
            'jwt_assertion',
        ];

        $fullRedactKeys = [
            'private_key',
            'private_key_passphrase',
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $fullRedactKeys, true)) {
                $data[$key] = '[REDACTED]';
            } elseif (in_array($key, $sensitiveKeys, true) && is_string($value)) {
                $data[$key] = $this->maskString($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            }
        }

        return $data;
    }

    private function maskString(string $value): string
    {
        if (strlen($value) <= 8) {
            return '********';
        }

        return substr($value, 0, 4) . '...' . substr($value, -4);
    }
}
