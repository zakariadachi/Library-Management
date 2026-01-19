-- Categories
INSERT INTO categories VALUES 
(1, 'Computer Science'),
(2, 'Literature'),
(3, 'Physics'),
(4, 'History'),
(5, 'Mathematics');

-- Library Branches
INSERT INTO library_branches VALUES 
(1, 'Central Library', 'Building A, Main Campus', '555-0101'),
(2, 'North Campus Branch', 'Building N, North Campus', '555-0102'),
(3, 'Science Library', 'Science Block, Floor 2', '555-0103'),
(4, 'Law Library', 'Law Faculty', '555-0104'),
(5, 'Arts Library', 'Arts Center', '555-0105');

-- Authors
INSERT INTO authors VALUES 
(1, 'Robert C. Martin', 'Uncle Bob', 'American', '1952-12-05'),
(2, 'J.K. Rowling', 'British author', 'British', '1965-07-31'),
(3, 'Isaac Newton', 'Physicist', 'English', '1643-01-04'),
(4, 'F. Scott Fitzgerald', 'Novelist', 'American', '1896-09-24'),
(5, 'Erich Gamma', 'GoF', 'Swiss', '1961-03-13');

-- Books
INSERT INTO books VALUES 
('9780132350884', 'Clean Code', 2008, 1, 'Available'),
('9780201633610', 'Design Patterns', 1994, 1, 'Available'),
('9780747532743', 'Harry Potter and the Philosopher''s Stone', 1997, 2, 'Checked Out'),
('9780684801520', 'The Great Gatsby', 1925, 2, 'Available'),
('9780140449105', 'Principia Mathematica', 1687, 3, 'Available');

-- Book Authors
INSERT INTO book_authors VALUES 
('9780132350884', 1),
('9780201633610', 5),
('9780747532743', 2),
('9780684801520', 4),
('9780140449105', 3);

-- Members
INSERT INTO members VALUES 
('S1001', 'Student', 'Alice Student', 'alice@uni.edu', '555-1001', '2026-12-31', 0.00),
('S1002', 'Student', 'Bob Student', 'bob@uni.edu', '555-1002', '2026-12-31', 0.00),
('F2001', 'Faculty', 'Dr. Smith', 'smith@uni.edu', '555-2001', '2028-12-31', 0.00),
('F2002', 'Faculty', 'Prof. Johnson', 'johnson@uni.edu', '555-2002', '2028-12-31', 0.00),
('S1003', 'Student', 'Charlie Latepayer', 'charlie@uni.edu', '555-1003', '2026-12-31', 15.00);

-- Inventory
INSERT INTO inventory VALUES 
('9780132350884', 1, 5, 5),
('9780132350884', 3, 2, 2),
('9780201633610', 1, 3, 3), 
('9780747532743', 2, 4, 3),
('9780684801520', 5, 2, 2);

-- Borrow Records 
INSERT INTO borrow_records VALUES 
(1, 'S1003', '9780747532743', 2, '2025-12-01', '2025-12-15', NULL, 0.00);
