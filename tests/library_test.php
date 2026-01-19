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
require_once __DIR__ . '/../src/Models/Reservation.php';
require_once __DIR__ . '/../src/Repositories/BookRepository.php';
require_once __DIR__ . '/../src/Repositories/MemberRepository.php';
require_once __DIR__ . '/../src/Repositories/BorrowRepository.php';
require_once __DIR__ . '/../src/Repositories/ReservationRepository.php';
require_once __DIR__ . '/../src/Repositories/BranchRepository.php';
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

    // Test Data IDs
    $studentId = 'S1001';
    $facultyId = 'F2001';
    $availableBookIsbn = '9780132350884';
    $checkedOutBookIsbn = '9780747532743';
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
    $activeBorrows = $borrowRepo->findActiveByMemberId($studentId);
    if (count($activeBorrows) > 0) {
        $borrowId = $activeBorrows[0]->getId();
        $service->returnBook($borrowId);
    } else {
        echo "No active borrow found to return for S1001.\n";
    }

    echo "\n--- Scenario 4: Faculty Borrowing ---\n";
    try {
        $service->borrowBook($facultyId, '9780201633610', 1);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n--- Scenario 5: Valid Reservation ---\n";
    try {
        $service->reserveBook($studentId, '9780747532743');
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\nTest Script Completed.\n";

} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}