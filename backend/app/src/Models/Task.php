<?php

namespace App\Models;

class Task
{
    public ?int $id;
    public ?int $userId;
    public string $title;
    public string $description;
    public ?int $categoryId;
    public string $priority;
    public string $status;
    public string $dueDate; // YYYY-MM-DD HH:MM:SS

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        $this->userId = isset($data['user_id']) ? (int)$data['user_id'] : (isset($data['userId']) ? (int)$data['userId'] : null);
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->categoryId = isset($data['category_id']) ? (int)$data['category_id'] : (isset($data['categoryId']) ? (int)$data['categoryId'] : null);
        $this->priority = $data['priority'] ?? '';
        $this->status = $data['status'] ?? '';
        $this->dueDate = $data['due_date'] ?? $data['dueDate'] ?? '';
    }
}
