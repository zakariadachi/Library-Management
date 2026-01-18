<?php

use Src\Repositories\DatabaseConnection;
use Src\Repositories\BookRepository;
use Src\Repositories\MemberRepository;
use Src\Repositories\BorrowRepository;
use Src\Services\LibraryService;

require_once __DIR__ . '/../src/Repositories/DatabaseConnection.php';
require_once __DIR__ . '/../src/Models/Member.php';
require_once __DIR__ . '/../src/Models/StudentMember.php';
require_once __DIR__ . '/../src/Models/FacultyMember.php';
require_once __DIR__ . '/../src/Models/Book.php';
require_once __DIR__ . '/../src/Models/BorrowRecord.php';
require_once __DIR__ . '/../src/Repositories/BookRepository.php';
require_once __DIR__ . '/../src/Repositories/MemberRepository.php';
require_once __DIR__ . '/../src/Repositories/BorrowRepository.php';
require_once __DIR__ . '/../src/Services/LibraryService.php';
require_once __DIR__ . '/../src/Exceptions/BookUnavailableException.php';
require_once __DIR__ . '/../src/Exceptions/MemberLimitExceededException.php';
require_once __DIR__ . '/../src/Exceptions/LateFeeException.php';

try {
    echo "Initializing Library System Test...\n";
    
    // Setup Service
    $bookRepo = new BookRepository();
    $memberRepo = new MemberRepository();
    $borrowRepo = new BorrowRepository();
    $service = new LibraryService($bookRepo, $memberRepo, $borrowRepo);

    // Test Data IDs (from sample_data.sql)
    $studentId = 'S1001'; // Alice
    $facultyId = 'F2001'; // Dr. Smith
    $availableBookIsbn = '9780132350884'; // Clean Code
    $checkedOutBookIsbn = '9780747532743'; // Harry Potter
    $branchId = 1;

    echo "\n--- Scenario 1: Student Borrowing Available Book ---\n";
    try {
        $service->borrowBook($studentId, $availableBookIsbn, $branchId);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n--- Scenario 2: Attempt Borrowing Unavailable Book ---\n";
    try {
        $service->borrowBook($studentId, $checkedOutBookIsbn, $branchId);
    } catch (Exception $e) {
        echo "Expected Error: " . $e->getMessage() . "\n";
    }

    echo "\n--- Scenario 3: Returning Book (Simulate logic) ---\n";
    // Need to find the borrow record ID we just created or one existing
    // For this test script, we can query active borrows for S1001
    $activeBorrows = $borrowRepo->findActiveByMemberId($studentId);
    if (count($activeBorrows) > 0) {
        $borrowId = $activeBorrows[0]->getId();
        $service->returnBook($borrowId);
    } else {
        echo "No active borrow found to return for S1001.\n";
    }

    echo "\n--- Scenario 4: Faculty Borrowing ---\n";
    try {
        // Borrow "Design Patterns"
        $service->borrowBook($facultyId, '9780201633610', 1);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n--- Scenario 5: Valid Reservation ---\n";
    try {
        // Attempt to reserve the Harry Potter book which should be checked out (from sample data or previous steps)
        // If scenario 2 failed appropriately, it's not checked out by *us* in this script, 
        // but Sample Data has it as 'Checked Out'.
        $service->reserveBook($studentId, '9780747532743');
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\nTest Script Completed.\n";

} catch (Exception $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}
