<?php

namespace App\Repositories;

use App\Models\Task;

interface ITaskRepository
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
