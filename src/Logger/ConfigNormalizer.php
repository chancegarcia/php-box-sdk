<?php

declare(strict_types=1);

namespace Box\Logger;

class ConfigNormalizer
{
    public function normalize(array $config): array
    {
        $normalized = [
            'name' => $config['name'] ?? 'box-sdk',
            'log_dir' => $config['log_dir'] ?? 'var/log',
            'log_file_basename' => $config['log_file_basename'] ?? 'box-sdk.log',
            'handlers' => [],
        ];

        foreach ($config['handlers'] ?? [] as $name => $handler) {
            $normalized['handlers'][$name] = [
                'type' => $handler['type'] ?? 'rotating_file',
                'path' => $handler['path'] ?? $normalized['log_dir'] . '/' . $normalized['log_file_basename'],
                'level' => $handler['level'] ?? 'debug',
                'max_files' => (int)($handler['max_files'] ?? 5),
                'max_file_size' => (int)($handler['max_file_size'] ?? 104857600), // 100MB
                'levels' => $handler['levels'] ?? null,
            ];
        }

        return $normalized;
    }
}
