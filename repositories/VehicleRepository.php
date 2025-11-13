<?php
namespace Repositories;

use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\Driver\Exception\InvalidArgumentException;

class VehicleRepository {
    private Database $db;
    private $collection;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->collection = $this->db->vehicules;
    }

    private function parseId(string $id): ObjectId {
        $id = trim($id);
        if (!preg_match('/^[a-f0-9]{24}$/i', $id)) {
            throw new InvalidArgumentException("ID invalide");
        }
        return new ObjectId($id);
    }

    public function getAll(): array {
        return $this->collection->find()->toArray();
    }

    public function getById(string $id): ?array {
        $oid = $this->parseId($id);
        return $this->collection->findOne(['_id' => $oid])?->getArrayCopy() ?? null;
    }

    public function create(array $data): string {
        $result = $this->collection->insertOne($data);
        return (string) $result->getInsertedId();
    }

    public function update(string $id, array $data): bool {
        $oid = $this->parseId($id);
        $result = $this->collection->updateOne(['_id' => $oid], ['$set' => $data]);
        return $result->getModifiedCount() > 0;
    }

    public function delete(string $id): bool {
        $oid = $this->parseId($id);
        $result = $this->collection->deleteOne(['_id' => $oid]);
        return $result->getDeletedCount() > 0;
    }
}
