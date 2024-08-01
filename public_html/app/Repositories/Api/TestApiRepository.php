<?php declare(strict_types=1);

namespace App\Repositories\Api;

use App\Repositories\Api\Interfaces\TestApiRepositoryInterface;
use Illuminate\Http\Client\PendingRequest;

/**
 * Class TestApiRepository
 * @package App\Repositories\Api
 */
class TestApiRepository extends BaseApi implements TestApiRepositoryInterface
{
    /**
     * @var PendingRequest $http
     */
    protected PendingRequest $http;

    /**
     * TestApiRepository constructor.
     */
    public function __construct()
    {
        $this->http = $this->buildClient();
    }

    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return 'https://jsonplaceholder.typicode.com/';
    }

    /**
     * @return \Illuminate\Http\Client\Response
     */
    public function testMethod()
    {
        return $this->http->post('users', ['something' => 'something']);
    }
}

