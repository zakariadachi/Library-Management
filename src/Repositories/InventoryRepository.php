<?php

namespace Src\Repositories;

use PDO;

class InventoryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAvailableCopies(string $isbn, int $branchId): int
    {
        $stmt = $this->db->prepare("
            SELECT available_copies 
            FROM inventory 
            WHERE book_isbn = :isbn AND branch_id = :branch_id
        ");
        $stmt->execute(['isbn' => $isbn, 'branch_id' => $branchId]);
        $result = $stmt->fetchColumn();
        
        return $result !== false ? (int)$result : 0;
    }

    public function updateAvailableCopies(string $isbn, int $branchId, int $change): void
    {
        $stmt = $this->db->prepare("
            UPDATE inventory 
            SET available_copies = available_copies + :change 
            WHERE book_isbn = :isbn AND branch_id = :branch_id
        ");
        $stmt->execute([
            'change' => $change,
            'isbn' => $isbn,
            'branch_id' => $branchId
        ]);
    }

    public function getTotalAvailableAcrossBranches(string $isbn): int
    {
        $stmt = $this->db->prepare("
            SELECT SUM(available_copies) 
            FROM inventory 
            WHERE book_isbn = :isbn
        ");
        $stmt->execute(['isbn' => $isbn]);
        $result = $stmt->fetchColumn();
        
        return $result !== false ? (int)$result : 0;
    }
}
