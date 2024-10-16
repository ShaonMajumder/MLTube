<?php

namespace Tests\Unit;

use App\Helpers\Elasticsearch;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class ElasticsearchTest extends TestCase
{
    protected $elasticsearch;
    protected $clientMock;
    protected $indexPatternId;
    protected $indexPattern;

    protected function setUp(): void
    {
        parent::setUp();
        $this->elasticsearch = new Elasticsearch();
        $this->indexPatternId = 'test-logs-pattern';
        $this->indexPattern = 'test-logs-*';
    }

    protected function tearDown(): void
    {
        // $this->elasticsearch->deleteKibanaIndexPattern($this->indexPatternId);
        parent::tearDown();
    }

    public function testCreateKibanaIndexPattern()
    {
        $this->assertEquals(true, $this->elasticsearch->createKibanaIndexPattern($this->indexPattern, $this->indexPatternId));
        sleep(3);
    }

    public function testListAllKibanaIndexPatterns(){
        $this->assertGreaterThan(0, count( $this->elasticsearch->listAllKibanaIndexPatterns() ) );
    }

    public function testcheckIfLogIndexPatternExists()
    {
        $this->assertEquals(true, $this->elasticsearch->checkIfLogIndexPatternExists($this->indexPatternId));
    }
    
    public function testDeleteKibanaIndexPattern()
    {
        $this->assertEquals(true, $this->elasticsearch->deleteKibanaIndexPattern($this->indexPatternId));
    }

    public function testCreateLogIndex()
    {
        $today = now()->format('Y-m-d');
        $indexName = 'test-logs-' . $today;
        $this->assertEquals(true, $this->elasticsearch->createLogsIndex($indexName));
    }

    public function testListAllLogsIndices()
    {
        $indices = $this->elasticsearch->listAllLogsIndices();
        $this->assertIsArray($indices);
        $this->assertGreaterThan(0, count($indices));
    }

    public function testDeleteLogIndex()
    {
        $today = now()->format('Y-m-d');
        $indexName = 'test-logs-' . $today;
        $this->assertEquals(true, $this->elasticsearch->deleteLogIndex($indexName) );
    }

    public function testTestConnection()
    {
        $this->assertEquals(true, $this->elasticsearch->testConnection() );
    }

}
