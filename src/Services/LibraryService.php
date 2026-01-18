<?php

namespace Src\Services;

use Src\Repositories\BookRepository;
use Src\Repositories\MemberRepository;
use Src\Repositories\BorrowRepository;
use Src\Models\BorrowRecord;
use Src\Exceptions\BookUnavailableException;
use Src\Exceptions\MemberLimitExceededException;
use Src\Exceptions\LateFeeException;
use Exception;

class LibraryService
{
    public function __construct(
        private BookRepository $bookRepo,
        private MemberRepository $memberRepo,
        private BorrowRepository $borrowRepo
    ) {}

    public function borrowBook(string $memberId, string $isbn, int $branchId): void
    {
        // 1. Validate Member
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new Exception("Member not found.");
        }

        if (!$member->canBorrow()) {
            throw new LateFeeException("Member cannot borrow due to unpaid fees or expired membership.");
        }

        // 2. Check Borrow Limit
        $activeBorrows = $this->borrowRepo->getActiveBorrowCount($memberId);
        if ($activeBorrows >= $member->getMaxBooks()) {
            throw new MemberLimitExceededException("Borrow limit reached for this member type.");
        }

        // 3. Check Book Availability
        $book = $this->bookRepo->findByIsbn($isbn);
        if (!$book) {
            throw new Exception("Book not found.");
        }
        if (!$book->isAvailable()) {
            throw new BookUnavailableException("Book is currently " . $book->getStatus());
        }

        // 4. Create Borrow Record
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime("+$member->getLoanPeriod() days"));
        
        $record = new BorrowRecord(
            null, 
            $memberId, 
            $isbn, 
            $branchId, 
            $borrowDate, 
            $dueDate
        );

        // 5. Transaction
        // Ideally wrap in DB transaction
        $this->borrowRepo->create($record);
        $this->bookRepo->updateStatus($isbn, 'Checked Out');
        
        echo "Book '{$book->getTitle()}' borrowed successfully by {$member->getName()}. Due: $dueDate\n";
    }

    public function returnBook(int $borrowId): void
    {
        $record = $this->borrowRepo->findById($borrowId);
        if (!$record) {
            throw new Exception("Borrow record not found.");
        }

        if ($record->getReturnDate()) {
            throw new Exception("Book already returned.");
        }

        // Calculate Fines
        $returnDate = date('Y-m-d');
        $lateFee = 0.0;

        if (strtotime($returnDate) > strtotime($record->getDueDate())) {
            $diffParams = date_diff(date_create($record->getDueDate()), date_create($returnDate));
            $daysLate = $diffParams->days;
            
            // Get Member rate
            $member = $this->memberRepo->findById($record->getMemberId());
            $lateFee = $daysLate * $member->getLateFeeRate();
            
            // Update member unpaid fees
            $member->addFee($lateFee);
            $this->memberRepo->updateUnpaidFees($member->getId(), $member->getUnpaidFees());
            
            echo "Book returned late. Fine: $$lateFee\n";
        } else {
            echo "Book returned on time.\n";
        }

        // Update Record
        $this->borrowRepo->updateReturn($borrowId, $returnDate, $lateFee);
        
        // Update Book Status
        $this->bookRepo->updateStatus($record->getBookIsbn(), 'Available');
    }

    // --- Advanced Features ---

    public function reserveBook(string $memberId, string $isbn): void
    {
        $book = $this->bookRepo->findByIsbn($isbn);
        if (!$book) {
            throw new Exception("Book not found.");
        }

        if ($book->isAvailable()) {
            throw new Exception("Book is currently available. You can borrow it directly.");
        }

        // Create Reservation
        $reservation = new \Src\Models\Reservation(
            null,
            $memberId,
            $isbn,
            date('Y-m-d H:i:s'),
            'Pending'
        );

        // We need a ReservationRepository instance here. 
        // Ideally injected, but for this quick add we can treat it slightly loosely or add setter/constructor.
        // Let's assume we update constructor or just instantiate here for simplicity of the workshop patch.
        $reservationRepo = new \Src\Repositories\ReservationRepository(); 
        $reservationRepo->create($reservation);

        echo "Book reserved successfully. You will be notified when it is available.\n";
    }

    public function renewBook(int $borrowId): void
    {
        $record = $this->borrowRepo->findById($borrowId);
        if (!$record) {
            throw new Exception("Borrow record not found.");
        }
        
        if ($record->getReturnDate()) {
            throw new Exception("Cannot renew returned book.");
        }

        // Check if reserved
        $reservationRepo = new \Src\Repositories\ReservationRepository();
        $pendingReservations = $reservationRepo->findPendingByIsbn($record->getBookIsbn());
        
        if (count($pendingReservations) > 0) {
            throw new Exception("Cannot renew: This book has pending reservations.");
        }

        // Extend Due Date (assuming 14 days renewal for everyone for simplicity, or fetch member type)
        // Simplification: Add 14 days to current Due Date
        $newDueDate = date('Y-m-d', strtotime($record->getDueDate() . ' +14 days'));
        
        // We'd need a method in BorrowRepository to update Due Date.
        // Let's implement a simple SQL run here or add to Repo. Cleanest is ad-hoc here for the workshop speed.
        $db = \Src\Repositories\DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE borrow_records SET due_date = :new_date WHERE id = :id");
        $stmt->execute(['new_date' => $newDueDate, 'id' => $borrowId]);

        echo "Book renewed successfully. New Due Date: $newDueDate\n";
    }
}
