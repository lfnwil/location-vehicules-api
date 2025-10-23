<?php
namespace Repositories;

use MongoDB\BSON\ObjectId;

class VehicleRepository {
    private $collection;

    public function __construct($db) {
        $this->collection = $db->vehicules;
    }

    public function create(array $data) {
        $result = $this->collection->insertOne($data);
        return $result->getInsertedId();
    }

    public function getAll(): array {
        return $this->collection->find()->toArray();
    }

    public function getById(string $id): ?array {
        $result = $this->collection->findOne(['_id' => new ObjectId($id)]);
        return $result ? $result->getArrayCopy() : null;
    }

    public function update(string $id, array $data) {
        $this->collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $data]);
    }

    public function delete(string $id) {
        $this->collection->deleteOne(['_id' => new ObjectId($id)]);
    }
}
