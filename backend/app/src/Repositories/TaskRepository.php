<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Task;

class TaskRepository extends Repository implements ITaskRepository
{
    /**
     * Get category statistics for dashboard charts.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getDashboardCategoryStats(int $userId): array
    {
        $sql = '
        SELECT
            c.id AS category_id,
            c.name AS category_name,
            COUNT(t.id) AS total,
            SUM(CASE WHEN t.status = "done" THEN 1 ELSE 0 END) AS done_count,
            SUM(CASE WHEN t.status <> "done" THEN 1 ELSE 0 END) AS pending_count
        FROM tasks t
        LEFT JOIN categories c ON c.id = t.category_id AND c.user_id = t.user_id
        WHERE t.user_id = :user_id
        GROUP BY c.id, c.name
        ORDER BY
            CASE WHEN c.name IS NULL THEN 1 ELSE 0 END,
            c.name ASC
    ';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return [
                'categoryId' => $row['category_id'] !== null ? (int)$row['category_id'] : null,
                'categoryName' => $row['category_name'] ?? 'Uncategorized',
                'total' => (int)($row['total'] ?? 0),
                'done' => (int)($row['done_count'] ?? 0),
                'pending' => (int)($row['pending_count'] ?? 0),
            ];
        }, $rows);
    }

    /**
     * Get upcoming tasks by due date for a user.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getUpcomingTasks(int $userId): array
    {
        $sql = '
        SELECT id, user_id, title, description, category_id, priority, status, due_date
        FROM tasks
        WHERE user_id = :user_id
          AND due_date IS NOT NULL
          AND due_date <> ""
          AND status NOT IN ("done", "completed", "cancelled")
        ORDER BY due_date ASC
    ';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $now = new \DateTime();
        $todayStart = (clone $now)->setTime(0, 0, 0);
        $todayEnd = (clone $now)->setTime(23, 59, 59);
        $weekEnd = (clone $todayEnd)->modify('+7 days');

        $result = [
            'overdue' => [],
            'today' => [],
            'week' => [],
        ];

        foreach ($rows as $row) {
            try {
                $task = new Task($row);
                $dueDate = new \DateTime($task->dueDate);

                if ($dueDate < $todayStart) {
                    $result['overdue'][] = $task;
                    continue;
                }

                if ($dueDate >= $todayStart && $dueDate <= $todayEnd) {
                    $result['today'][] = $task;
                    continue;
                }

                if ($dueDate > $todayEnd && $dueDate <= $weekEnd) {
                    $result['week'][] = $task;
                }
            } catch (\Exception $e) {
                // ignore malformed dates
            }
        }

        return $result;
    }

    /**
     * Get a task by id ensuring it belongs to the user.
     *
     * @param int $id
     * @param int $userId
     *
     * @return Task|null
     */
    public function getByIdAndUserId(int $id, int $userId): ?Task
    {
        $sql = 'SELECT id, user_id, title, description, category_id, priority, status, due_date
            FROM tasks
            WHERE id = :id AND user_id = :user_id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ? new Task($row) : null;
    }

    /**
     * Get overall task statistics for dashboard.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return array
     */
    public function getDashboardStats(int $userId, array $filters = []): array
    {
        $sql = '
        SELECT id, status, due_date
        FROM tasks
        WHERE user_id = :user_id
    ';

        $params = [
            ':user_id' => $userId,
        ];

        if (!empty($filters['search'])) {
            $sql .= ' AND title LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= ' AND priority = :priority';
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        }

        $stmt = $this->getConnection()->prepare($sql);

        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $now = new \DateTime();

        $totalTasks = count($rows);
        $done = 0;
        $pending = 0;
        $overdue = 0;

        foreach ($rows as $row) {
            $status = strtolower((string)($row['status'] ?? ''));

            $isDone = in_array($status, ['done', 'completed', '3'], true);

            if ($isDone) {
                $done++;
            } else {
                $pending++;
            }

            if (!$isDone && !empty($row['due_date'])) {
                try {
                    $dueDate = new \DateTime($row['due_date']);
                    if ($dueDate < $now) {
                        $overdue++;
                    }
                } catch (\Exception $e) {
                    // ignore invalid dates in stats
                }
            }
        }

        return [
            'totalTasks' => $totalTasks,
            'done' => $done,
            'pending' => $pending,
            'overdue' => $overdue,
        ];
    }

    /**
     * Get all tasks for a user.
     *
     * @param int $userId
     *
     * @return Task[]
     */
    public function getByUserId(int $userId): array
    {
        $sql = 'SELECT id, user_id, title, description, category_id, priority, status, due_date
                FROM tasks
                WHERE user_id = :user_id
                ORDER BY due_date ASC';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Task($row), $rows);
    }

    /**
     * Get dashboard tasks with filters and pagination.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return Task[]
     */
    public function getDashboardTasks(int $userId, array $filters): array
    {
        $sql = '
            SELECT id, user_id, title, description, category_id, priority, status, due_date
            FROM tasks
            WHERE user_id = :user_id
        ';

        $params = [
            ':user_id' => $userId,
        ];

        if (!empty($filters['search'])) {
            $sql .= ' AND title LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= ' AND priority = :priority';
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        }

        $sortMap = [
            'due_asc' => 'due_date ASC',
            'due_desc' => 'due_date DESC',
            'title_asc' => 'title ASC',
            'title_desc' => 'title DESC',
            'category_asc' => 'category_id ASC',
            'category_desc' => 'category_id DESC',
        ];

        $orderBy = $sortMap[$filters['sort']] ?? 'due_date ASC';
        $sql .= " ORDER BY {$orderBy}";

        $limit = (int)$filters['limit'];
        $offset = ((int)$filters['page'] - 1) * $limit;

        $sql .= ' LIMIT :limit OFFSET :offset';

        $stmt = $this->getConnection()->prepare($sql);

        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => new Task($row), $rows);
    }

    /**
     * Count tasks for dashboard pagination.
     *
     * @param int   $userId
     * @param array $filters
     *
     * @return int
     */
    public function countDashboardTasks(int $userId, array $filters): int
    {
        $sql = 'SELECT COUNT(*) FROM tasks WHERE user_id = :user_id';
        $params = [
            ':user_id' => $userId,
        ];

        if (!empty($filters['search'])) {
            $sql .= ' AND title LIKE :search';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= ' AND priority = :priority';
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND category_id = :category_id';
            $params[':category_id'] = (int)$filters['category_id'];
        }

        $stmt = $this->getConnection()->prepare($sql);

        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue($key, $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    /**
     * Persist a new task.
     *
     * @param Task $task
     *
     * @return Task
     */
    public function create(Task $task): Task
    {
        $sql = 'INSERT INTO tasks (user_id, title, description, category_id, priority, status, due_date)
                VALUES (:user_id, :title, :description, :category_id, :priority, :status, :due_date)';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([
            ':user_id' => $task->userId,
            ':title' => $task->title,
            ':description' => $task->description,
            ':category_id' => $task->categoryId,
            ':priority' => $task->priority,
            ':status' => $task->status,
            ':due_date' => $task->dueDate,
        ]);

        $task->id = (int)$this->getConnection()->lastInsertId();

        return $task;
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     *
     * @return bool
     */
    public function update(Task $task): bool
    {
        $sql = 'UPDATE tasks
                SET title = :title,
                    description = :description,
                    category_id = :category_id,
                    priority = :priority,
                    status = :status,
                    due_date = :due_date
                WHERE id = :id AND user_id = :user_id';

        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->execute([
            ':id' => $task->id,
            ':user_id' => $task->userId,
            ':title' => $task->title,
            ':description' => $task->description,
            ':category_id' => $task->categoryId,
            ':priority' => $task->priority,
            ':status' => $task->status,
            ':due_date' => $task->dueDate,
        ]);
    }

    /**
     * Delete a task for a user by id.
     *
     * @param int $id
     * @param int $userId
     *
     * @return void
     */
    public function delete(int $id, int $userId): void
    {
        $sql = 'DELETE FROM tasks WHERE id = :id AND user_id = :user_id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
    }
}

