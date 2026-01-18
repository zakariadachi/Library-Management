<?php

namespace Src\Repositories;

use Src\Models\LibraryBranch;
use PDO;

class BranchRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function findById(int $id): ?LibraryBranch
    {
        $stmt = $this->db->prepare("SELECT * FROM library_branches WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new LibraryBranch(
            $row['id'],
            $row['name'],
            $row['location'],
            $row['contact_info']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM library_branches");
        $results = $stmt->fetchAll();

        $branches = [];
        foreach ($results as $row) {
            $branches[] = new LibraryBranch(
                $row['id'],
                $row['name'],
                $row['location'],
                $row['contact_info']
            );
        }
        return $branches;
    }
}
