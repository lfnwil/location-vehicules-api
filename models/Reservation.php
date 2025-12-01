<?php
namespace Models;

class Reservation {
    public ?string $id = null;
    public string $user_id;
    public string $vehicle_id;
    public string $start_date;
    public string $end_date;
    public string $status = 'pending';
    public string $created_at;

    public function __construct(array $data) {
        $this->id = $data['_id'] ?? null;
        $this->user_id = $data['user_id'];
        $this->vehicle_id = $data['vehicle_id'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
        $this->status = $data['status'] ?? 'pending';
        $this->created_at = $data['created_at'] ?? new \MongoDB\BSON\UTCDateTime();
    }
}
