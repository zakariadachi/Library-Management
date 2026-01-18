<?php

namespace Src\Repositories;

use Src\Models\Member;
use Src\Models\StudentMember;
use Src\Models\FacultyMember;
use PDO;

class MemberRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function findById(string $id): ?Member
    {
        $stmt = $this->db->prepare("SELECT * FROM members WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        // Factory logic based on 'type' column
        if ($data['type'] === 'Student') {
            return new StudentMember(
                $data['id'],
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['expiry_date'],
                (float)$data['unpaid_fees']
            );
        } elseif ($data['type'] === 'Faculty') {
            return new FacultyMember(
                $data['id'],
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['expiry_date'],
                (float)$data['unpaid_fees']
            );
        }

        return null;
    }

    public function updateUnpaidFees(string $id, float $amount): void
    {
        $stmt = $this->db->prepare("UPDATE members SET unpaid_fees = :amount WHERE id = :id");
        $stmt->execute(['amount' => $amount, 'id' => $id]);
    }
}
