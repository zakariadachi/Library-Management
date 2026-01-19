<?php

namespace Src\Services;

use Src\Repositories\BookRepository;
use Src\Repositories\MemberRepository;
use Src\Repositories\BorrowRepository;
use Src\Repositories\ReservationRepository;
use Src\Repositories\InventoryRepository;
use Src\Models\BorrowRecord;
use Src\Exceptions\LateFeeException;
use Src\Exceptions\BookUnavailableException;
use Src\Exceptions\MemberLimitExceededException;
use Exception;

class LibraryService
{
    public function __construct(
        private BookRepository $bookRepo,
        private MemberRepository $memberRepo,
        private BorrowRepository $borrowRepo,
        private ReservationRepository $reservationRepo,
        private InventoryRepository $inventoryRepo
    ) {}

    public function borrowBook(string $memberId, string $isbn, int $branchId): void
    {
        // Validate Member
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new Exception("Member not found with ID: $memberId");
        }
        
        // Check unpaid fees first
        if ($member->getUnpaidFees() > 10.00) {
            throw new LateFeeException();
        }
        
        // Check membership expiry
        if (strtotime($member->getExpiryDate()) < time()) {
            throw new Exception("Membership has expired: " . $member->getExpiryDate());
        }

        // Check Borrow Limit
        $activeBorrows = $this->borrowRepo->getActiveBorrowCount($memberId);
        if ($activeBorrows >= $member->getMaxBooks()) {
            throw new MemberLimitExceededException();
        }

        // Check Book Existence
        $book = $this->bookRepo->findByIsbn($isbn);
        if (!$book) {
            throw new Exception("Book not found.");
        }

        // Check Inventory Availability (Multi-copy support fix)
        $availableInBranch = $this->inventoryRepo->getAvailableCopies($isbn, $branchId);
        if ($availableInBranch <= 0) {
            throw new BookUnavailableException();
        }

        // Create Borrow Record
        $borrowDate = date('Y-m-d');
        $loanPeriod = $member->getLoanPeriod();
        $dueDate = date('Y-m-d', strtotime("+$loanPeriod days"));
        
        $record = new BorrowRecord(
            null, 
            $memberId, 
            $isbn, 
            $branchId, 
            $borrowDate, 
            $dueDate
        );

        // Transactional operations
        $this->borrowRepo->create($record);
        
        // Update Inventory
        $this->inventoryRepo->updateAvailableCopies($isbn, $branchId, -1);
        
        // Update general book status (only if all copies everywhere are gone)
        if ($this->inventoryRepo->getTotalAvailableAcrossBranches($isbn) <= 0) {
            $this->bookRepo->updateStatus($isbn, 'Checked Out');
        }
        
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
        
        // Update Inventory
        $this->inventoryRepo->updateAvailableCopies($record->getBookIsbn(), $record->getBranchId(), 1);

        // Update Book Status and Check reservations
        $reservations = $this->reservationRepo->findPendingByIsbn($record->getBookIsbn());
        if (!empty($reservations)) {
            $this->bookRepo->updateStatus($record->getBookIsbn(), 'Reserved');
            echo "Book returned. Status set to Reserved for next member.\n";
        } else {
            $this->bookRepo->updateStatus($record->getBookIsbn(), 'Available');
        }
    }

    // --- Advanced Features ---

    public function reserveBook(string $memberId, string $isbn): void
    {
        // Validate Member
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new Exception("Member not found.");
        }
        if ($member->getUnpaidFees() > 10.00) {
             throw new Exception("Cannot reserve: Unpaid fees exceed limit.");
        }

        $book = $this->bookRepo->findByIsbn($isbn);
        if (!$book) {
            throw new Exception("Book not found.");
        }

        // Check if book is available ANYWHERE (Simplified)
        if ($this->inventoryRepo->getTotalAvailableAcrossBranches($isbn) > 0) {
            throw new Exception("Book is currently available. You can borrow it directly.");
        }

        // Check for existing reservations
        $memberReservations = $this->reservationRepo->findByMember($memberId);
        foreach ($memberReservations as $res) {
            if ($res->getBookIsbn() === $isbn && $res->getStatus() === 'Pending') {
                throw new Exception("You already have an active reservation for this book");
            }
        }

        // Create Reservation
        $reservation = new \Src\Models\Reservation(
            null,
            $memberId,
            $isbn,
            date('Y-m-d H:i:s'),
            'Pending'
        );
        $this->reservationRepo->create($reservation);

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
        $pendingReservations = $this->reservationRepo->findPendingByIsbn($record->getBookIsbn());
        
        if (count($pendingReservations) > 0) {
            throw new Exception("Cannot renew: This book has pending reservations.");
        }

        // Simplification
        $newDueDate = date('Y-m-d', strtotime($record->getDueDate() . ' +14 days'));
        
        $db = \Src\Repositories\DatabaseConnection::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE borrow_records SET due_date = :new_date WHERE id = :id");
        $stmt->execute(['new_date' => $newDueDate, 'id' => $borrowId]);

        echo "Book renewed successfully. New Due Date: $newDueDate\n";
    }
}