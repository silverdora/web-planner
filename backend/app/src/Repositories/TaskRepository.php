<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Task;

class TaskRepository extends Repository implements ITaskRepository
{
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

        $tasks = [];
        foreach ($rows as $row) {
            $tasks[] = new Task($row);
        }

        return $tasks;
    }

    /**
     * @return Task[]
     */
    public function getAll(): array
    {
        $sql = 'SELECT id, user_id, title, description, category_id, priority, status, due_date FROM tasks';
        $result = $this->getConnection()->query($sql);

        return $result->fetchAll(\PDO::FETCH_CLASS, '\App\Models\Task');
    }

    public function getById(int $id): ?Task
    {
        $sql = 'SELECT id, user_id, title, description, category_id, priority, status, due_date FROM tasks WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, '\App\Models\Task');
        $task = $stmt->fetch();

        return $task ?: null;
    }

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

    public function update(Task $task): bool
    {
    $sql = 'UPDATE tasks
            SET title = :title,
                description = :description,
                category_id = :category_id,
                priority = :priority,
                status = :status,
                due_date = :due_date
            WHERE id = :id';

    $stmt = $this->getConnection()->prepare($sql);

    return $stmt->execute([
        ':id' => $task->id,
        ':title' => $task->title,
        ':description' => $task->description,
        ':category_id' => $task->categoryId,
        ':priority' => $task->priority,
        ':status' => $task->status,
        ':due_date' => $task->dueDate,
    ]);
}

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}