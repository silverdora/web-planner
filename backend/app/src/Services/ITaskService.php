<?php

namespace App\Services;

use App\Models\Task;

interface ITaskService
{
    public function getById(int $id): ?Task;

    /**
     * @return Task[]
     */
    public function getByUserId(int $userId): array;

    public function create(Task $task): Task;
    public function update(Task $task): bool;
    public function delete(int $id, int $userId): void;
}
