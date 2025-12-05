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

        // ⚠️ Si un mot de passe brut est fourni → on le hash
        if (isset($data['password'])) {
            // Ne re-hash pas un mot de passe déjà hashé
            if ($this->isHashed($data['password'])) {
                $this->password = $data['password'];
            } else {
                $this->password = password_hash($data['password'], PASSWORD_DEFAULT);
            }
        } else {
            $this->password = '';
        }

        $this->role = $data['role'] ?? 'user';
        $this->created_at = $data['created_at'] ?? new \MongoDB\BSON\UTCDateTime();
    }

    // Vérifier si une chaîne ressemble déjà à un hash
    private function isHashed($password) {
        return preg_match('/^\$2y\$/', $password);
    }
}
