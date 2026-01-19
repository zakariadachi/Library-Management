
-- Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Library Branches
CREATE TABLE library_branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location TEXT,
    contact_info VARCHAR(50)
);

-- Authors
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    biography TEXT,
    nationality VARCHAR(50),
    birth_date DATE
);

-- Books
CREATE TABLE books (
    isbn VARCHAR(20) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    publication_year INT,
    category_id INT,
    status ENUM('Available', 'Checked Out', 'Reserved') DEFAULT 'Available',
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Book Authors
CREATE TABLE book_authors (
    book_isbn VARCHAR(20),
    author_id INT,
    PRIMARY KEY (book_isbn, author_id),
    FOREIGN KEY (book_isbn) REFERENCES books(isbn),
    FOREIGN KEY (author_id) REFERENCES authors(id)
);

-- Members
CREATE TABLE members (
    id VARCHAR(50) PRIMARY KEY,
    type ENUM('Student', 'Faculty') NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    expiry_date DATE NOT NULL,
    unpaid_fees DECIMAL(10, 2) DEFAULT 0.00
);

-- Inventory
CREATE TABLE inventory (
    book_isbn VARCHAR(20),
    branch_id INT,
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    PRIMARY KEY (book_isbn, branch_id),
    FOREIGN KEY (book_isbn) REFERENCES books(isbn),
    FOREIGN KEY (branch_id) REFERENCES library_branches(id)
);

-- Borrow Records
CREATE TABLE borrow_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(50) NOT NULL,
    book_isbn VARCHAR(20) NOT NULL,
    branch_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    late_fee DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (book_isbn) REFERENCES books(isbn),
    FOREIGN KEY (branch_id) REFERENCES library_branches(id)
);

-- Reservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id VARCHAR(50) NOT NULL,
    book_isbn VARCHAR(20) NOT NULL,
    reservation_date DATETIME NOT NULL,
    status ENUM('Pending', 'Completed', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (book_isbn) REFERENCES books(isbn)
);