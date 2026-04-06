<?php

namespace App\Repositories;

use App\Models\Task;

interface ITaskRepository
{
    public function getUpcomingTasks(int $userId): array;

    /**
     * @return Task[]
     */
    public function getByUserId(int $userId): array;

    /**
     * @return Task[]
     */
    public function getDashboardTasks(int $userId, array $filters): array;
    public function getDashboardCategoryStats(int $userId): array;

    public function countDashboardTasks(int $userId, array $filters): int;

    public function getDashboardStats(int $userId, array $filters = []): array;
    public function getByIdAndUserId(int $id, int $userId): ?Task;

    public function create(Task $task): Task;
    public function update(Task $task): bool;
    public function delete(int $id, int $userId): void;
}
