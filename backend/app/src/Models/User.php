<?php

namespace App\Models;

class User
{
    public ?int $id;
    public string $name;
    public string $email;
    public string $password;

//    public function __construct(array $data = [])
//    {
//        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
//        $this->name = $data['name'] ?? '';
//        $this->email = $data['email'] ?? '';
//        $this->password = $data['password'] ?? '';
//    }
    /**
     * @param int|null $id
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(?int $id, string $name, string $email, string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
}