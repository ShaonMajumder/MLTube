<?php declare(strict_types=1);

namespace App\Repositories;

use App\Models\PushNotification;
use App\Repositories\Interfaces\PushNotificationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PushNotificationRepository implements PushNotificationRepositoryInterface
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return PushNotification::paginate($perPage);
    }

    public function create(array $data): PushNotification
    {
        return PushNotification::create($data);
    }

    public function find(int $id): ?PushNotification
    {
        return PushNotification::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $pushNotification = $this->find($id);
        if ($pushNotification) {
            return $pushNotification->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $pushNotification = $this->find($id);
        if ($pushNotification) {
            return $pushNotification->delete();
        }
        return false;
    }
}
