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

    public function getByIdAndUserId(int $id, int $userId): ?Task
    {
        return $this->repository->getByIdAndUserId($id, $userId);
    }

    public function getDashboardStats(int $userId, array $filters = []): array
    {
        $status = trim((string)($filters['status'] ?? ''));
        if ($status !== '') {
            $status = $this->normalizeStatus($status, false);
        }

        $priority = trim((string)($filters['priority'] ?? ''));
        if ($priority !== '') {
            $priority = $this->normalizePriority($priority);
        }

        $categoryIdRaw = $filters['category_id'] ?? '';
        $categoryId = ($categoryIdRaw !== '' && $categoryIdRaw !== null)
            ? (int)$categoryIdRaw
            : null;

        $normalizedFilters = [
            'search' => trim((string)($filters['search'] ?? '')),
            'status' => $status,
            'priority' => $priority,
            'category_id' => $categoryId,
        ];

        return $this->repository->getDashboardStats($userId, $normalizedFilters);
    }


    public function getByUserId(int $userId): array
    {
        return $this->repository->getByUserId($userId);
    }

    public function getDashboardTasks(int $userId, array $filters): array
    {
        $page = max(1, (int)($filters['page'] ?? 1));
        $limit = max(1, min((int)($filters['limit'] ?? 10), 100));

        $sort = trim((string)($filters['sort'] ?? ''));
        $allowedSorts = [
            '',
            'due_asc',
            'due_desc',
            'title_asc',
            'title_desc',
            'category_asc',
            'category_desc',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            throw new \InvalidArgumentException('Invalid sort value.');
        }

        $status = trim((string)($filters['status'] ?? ''));
        if ($status !== '') {
            $status = $this->normalizeStatus($status, false);
        }

        $priority = trim((string)($filters['priority'] ?? ''));
        if ($priority !== '') {
            $priority = $this->normalizePriority($priority);
        }

        $categoryIdRaw = $filters['category_id'] ?? '';
        $categoryId = ($categoryIdRaw !== '' && $categoryIdRaw !== null)
            ? (int)$categoryIdRaw
            : null;

        $normalizedFilters = [
            'search' => trim((string)($filters['search'] ?? '')),
            'status' => $status,
            'priority' => $priority,
            'category_id' => $categoryId,
            'sort' => $sort,
            'page' => $page,
            'limit' => $limit,
        ];

        $items = $this->repository->getDashboardTasks($userId, $normalizedFilters);
        $total = $this->repository->countDashboardTasks($userId, $normalizedFilters);

        return [
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'totalPages' => (int)ceil($total / $limit),
            ],
        ];
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

        $task->description = trim($task->description);
        $task->status = $this->normalizeStatus($task->status, $isCreate);
        $task->priority = $this->normalizePriority($task->priority);
        $task->dueDate = $this->normalizeDueDate($task->dueDate);
        $task->categoryId = $this->normalizeCategoryId($task->categoryId);
    }
}
