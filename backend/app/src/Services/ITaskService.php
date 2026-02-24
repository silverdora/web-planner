<?php

namespace App\Services;

use App\Models\Task;

interface ITaskService
{
    /**
     * @return Task[]
     */
    public function getAll(): array;
    public function getById(int $id): ?Task;
    public function create(Task $task): Task;
    public function update(Task $task): bool;
    public function delete(int $id): void;
}
