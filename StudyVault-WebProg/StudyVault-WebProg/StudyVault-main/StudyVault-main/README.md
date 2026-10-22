# StudyVault
Database-Driven Student Note Sharing & Subject Archiving Portal

Stack: PHP 8.x, MySQL 8, HTML5, CSS3, JavaScript, PDO, XAMPP.

## Setup
1. Copy `StudyVault` into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `database/studyvault.sql`.
4. Check database settings in `config/db.php`.
5. Make sure Apache can write to `uploads/`.
6. Visit `http://localhost/StudyVault/`.

## Default admin
Register a normal account first. Then run:
`UPDATE users SET role='admin' WHERE email='your@email.com';`

## Features
- Registration/login/logout with password_hash/password_verify
- Session and role authorization
- CSRF protection
- Note upload/download
- Subject and semester catalog
- Search/filter by subject, keyword, semester
- Comments and 1–5 ratings
- Admin moderation
- Admin user/subject management
- Audit log trigger
- MySQL view for approved notes
- PDO prepared statements
- Client-side + server-side validation
