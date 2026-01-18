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
            $reservations[] = new Reservation(
                $row['id'],
                $row['member_id'],
                $row['book_isbn'],
                $row['reservation_date'],
                $row['status']
            );
        }
        return $reservations;
    }
}
