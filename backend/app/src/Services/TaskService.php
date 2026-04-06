<?php

namespace App\Services;

use App\Enums\Status;
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

    public function getByUserId(int $userId): array
    {
        return $this->repository->getByUserId($userId);
    }

    public function getById(int $id): ?Task
    {
        return $this->repository->getById($id);
    }

    public function create(Task $task): Task
    {
        $task->status = $this->normalizeStatus($task->status, true);
        return $this->repository->create($task);
    }

    public function update(Task $task): bool
    {
        $task->status = $this->normalizeStatus($task->status, false);
        return $this->repository->update($task);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    private function normalizeStatus(string $value, bool $isCreate): string
    {
        $normalized = strtolower(trim($value));

        if ($normalized === '') {
            if ($isCreate) {
                return Status::Created->value;
            }

            throw new \InvalidArgumentException('Status is required for update.');
        }

        $map = [
            '1' => Status::Created,
            '2' => Status::InProgress,
            '3' => Status::Done,
            'created' => Status::Created,
            'in progress' => Status::InProgress,
            'in_progress' => Status::InProgress,
            'done' => Status::Done,
            'completed' => Status::Done,
            'cancelled' => Status::Cancelled,
        ];

        $status = $map[$normalized] ?? null;

        if (!$status) {
            throw new \InvalidArgumentException('Invalid status value. Use created, in progress, done, or cancelled.');
        }

        return $status->value;
    }
}
