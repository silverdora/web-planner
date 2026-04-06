<?php

namespace App\Models;

class Category
{
    public ?int $id;
    public ?int $userId;
    public string $name;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        $this->userId = isset($data['user_id'])
            ? (int)$data['user_id']
            : (isset($data['userId']) ? (int)$data['userId'] : null);
        $this->name = trim($data['name'] ?? '');
    }
}