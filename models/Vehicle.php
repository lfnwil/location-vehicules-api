<?php
namespace Models;

class Vehicle {
    public $type;
    public $marque;
    public $modele;
    public $kilometrage;
    public $prix_journalier;
    public $disponibilite;
    public $created_at;

    public function __construct(array $data) {
        $this->type = $data['type'] ?? '';
        $this->marque = $data['marque'] ?? '';
        $this->modele = $data['modele'] ?? '';
        $this->kilometrage = $data['kilometrage'] ?? 0;
        $this->prix_journalier = $data['prix_journalier'] ?? 0;
        $this->disponibilite = $data['disponibilite'] ?? true;
        $this->created_at = $data['created_at'] ?? new \MongoDB\BSON\UTCDateTime();
    }

    public function toArray() {
        return [
            'type' => $this->type,
            'marque' => $this->marque,
            'modele' => $this->modele,
            'kilometrage' => $this->kilometrage,
            'prix_journalier' => $this->prix_journalier,
            'disponibilite' => $this->disponibilite,
            'created_at' => $this->created_at
        ];
    }
}
