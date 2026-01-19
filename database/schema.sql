
-- Categories (Helper for Books)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Authors
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    biography TEXT,
    nationality VARCHAR(100),
    birth_date DATE
);

-- Library Branches (Attributes from UML: id, name, location, contactInfo)
CREATE TABLE library_branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    location VARCHAR(255) NOT NULL,
    contact_info VARCHAR(255)
);

-- Members
CREATE TABLE members (
    id VARCHAR(50) PRIMARY KEY,
    type ENUM('Student', 'Faculty') NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    expiry_date DATE NOT NULL,
    unpaid_fees DECIMAL(10, 2) DEFAULT 0.00
);

-- Books
CREATE TABLE books (
    isbn VARCHAR(13) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    publication_year INT NOT NULL,
    category_id INT NOT NULL,
    status ENUM('Available', 'Checked Out', 'Reserved') DEFAULT 'Available',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- Book_Authors
CREATE TABLE book_authors (
    book_isbn VARCHAR(13) NOT NULL,
    author_id INT NOT NULL,
    PRIMARY KEY (book_isbn, author_id),
    FOREIGN KEY (book_isbn) REFERENCES books(isbn) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
);

-- 7. Inventory (Attributes from UML: availableCopies, totalCopies)
CREATE TABLE inventory (
    book_isbn VARCHAR(13) NOT NULL,
    branch_id INT NOT NULL,
    total_copies INT NOT NULL DEFAULT 0,
    available_copies INT NOT NULL DEFAULT 0,
    PRIMARY KEY (book_isbn, branch_id),
    FOREIGN KEY (book_isbn) REFERENCES books(isbn) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES library_branches(id) ON DELETE CASCADE
);

-- 8. Borrow Records
CREATE TABLE borrow_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(50) NOT NULL,
    book_isbn VARCHAR(13) NOT NULL,
    branch_id INT NOT NULL,
    borrow_date DATE NOT NULL DEFAULT CURRENT_DATE,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    late_fee DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE RESTRICT,
    FOREIGN KEY (book_isbn) REFERENCES books(isbn) ON DELETE RESTRICT,
    FOREIGN KEY (branch_id) REFERENCES library_branches(id) ON DELETE RESTRICT
);

-- 9. Reservations
DROP TABLE IF EXISTS reservations;
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(50) NOT NULL,
    book_isbn VARCHAR(13) NOT NULL,
    reservation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Fulfilled', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (book_isbn) REFERENCES books(isbn) ON DELETE CASCADE
);
