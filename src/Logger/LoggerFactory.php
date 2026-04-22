<?php

declare(strict_types=1);

namespace Box\Logger;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\FilterHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class LoggerFactory
{
    public function __construct(
        private ConfigNormalizer $normalizer
    ) {}

    public function createLogger(array $config, array $overrides = []): LoggerInterface
    {
        $normalizedConfig = $this->normalizer->normalize($config);
        $this->applyOverrides($normalizedConfig, $overrides);

        $logger = new Logger($normalizedConfig['name']);

        foreach ($normalizedConfig['handlers'] as $handlerConfig) {
            $handler = $this->createHandler($handlerConfig);
            $logger->pushHandler($handler);
        }

        return $logger;
    }

    private function applyOverrides(array &$config, array $overrides): void
    {
        if (isset($overrides['log_dir'])) {
            $config['log_dir'] = $overrides['log_dir'];
            // Re-apply paths for default handlers if they were using the old log_dir
            foreach ($config['handlers'] as &$handler) {
                // If the path was derived from the default log_dir, update it
                $handler['path'] = $config['log_dir'] . '/' . basename($handler['path']);
            }
        }

        if (isset($overrides['log_file'])) {
            // --log-file forces a single handler with all levels
            $config['handlers'] = [
                'override' => [
                    'type' => 'rotating_file',
                    'path' => $config['log_dir'] . '/' . $overrides['log_file'],
                    'level' => 'debug',
                    'max_files' => 5,
                    'max_file_size' => 104857600,
                    'levels' => null, // null means all levels >= level
                ],
            ];
        }
    }

    private function createHandler(array $handlerConfig): RotatingFileHandler|FilterHandler
    {
        $level = Level::fromName(ucfirst($handlerConfig['level']));
        
        $handler = new RotatingFileHandler(
            $handlerConfig['path'],
            $handlerConfig['max_files'],
            $level
        );

        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            "Y-m-d H:i:s"
        );
        $handler->setFormatter($formatter);

        if (!empty($handlerConfig['levels'])) {
            $levels = array_map(fn($l) => Level::fromName(ucfirst($l)), $handlerConfig['levels']);
            return new FilterHandler($handler, $levels);
        }

        return $handler;
    }
}
