<?php
namespace Services;

use Repositories\ReservationRepository;

class ReservationService {

    private ReservationRepository $repository;

    public function __construct(ReservationRepository $repository) {
        $this->repository = $repository;
    }

    public function getAll() {
        return $this->repository->getAll();
    }

    public function getById(string $id) {
        return $this->repository->getById($id);
    }

    public function createReservation(array $data) {
        return $this->repository->createReservation($data);
    }

    public function deleteReservation(string $id) {
        return $this->repository->delete($id);
    }
}
