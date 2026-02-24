<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\ITaskRepository;
use App\Repositories\TaskRepository;

class TaskService implements ITaskService
{
    private ITaskRepository $repository;

    public function __construct()
    {
        $this->repository = new TaskRepository();
    }

    /**
     * @return Task[]
     */
    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(int $id): ?Task
    {
        return $this->repository->getById($id);
    }

    public function create(Task $task): Task
    {
        return $this->repository->create($task);
    }

    public function update(Task $task): bool
    {
        return $this->repository->update($task);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
