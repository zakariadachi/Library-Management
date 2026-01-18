-- Sample Data for Library Management System
USE library_db;

-- 1. Branches
INSERT INTO branches (name, location, opening_hours, contact_info) VALUES
('North Campus', 'Building A, 2nd Floor', '08:00 - 18:00', 'north@library.edu'),
('South Campus', 'Building B, Ground Floor', '09:00 - 20:00', 'south@library.edu');

-- 2. Categories
INSERT INTO categories (name) VALUES
('Computer Science'),
('Literature'),
('Science'),
('Mathematics');

-- 3. Authors
INSERT INTO authors (name, biography, nationality, primary_genre) VALUES
('Robert C. Martin', 'Uncle Bob, author of Clean Code.', 'American', 'Technology'),
('F. Scott Fitzgerald', 'Author of The Great Gatsby.', 'American', 'Fiction'),
('Isaac Newton', 'Philosopher and Mathematician.', 'British', 'Science');

-- 4. Books
INSERT INTO books (isbn, title, publication_year, category_id, total_copies, available_copies, status) VALUES
('978-0132350884', 'Clean Code', 2008, 1, 5, 5, 'Available'),
('978-0743273565', 'The Great Gatsby', 1925, 2, 2, 2, 'Available'),
('978-0521273341', 'Principia Mathematica', 1687, 4, 1, 1, 'Available');

-- 5. Book Authors
INSERT INTO book_authors (book_isbn, author_id) VALUES
('978-0132350884', 1),
('978-0743273565', 2),
('978-0521273341', 3);

-- 6. Members
-- Student: valid until next year, 0 fees
INSERT INTO members (full_name, email, phone, member_type, membership_expiry, unpaid_fees) VALUES
('Alice Student', 'alice@student.edu', '555-0101', 'Student', '2027-01-01', 0.00),
-- Faculty: valid until 3 years later, 0 fees
('Bob Professor', 'bob@faculty.edu', '555-0202', 'Faculty', '2029-01-01', 0.00),
-- Expired Member
('Charlie Expired', 'charlie@expired.edu', '555-0303', 'Student', '2023-01-01', 0.00);
