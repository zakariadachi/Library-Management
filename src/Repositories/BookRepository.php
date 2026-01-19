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

    public function searchBooks(string $term): array
    {
        $sql = "
            SELECT 
                b.*,
                c.name AS category_name,
                GROUP_CONCAT(a.name) AS authors
            FROM books b
            JOIN categories c ON b.category_id = c.id
            JOIN book_authors ba ON b.isbn = ba.book_isbn
            JOIN authors a ON ba.author_id = a.id
            WHERE b.title LIKE :term
               OR a.name LIKE :term
               OR c.name LIKE :term
            GROUP BY b.isbn
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':term' => '%' . $term . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(string $isbn, string $status): void
    {
        $stmt = $this->db->prepare("UPDATE books SET status = :status WHERE isbn = :isbn");
        $stmt->execute(['status' => $status, 'isbn' => $isbn]);
    }
}
