# Library Management System

A modern, object-oriented Library Management System built with PHP 8 (no frameworks) and MySQL.

## Features

- **OOP Architecture**: MVC-like separation with Models, Repositories, and Services.
- **SOLID Principles**: Applied throughout the design.
- **Member Management**: Single Table Inheritance for Students and Faculty with different borrowing rules.
- **Book Management**: Tracking availability and status.
- **Borrowing System**: Complete workflow with validations (fees, limits) and fine calculation.
- **Database**: 3rd Normal Form schema with PDO Security.

## Setup

1. Import `database/schema.sql` to your MySQL database.
2. Import `database/sample_data.sql` to populate initial data.
3. Configure `src/Repositories/DatabaseConnection.php` if your DB credentials differ from default XAMPP.
4. Run tests: `php tests/library_test.php`.

## Directory Structure

- `src/Models`: Domain entities.
- `src/Repositories`: Database access layer.
- `src/Services`: Business logic orchestration.
- `database`: SQL schema and data.
- `tests`: Verification scripts.
