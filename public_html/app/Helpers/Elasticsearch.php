<?php

namespace App\Helpers;

use Elastic\Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Support\Facades\Log;

class Elasticsearch
{
    const KIBANA_INDEX = '.kibana';
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST','elasticsearch:9200')])
            ->build();
    }

    /**
     *  Create a Kibana index pattern
     *
     * @param $indexPatternName
     * @param $patternId
     * @return bool
     * @throws Exception
     */
    public function createKibanaIndexPattern($indexPatternName, $patternId): bool
    {
        $params = [
            'index' => self::KIBANA_INDEX,
            'id'    => "index-pattern:$patternId", // Unique ID for the pattern
            'body'  => [
                'type' => 'index-pattern',
                'index-pattern' => [
                    'title'       => $indexPatternName, // The index pattern, e.g., "my-logs-*"
                    'timeFieldName' => '@timestamp' // Optional: the default time field
                ]
            ]
        ];

        try {
            $response = $this->client->index($params);
            Log::info('While Log index pattern created: ' . $indexPatternName . ' response => ' . $response);

            // if already exist it is "updated"
            return isset($response['result']) && $response['result'] === "created"
                && isset($response['_shards']['successful']) && $response['_shards']['successful'] === 1
                && isset($response['_id']) && $response['_id'] === "index-pattern:$patternId";
        } catch (\Exception $e) {
            Log::error('Failed to create index pattern: ' . $e->getMessage());
            return false;
        }
    }

    /**
     *  List all Kibana index patterns
     *
     * @return array
     * @throws Exception
     */
    public function listAllKibanaIndexPatterns(): array
    {
        $params = [
            'index' => self::KIBANA_INDEX,
            'body'  => [
                'query' => [
                    'term' => ['type' => 'index-pattern']
                ]
            ]
        ];

        try {
            $response = $this->client->search($params);
            Log::info('Successfully retrieved Kibana index patterns. response => '. $response);
            return isset($response['hits']) && isset($response['hits']['hits']) ? $response['hits']['hits'] : [];
        } catch (Exception $e) {
            Log::error('Failed to retrieve Kibana index patterns: ' . $e->getMessage());
            return [];
        }
    }

    /**
     *  Delete a Kibana index pattern
     *
     * @param $indexPatternId
     * @return bool
     * @throws Exception
     */
    public function deleteKibanaIndexPattern($indexPatternId): bool
    {
        $params = [
            'index' => self::KIBANA_INDEX,
            'id'    => "index-pattern:$indexPatternId", // Unique ID for the pattern
        ];

        try {
            $response = $this->client->delete($params);
            Log::info('While Log index pattern deleted with id: ' . $indexPatternId . ' response => ' . $response);
            return isset($response['result']) && $response['result'] === "deleted"
                && isset($response['_id']) && $response['_id'] === "index-pattern:$indexPatternId"
                && isset($response['_shards']['successful']) && $response['_shards']['successful'] === 1;
        } catch (Exception $e) {
            Log::error('Failed to delete index pattern: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all Kibana index patterns of type 'index-pattern'
     *
     * @return bool|object
     */
    public function deleteAllKibanaIndexPatterns()
    {
        $params = [
            'index' => self::KIBANA_INDEX,
            'body'  => [
                'query' => [
                    'term' => ['type' => 'index-pattern']
                ]
            ]
        ];

        try {
            $response = $this->client->deleteByQuery($params);
            Log::info('All Kibana index patterns deleted. ' . $response);
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to delete all Kibana index patterns: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a log index
     *
     * @param $indexName
     * @return bool
     * @throws Exception
     */
    public function createLogsIndex($indexName): bool
    {
        $params = [
            'index' => $indexName,
            'body'  => [
                'mappings' => [
                    'properties' => [
                        'message' => [
                            'type' => 'text'
                        ],
                        '@timestamp' => [
                            'type' => 'date'
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = $this->client->indices()->create($params);
            Log::info('While Log index created: ' . $indexName . ' response => ' . $response);
            return isset($response['acknowledged']) && $response['acknowledged'] === true
                && isset($response['shards_acknowledged']) && $response['shards_acknowledged'] === true
                && isset($response['index']) && $response['index'] === $indexName;
        } catch (Exception $e) {
            Log::error('Failed to create log index: ' . $e->getMessage());
            return false;
        }
    }

    /**
     *  Delete a log index
     *
     * @param $indexName
     * @return bool
     * @throws Exception
     */
    public function deleteLogIndex($indexName): bool
    {
        $params = [
            'index' => $indexName,
        ];

        try {
            $response = $this->client->indices()->delete($params);
            Log::info('While Log index deleted: ' . $indexName . ' response => ' . $response);
            return isset($response['acknowledged']) && $response['acknowledged'] === true;
        } catch (Exception $e) {
            Log::error('Failed to delete log index: ' . $e->getMessage());
            return false;
        }
    }

    /**
     *  Delete all indices in Elasticsearch
     *
     * @return bool
     * @throws Exception
     */
    public function deleteAllIndices()
    {
        try {
            $indice = $this->listAllLogsIndices();
            foreach($indice as $index){
                $this->deleteLogIndex($index['index']);
            }

            Log::info('All user indices deleted.' . $indice);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete all user indices: ' . $e->getMessage());
            return false;
        }
    }
    

    /**
     *  List all indices in Elasticsearch
     *
     * @return array
     * @throws Exception
     */
    public function listAllLogsIndices(): array
    {
        try {
            $indices = $this->client->cat()->indices([
                'format' => 'json',
            ])->asArray();
            Log::info('Successfully retrieved all indices. ', $indices);
            return $indices;
        } catch (Exception $e) {
            Log::error('Failed to retrieve indices from Elasticsearch: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if Log Index Pattern exists.
     *
     * @param $patternId
     * @return bool
     * @throws Exception
     */
    public function checkIfLogIndexPatternExists($patternId): bool
    {
        $params = [
            'index' => self::KIBANA_INDEX,
            'id'    => "index-pattern:$patternId"
        ];

        try {
            $response = $this->client->get($params);
            Log::info('While checking if Log Index Pattern exists. response => '. $response);
            return isset($response['found']) && $response['found'] === true
                && isset($response['_id']) && $response['_id'] === "index-pattern:$patternId"
                && isset($response['_source']['type']) && $response['_source']['type'] === 'index-pattern';
        } catch (Exception $e) {
            Log::info('Checking If Log Index Pattern exists failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     *  Check if the Elasticsearch cluster is healthy
     *
     * @return bool
     * @throws Exception
     */
    public function isClusterHealthy(): bool
    {
        try {
            $response = $this->client->cluster()->health();
            $healthStatus = $response['status'];
            Log::info('Cluster health status: ' . $healthStatus);
            return $healthStatus === 'green';
        } catch (\Exception $e) {
            Log::error('Failed to check cluster health: ' . $e->getMessage());
            return false;
        }
    }

    // not tested
    public function deleteAllKibanaIndexPatternsByPrefix($prefix)
    {
        $params = [
            'index' => self::KIBANA_INDEX,
            'body'  => [
                'query' => [
                    'prefix' => ['_id' => $prefix] // Deletes all documents with _id starting with the prefix
                ]
            ]
        ];

        try {
            $response = $this->client->deleteByQuery($params);
            Log::info('All Kibana index patterns with prefix ' . $prefix . ' deleted.');
            return $response;
        } catch (Exception $e) {
            Log::error('Failed to delete all Kibana index patterns by prefix: ' . $e->getMessage());
            return false;
        }
    }

    public function testConnection(): bool
    {
        try {
            $response = $this->client->cluster()->health()->asArray();
            Log::info('Elasticsearch connection successful.', ['response' => $response]);
            return isset($response['status']) && $response['status'] === 'green';
        } catch (Exception $e) {
            Log::error('Elasticsearch connection failed: ' . $e->getMessage());
            return false;
        }
    }

}
