<?php
namespace Repositories;

use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use Models\User;
use InvalidArgumentException;

class UserRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    private function parseId(string $id): ObjectId {
        $id = trim($id);
        if (!preg_match('/^[a-f0-9]{24}$/', $id)) {
            throw new InvalidArgumentException('ID invalide pour MongoDB');
        }
        return new ObjectId($id);
    }

    public function create(array $data): string {
        $data['created_at'] = new \MongoDB\BSON\UTCDateTime();
        $result = $this->db->users->insertOne($data);
        return (string)$result->getInsertedId();
    }

    public function getAll(): array {
        return $this->db->users->find()->toArray();
    }

    public function getById(string $id): ?array {
        return $this->db->users->findOne(['_id' => $this->parseId($id)])?->getArrayCopy();
    }

    public function update(string $id, array $data) {
        $this->db->users->updateOne(['_id' => $this->parseId($id)], ['$set' => $data]);
    }

    public function delete(string $id) {
        $this->db->users->deleteOne(['_id' => $this->parseId($id)]);
    }
}
