<?php declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PushNotification;
use Illuminate\Pagination\LengthAwarePaginator;

interface PushNotificationRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator;

    public function create(array $data): PushNotification;

    public function find(int $id): ?PushNotification;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
