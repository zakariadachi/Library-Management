<?php

namespace Src\Repositories;

use Src\Models\Reservation;
use PDO;

class ReservationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function create(Reservation $reservation): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO reservations (member_id, book_isbn, reservation_date, status)
            VALUES (:member_id, :book_isbn, :reservation_date, :status)
        ");
        $stmt->execute([
            'member_id' => $reservation->getMemberId(),
            'book_isbn' => $reservation->getBookIsbn(),
            'reservation_date' => $reservation->getReservationDate(),
            'status' => $reservation->getStatus()
        ]);
    }

    public function findPendingByIsbn(string $isbn): array
    {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE book_isbn = :isbn AND status = 'Pending' ORDER BY reservation_date ASC");
        $stmt->execute(['isbn' => $isbn]);
        $rows = $stmt->fetchAll();

        $reservations = [];
        foreach ($rows as $row) {
            $reservations[] = $this->mapToModel($row);
        }
        return $reservations;
    }

    public function findByMember(string $memberId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE member_id = :member_id ORDER BY reservation_date DESC");
        $stmt->execute(['member_id' => $memberId]);
        $rows = $stmt->fetchAll();

        $reservations = [];
        foreach ($rows as $row) {
            $reservations[] = $this->mapToModel($row);
        }
        return $reservations;
    }

    private function mapToModel(array $row): Reservation
    {
        return new Reservation(
            $row['id'],
            $row['member_id'],
            $row['book_isbn'],
            $row['reservation_date'],
            $row['status']
        );
    }
}
