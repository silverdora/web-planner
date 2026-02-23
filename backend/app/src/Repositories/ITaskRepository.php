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
    public function create(Article $task): Task;
    public function update(Article $task): bool;
    public function delete(int $id): bool;
}
