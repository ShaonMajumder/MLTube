<?php declare(strict_types=1);

namespace App\Repositories\Api;

use App\Http\Middleware\HttpRequestMiddleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Class BaseApi
 * @package App\Repositories\Api
 */
abstract class BaseApi
{
    /**
     * @return string
     */
    abstract protected function baseUrl(): string;

    /**
     * @param bool $proxy
     * @return PendingRequest
     */
    protected function buildClient(bool $proxy = true): PendingRequest
    {
        $options = [
            'base_uri' => $this->baseUrl()
        ];

        if ($proxy && env('HTTP_PROXY_HOST', false) && env('APP_ENV') !== 'local') {
            //$options['proxy'] = env('HTTP_PROXY_HOST', 'tnmproxy.telenor.com.mm:8008');
            $options['proxy'] = 'http://10.84.93.39:8008';
        }

        return Http::withOptions($options)->withoutVerifying();
    }
}
