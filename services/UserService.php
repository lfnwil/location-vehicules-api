<?php
namespace Services;

use Repositories\UserRepository;

class UserService {
    private UserRepository $repo;

    public function __construct(UserRepository $repo) {
        $this->repo = $repo;
    }

    public function createUser(array $data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->repo->create($data);
    }

    public function getAllUsers() {
        return $this->repo->getAll();
    }

    public function getUserById(string $id) {
        return $this->repo->getById($id);
    }

    public function updateUser(string $id, array $data) {
        if(isset($data['password'])){
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $this->repo->update($id, $data);
    }

    public function deleteUser(string $id) {
        $this->repo->delete($id);
    }
}
