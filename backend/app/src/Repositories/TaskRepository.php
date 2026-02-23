<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Task;
use App\Utils\JsonStore;

class TaskRepository implements ITaskRepository
{
    // private JsonStore $store;
    // private const DATA_FILE = __DIR__ . '/../data/articles.json';

    // public function __construct()
    // {
    //     $this->store = new JsonStore(self::DATA_FILE, Task::class);
    // }

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
        $sql = 'SELECT id, user_id, title, description, category_id, priority, status, due_date FROM task WHERE id = :id'

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue('id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, '\App\Models\Task');
        $salon = $stmt->fetch();

        return $task ?: null;
    }

    public function create(Task $task): Task
    {
        $sql = 'INSERT INTO tasks (id, user_id, title, description, category_id, priority, status, due_date)
            VALUES (:id, :user_id, :title, :description, :category_id, :priority, :status, :due_date)';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([
            ':id' => $task->id,
            ':user_id' => $task->userId,
            ':title' => $task->title,
            ':description' => $task->description,
            ':category_id' => $task->categoryId,
            ':priority' => $task->priority,
            ':status' => $task->status,
            ':due_date' => $task->dueDate,
        ]);

        return (int)$this->getConnection()->lastInsertId();
    }

    public function update(Task $task): bool
    {
        $sql = 'UPDATE tasks
            SET title = :title,
            description = :description,
            category_id = :category_id,
            priority = :priority,
            status = :status,
            due_date = :due_date,
            WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':title' => $task->title,
            ':description' => $task->description,
            ':category_id' => $task->categoryId,
            ':priority' => $task->priority,
            ':due_date' => $task->dueDate,
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}