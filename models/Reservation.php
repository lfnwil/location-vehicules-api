<?php
namespace Models;

class Reservation {
    public string $vehicule_id;
    public string $user_id;
    public string $date_debut;
    public string $date_fin;
    public float $prix_total;
    public $created_at;

    public function __construct(array $data) {
        $this->vehicule_id = $data['vehicule_id'] ?? '';
        $this->user_id = $data['user_id'] ?? '';
        $this->date_debut = $data['date_debut'] ?? '';
        $this->date_fin = $data['date_fin'] ?? '';
        $this->prix_total = $data['prix_total'] ?? 0;
        $this->created_at = $data['created_at'] ?? new \MongoDB\BSON\UTCDateTime();
    }

    public function toArray() {
        return [
            "vehicule_id" => $this->vehicule_id,
            "user_id"     => $this->user_id,
            "date_debut"  => $this->date_debut,
            "date_fin"    => $this->date_fin,
            "prix_total"  => $this->prix_total,
            "created_at"  => $this->created_at
        ];
    }
}
