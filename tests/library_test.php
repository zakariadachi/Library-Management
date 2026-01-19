<?php

use Src\Repositories\DatabaseConnection;
use Src\Repositories\BookRepository;
use Src\Repositories\MemberRepository;
use Src\Repositories\BorrowRepository;
use Src\Repositories\ReservationRepository;
use Src\Repositories\InventoryRepository;
use Src\Services\LibraryService;
use Src\Exceptions\LateFeeException;
use Src\Exceptions\BookUnavailableException;
use Src\Exceptions\MemberLimitExceededException;

require_once __DIR__ . '/../src/Repositories/DatabaseConnection.php';
require_once __DIR__ . '/../src/Models/Member.php';
require_once __DIR__ . '/../src/Models/StudentMember.php';
require_once __DIR__ . '/../src/Models/FacultyMember.php';
require_once __DIR__ . '/../src/Models/Book.php';
require_once __DIR__ . '/../src/Models/BorrowRecord.php';
require_once __DIR__ . '/../src/Models/Reservation.php';
require_once __DIR__ . '/../src/Repositories/BookRepository.php';
require_once __DIR__ . '/../src/Repositories/MemberRepository.php';
require_once __DIR__ . '/../src/Repositories/BorrowRepository.php';
require_once __DIR__ . '/../src/Repositories/ReservationRepository.php';
require_once __DIR__ . '/../src/Repositories/InventoryRepository.php';
require_once __DIR__ . '/../src/Services/LibraryService.php';
require_once __DIR__ . '/../src/Exceptions/BookUnavailableException.php';
require_once __DIR__ . '/../src/Exceptions/MemberLimitExceededException.php';
require_once __DIR__ . '/../src/Exceptions/LateFeeException.php';

echo "==============================================\n";
echo "  LIBRARY MANAGEMENT SYSTEM - COMPREHENSIVE TEST\n";
echo "==============================================\n\n";


try {
    // Setup Service
    $bookRepo = new BookRepository();
    $memberRepo = new MemberRepository();
    $borrowRepo = new BorrowRepository();
    $reservationRepo = new ReservationRepository();
    $inventoryRepo = new InventoryRepository();
    $service = new LibraryService($bookRepo, $memberRepo, $borrowRepo, $reservationRepo, $inventoryRepo);

    // Internal Reset - Clean up data from previous runs without reset_db.php
    $db = DatabaseConnection::getInstance()->getConnection();
    $db->exec("DELETE FROM borrow_records");
    $db->exec("DELETE FROM reservations");
    $db->exec("UPDATE inventory SET available_copies = total_copies");
    $db->exec("UPDATE books SET status = 'Available'");
    $db->exec("UPDATE members SET unpaid_fees = 0 WHERE id != 'S1003'");
    
    // Set Harry Potter to Checked Out (0 available copies) to test reservation
    $db->exec("UPDATE inventory SET available_copies = 0 WHERE book_isbn = '9780747532743'");
    $db->exec("UPDATE books SET status = 'Checked Out' WHERE isbn = '9780747532743'");
    
    // Ensure "The Great Gatsby" is available in Branch 1 for Test 4
    $db->exec("INSERT IGNORE INTO inventory (book_isbn, branch_id, total_copies, available_copies) VALUES ('9780684801520', 1, 1, 1)");
    // Ensure "Principia Mathematica" is available in Branch 1 for Test 4 (limit test)
    $db->exec("INSERT IGNORE INTO inventory (book_isbn, branch_id, total_copies, available_copies) VALUES ('9780140449105', 1, 1, 1)");

    // Test Data IDs
    $studentId = 'S1001';
    $studentWithFeesId = 'S1003';
    $facultyId = 'F2001';
    $availableBookIsbn = '9780132350884';
    $checkedOutBookIsbn = '9780747532743';
    $branchId = 1;

    // TEST 1: Book Search
    echo "TEST 1: Book Search (Keyword: 'Clean')\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $books = $bookRepo->searchBooks('Clean');
        if (count($books) > 0) {
            foreach ($books as $book) {
                echo "  Found: {$book['title']}\n";
                echo "  ISBN: {$book['isbn']}\n";
                echo "  Authors: {$book['authors']}\n";
                echo "  Category: {$book['category_name']}\n";
                echo "  Status: {$book['status']}\n";
            }
        } else {
            echo "No books found\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 2: Late Fee Blocking
    echo "TEST 2: Late Fee Blocking (Member with fees > \$10)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $service->borrowBook($studentWithFeesId, $availableBookIsbn, $branchId);
        echo "FAILED: Should have been blocked\n";
    } catch (LateFeeException $e) {
        echo " PASSED: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 3: Valid Borrow (Student without debts)
    echo "TEST 3: Valid Borrow (Student without debts)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $service->borrowBook($studentId, $availableBookIsbn, $branchId);
        echo " PASSED: Book borrowed successfully\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 4: Borrow Limit Exceeded
    echo "TEST 4: Borrow Limit (Student max 3 books)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        // Borrow 2 more books to reach limit
        $service->borrowBook($studentId, '9780201633610', $branchId);
        $service->borrowBook($studentId, '9780684801520', $branchId);
        
        // Try to borrow 4th book
        $service->borrowBook($studentId, '9780140449105', $branchId);
        echo "FAILED: Should have been blocked at 3 books\n";
    } catch (MemberLimitExceededException $e) {
        echo " PASSED: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 5: Book Unavailable
    echo "TEST 5: Book Unavailable (Checked Out)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $service->borrowBook($facultyId, $checkedOutBookIsbn, $branchId);
        echo "FAILED: Should have been blocked\n";
    } catch (BookUnavailableException $e) {
        echo " PASSED: " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 6: Return Book with Late Fee
    echo "TEST 6: Return Book (Simulated Late Return)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        // Get the first active borrow for student
        $activeBorrows = $borrowRepo->findActiveByMemberId($studentId);
        if (count($activeBorrows) > 0) {
            $borrowId = $activeBorrows[0]->getId();
            $service->returnBook($borrowId);
            echo " PASSED: Book returned\n";
        } else {
            echo "No active borrows to return\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 7: Valid Reservation
    echo "TEST 7: Valid Reservation (Unavailable Book)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $service->reserveBook($studentId, $checkedOutBookIsbn);
        echo " PASSED: Book reserved successfully\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 8: Duplicate Reservation
    echo "TEST 8: Duplicate Reservation (Already Reserved)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $service->reserveBook($studentId, $checkedOutBookIsbn);
        echo "FAILED: Should have been blocked\n";
    } catch (Exception $e) {
        echo " PASSED: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 9: Faculty Borrowing (Higher Limits)
    echo "TEST 9: Faculty Borrowing (Higher Limits)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $service->borrowBook($facultyId, '9780201633610', $branchId);
        echo " PASSED: Faculty borrowed successfully\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 10: Renew Book
    echo "TEST 10: Renew Book (Extend Due Date)\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $activeBorrows = $borrowRepo->findActiveByMemberId($facultyId);
        if (count($activeBorrows) > 0) {
            $borrowId = $activeBorrows[0]->getId();
            $service->renewBook($borrowId);
            echo " PASSED: Book renewed successfully\n";
        } else {
            echo "No active borrows to renew\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // TEST 11: Search by Author
    echo "TEST 11: Search by Author (Keyword: 'Martin')\n";
    echo str_repeat("-", 46) . "\n";
    try {
        $books = $bookRepo->searchBooks('Martin');
        if (count($books) > 0) {
            foreach ($books as $book) {
                echo " Found: {$book['title']} by {$book['authors']}\n";
            }
        } else {
            echo "No books found\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";

    echo "  ALL TESTS COMPLETED\n";

} catch (Throwable $e) {
    echo "\n!!! CRITICAL ERROR !!!\n";
    echo "Message: " . $e->getMessage() . "\n";
}