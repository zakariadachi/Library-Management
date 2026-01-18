<?php

namespace Src\Repositories;

use Src\Models\BorrowRecord;
use PDO;

class BorrowRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function create(BorrowRecord $record): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO borrow_records 
            (member_id, book_isbn, branch_id, borrow_date, due_date, late_fee) 
            VALUES 
            (:member_id, :book_isbn, :branch_id, :borrow_date, :due_date, :late_fee)
        ");
        
        $stmt->execute([
            'member_id' => $record->getMemberId(),
            'book_isbn' => $record->getBookIsbn(),
            'branch_id' => $record->getBranchId(),
            'borrow_date' => $record->getBorrowDate(),
            'due_date' => $record->getDueDate(),
            'late_fee' => $record->getLateFee()
        ]);
    }

    public function findActiveByMemberId(string $memberId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM borrow_records WHERE member_id = :member_id AND return_date IS NULL");
        $stmt->execute(['member_id' => $memberId]);
        $results = $stmt->fetchAll();

        $records = [];
        foreach ($results as $row) {
            $records[] = $this->mapToModel($row);
        }
        return $records;
    }

    public function findById(int $id): ?BorrowRecord
    {
        $stmt = $this->db->prepare("SELECT * FROM borrow_records WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        
        return $row ? $this->mapToModel($row) : null;
    }

    public function updateReturn(int $id, string $returnDate, float $lateFee): void
    {
        $stmt = $this->db->prepare("UPDATE borrow_records SET return_date = :return_date, late_fee = :late_fee WHERE id = :id");
        $stmt->execute([
            'return_date' => $returnDate,
            'late_fee' => $lateFee,
            'id' => $id
        ]);
    }

    private function mapToModel(array $row): BorrowRecord
    {
        return new BorrowRecord(
            (int)$row['id'],
            $row['member_id'],
            $row['book_isbn'],
            (int)$row['branch_id'],
            $row['borrow_date'],
            $row['due_date'],
            $row['return_date'],
            (float)$row['late_fee']
        );
    }
    
    public function getActiveBorrowCount(string $memberId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM borrow_records WHERE member_id = :member_id AND return_date IS NULL");
        $stmt->execute(['member_id' => $memberId]);
        return (int)$stmt->fetchColumn();
    }
}
