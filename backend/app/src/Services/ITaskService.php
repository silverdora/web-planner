<?php

namespace App\Services;

use App\Models\Task;

interface ITaskService
{
    public function getByIdAndUserId(int $id, int $userId): ?Task;

    /**
     * @return Task[]
     */
    public function getByUserId(int $userId): array;

    public function getDashboardTasks(int $userId, array $filters): array;
    public function getDashboardStats(int $userId, array $filters = []): array;

    public function create(Task $task): Task;
    public function update(Task $task): bool;
    public function delete(int $id, int $userId): void;
}
