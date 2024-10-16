<?php

namespace App\Logging;

use App\Helpers\Elasticsearch;
use Monolog\Handler\ElasticsearchHandler;
use Monolog\Logger;

class CustomElasticsearchHandler extends ElasticsearchHandler
{
    protected function write(array $record): void
    {
        if (isset($record['context']['index'])) {
            $today = now()->format('Y-m-d');
            $indexName = $record['context']['index'] . '-' . $today;

            $this->options['index'] = $indexName;
            $record['formatted']['_index'] = $indexName;
            unset($record['context']['index']);
            unset($record['formatted']['context']['index']);
        }
        // dd($record);
        parent::write($record);
    }
}
