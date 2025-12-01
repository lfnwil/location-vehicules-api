<?php
namespace Repositories;

use MongoDB\Database;
use MongoDB\BSON\ObjectId;
use Models\Reservation;

class ReservationRepository {
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    private function parseId(string $id): ObjectId {
        try {
            return new ObjectId(trim($id));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("ID invalide pour MongoDB");
        }
    }

    public function create(array $data): string {
        $result = $this->db->reservations->insertOne($data);
        return (string)$result->getInsertedId();
    }

    public function findAll(): array {
        return $this->db->reservations->find()->toArray();
    }

    public function getById(string $id): ?array {
        $objId = $this->parseId($id);
        $reservation = $this->db->reservations->findOne(['_id' => $objId]);
        return $reservation ? (array)$reservation : null;
    }

    public function update(string $id, array $data): bool {
        $objId = $this->parseId($id);
        $result = $this->db->reservations->updateOne(['_id' => $objId], ['$set' => $data]);
        return $result->getModifiedCount() > 0;
    }

    public function delete(string $id): bool {
        $objId = $this->parseId($id);
        $result = $this->db->reservations->deleteOne(['_id' => $objId]);
        return $result->getDeletedCount() > 0;
    }
}
