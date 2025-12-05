<?php
namespace Services;

use Repositories\UserRepository;

class UserService {
    private UserRepository $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function getAllUsers(): array {
        return $this->repository->getAll();
    }

    public function getUserById(string $id): ?array {
        return $this->repository->getById($id);
    }

    public function createUser(array $data): string {
        return $this->repository->create($data);
    }

    public function updateUser(string $id, array $data): bool {
        return $this->repository->update($id, $data);
    }

    public function deleteUser(string $id): bool {
        return $this->repository->delete($id);
    }
}
