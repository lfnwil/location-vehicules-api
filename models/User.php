<?php

class User {
    public $id;
    public $username;
    public $role; // 'admin' ou 'client'

    public function isAdmin() {
        return $this->role === 'admin';
    }
}
