<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $client = ClientBuilder::create()
            ->setHosts(['elasticsearch:9200'])
            ->build();

        $indices = config('elasticsearch.indices');

        foreach ($indices as $key=>$index) {
            if (!$client->indices()->exists(['index' => $index])) {
                $client->indices()->create(['index' => $index]);
            }
        }
    }
}

