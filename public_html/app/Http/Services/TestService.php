<?php declare(strict_types=1);

namespace App\Http\Services;

use App\Repositories\Api\Interfaces\TestApiRepositoryInterface;
use App\Repositories\Interfaces\TestRepositoryInterface;

class TestService
{

    /**
     * @var TestRepositoryInterface $testRepository
     */
    private TestRepositoryInterface $testRepository;

    /**
     * @var TestApiRepositoryInterface $testApiRepository
     */
    private TestApiRepositoryInterface $testApiRepository;

    /**
     * TestService constructor.
     * @param TestRepositoryInterface $testRepository
     * @param TestApiRepositoryInterface $testApiRepository
     */
    public function __construct(TestRepositoryInterface $testRepository, TestApiRepositoryInterface $testApiRepository)
    {
        $this->testRepository = $testRepository;
        $this->testApiRepository = $testApiRepository;
    }

    /**
     * @return int
     */
    public function testMethod(): array
    {
        return $this->testRepository->testRepoMethod();
    }

    public function testApiMethod()
    {
        return $this->testApiRepository->testMethod();
    }

}
