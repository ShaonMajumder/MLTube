<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonthlyLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        // Retrieve the directory path and filename prefix from the config
        $directoryPath = isset($config['directory']) ? storage_path('logs/' . $config['directory']) : storage_path('logs');
        $filename = $config['filename'] ?? 'laravel';
        $logLevel = $config['level'] ?? Logger::DEBUG;

        // Ensure the directory exists; if not, create it
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // Define the log file path dynamically, based on filename and current month
        $logPath = $directoryPath . '/' . $filename . '-' . date('Y-m') . '.log';

        // Create a new Logger instance with the specified filename prefix
        $logger = new Logger($filename);

        // Add a StreamHandler to write to the log file
        $logger->pushHandler(new StreamHandler($logPath, $logLevel));

        return $logger;
    }
}
