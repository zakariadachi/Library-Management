<?php

namespace Src\Repositories;

use Src\Models\Book;
use PDO;

class BookRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function findByIsbn(string $isbn): ?Book
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE isbn = :isbn");
        $stmt->execute(['isbn' => $isbn]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return new Book(
            $data['isbn'],
            $data['title'],
            (int)$data['publication_year'],
            (int)$data['category_id'],
            $data['status']
        );
    }

    public function updateStatus(string $isbn, string $status): void
    {
        $stmt = $this->db->prepare("UPDATE books SET status = :status WHERE isbn = :isbn");
        $stmt->execute(['status' => $status, 'isbn' => $isbn]);
    }
}
