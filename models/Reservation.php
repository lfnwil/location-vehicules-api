<?php
namespace Models;

class Reservation {
    public string $user_id;
    public string $vehicle_id;
    public \MongoDB\BSON\UTCDateTime $date_debut;
    public \MongoDB\BSON\UTCDateTime $date_fin;
    public float $prix_total;
    public string $statut;
    public $created_at;

    public function __construct(array $data) {
        $this->user_id = $data['user_id'] ?? '';
        $this->vehicle_id = $data['vehicle_id'] ?? '';
        $this->date_debut = $data['date_debut'] ?? new \MongoDB\BSON\UTCDateTime();
        $this->date_fin = $data['date_fin'] ?? new \MongoDB\BSON\UTCDateTime();
        $this->prix_total = $data['prix_total'] ?? 0;
        $this->statut = $data['statut'] ?? 'en_cours';
        $this->created_at = $data['created_at'] ?? new \MongoDB\BSON\UTCDateTime();
    }

    public function toArray() {
        return [
            'user_id' => $this->user_id,
            'vehicle_id' => $this->vehicle_id,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'prix_total' => $this->prix_total,
            'statut' => $this->statut,
            'created_at' => $this->created_at,
        ];
    }
}
