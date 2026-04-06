<?php

namespace App\Services;

use App\Enums\Priority;
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
        $this->validateTask($task, true);
        return $this->repository->create($task);
    }

    public function update(Task $task): bool
    {
        $this->validateTask($task, false);
        return $this->repository->update($task);
    }

    public function delete(int $id, int $userId): void
    {
        $this->repository->delete($id, $userId);
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

    private function normalizePriority(string $value): string
    {
        $normalized = strtolower(trim($value));

        if ($normalized === '') {
            throw new \InvalidArgumentException('Priority is required.');
        }

        $map = [
            '1' => Priority::Low,
            '2' => Priority::Medium,
            '3' => Priority::High,
            'low' => Priority::Low,
            'medium' => Priority::Medium,
            'high' => Priority::High,
        ];

        $priority = $map[$normalized] ?? null;

        if (!$priority) {
            throw new \InvalidArgumentException('Invalid priority value. Use low, medium, or high.');
        }

        return $priority->value;
    }

    private function normalizeDueDate(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new \InvalidArgumentException('Due date is required.');
        }

        $formats = ['Y-m-d H:i:s', 'Y-m-d\TH:i', 'Y-m-d'];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        throw new \InvalidArgumentException('Invalid due date format.');
    }

    private function normalizeCategoryId(?int $value): ?int
    {
        if ($value === null || $value === 0) {
            return null;
        }

        if ($value < 0) {
            throw new \InvalidArgumentException('Invalid category ID.');
        }

        return $value;
    }

    private function validateTask(Task $task, bool $isCreate): void
    {
        $task->title = trim($task->title);

        if ($task->title === '') {
            throw new \InvalidArgumentException('Title is required.');
        }

        if (mb_strlen($task->title) > 255) {
            throw new \InvalidArgumentException('Title must not exceed 255 characters.');
        }

        $task->description = trim($task->description);

        if (mb_strlen($task->description) > 2000) {
            throw new \InvalidArgumentException('Description must not exceed 2000 characters.');
        }

        $task->status = $this->normalizeStatus($task->status, $isCreate);
        $task->priority = $this->normalizePriority($task->priority);

        $task->dueDate = $this->normalizeDueDate($task->dueDate);
        $task->categoryId = $this->normalizeCategoryId($task->categoryId);
    }
}
