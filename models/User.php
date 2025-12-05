<?php
namespace Models;

class User {
    public string $name;
    public string $email;
    public string $password; 
    public string $role; // 'user' ou 'admin'
    public $created_at;

    public function __construct(array $data) {
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->role = $data['role'] ?? 'user';
        $this->created_at = $data['created_at'] ?? new \MongoDB\BSON\UTCDateTime();
    }
}
