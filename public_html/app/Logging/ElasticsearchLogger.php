<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\ElasticsearchHandler;

class ElasticsearchLogger
{
    /**
     * Create a custom Monolog instance for Elasticsearch.
     */
    public function __invoke(array $config): Logger
    {
        dd('jere');
        // Use the existing ElasticsearchHandler
        $client = \Elastic\Elasticsearch\ClientBuilder::create()
            ->setHosts(['elasticsearch:9200'])
            ->build();

        $options = array_merge([
            'index' => 'laravel-logs', // Default index
            'type' => '_doc',
        ], $config['handler_with']['options']);

        return new Logger(
            env('APP_NAME', 'elasticsearch-logger'), // Logger name
            [
                new ElasticsearchHandler($client, $options), // Create the handler
            ]
        );
    }
}
