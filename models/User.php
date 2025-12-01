<?php
namespace Models;

class User {
    public ?string $id = null;
    public string $name;
    public string $email;
    public string $password;
    public string $role = 'user';
    public ?\DateTime $created_at = null;
}
