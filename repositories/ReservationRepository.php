<?php
namespace Repositories;

use MongoDB\Database;
use MongoDB\BSON\ObjectId;

class ReservationRepository {
    
    private $collection;
    private $collectionVehicles;
    private $collectionUsers;

    public function __construct(Database $db) {
        $this->collection = $db->reservations;
        $this->collectionVehicles = $db->vehicles;
        $this->collectionUsers = $db->users;
    }

    public function getAll() {
        return $this->collection->find()->toArray();
    }

    public function getById(string $id) {
        return $this->collection->findOne(["_id" => new ObjectId($id)]);
    }

    public function createReservation(array $data) {

        $vehicle = $this->collectionVehicles->findOne([
            "_id" => new ObjectId($data["vehicule_id"])
        ]);

        if (!$vehicle) {
            return ["error" => "Véhicule introuvable"];
        }

        $user = $this->collectionUsers->findOne([
            "_id" => new ObjectId($data["user_id"])
        ]);

        if (!$user) {
            return ["error" => "User introuvable"];
        }

        $dateDebut = new \DateTime($data["date_debut"]);
        $dateFin   = new \DateTime($data["date_fin"]);
        $diff = $dateDebut->diff($dateFin)->days;

        if ($diff <= 0) {
            return ["error" => "La date de fin doit être supérieure à la date de début"];
        }

        $prixJournalier = $vehicle->prix_journalier;
        $prixTotal = $diff * $prixJournalier;

        $reservation = [
            "vehicule_id" => new ObjectId($data["vehicule_id"]),
            "user_id" => new ObjectId($data["user_id"]),
            "date_debut" => $data["date_debut"],
            "date_fin" => $data["date_fin"],
            "prix_total" => $prixTotal,
            "created_at" => new \MongoDB\BSON\UTCDateTime()
        ];

        $result = $this->collection->insertOne($reservation);

        return [
            "message" => "Réservation créée",
            "id" => (string)$result->getInsertedId(),
            "prix_total" => $prixTotal
        ];
    }

    public function delete(string $id) {
        $result = $this->collection->deleteOne([
            "_id" => new ObjectId($id)
        ]);

        return $result->getDeletedCount() > 0;
    }
}
