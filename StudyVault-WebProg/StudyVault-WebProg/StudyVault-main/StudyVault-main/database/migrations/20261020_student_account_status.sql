USE studyvault;

-- Preserve current account states and allow new student accounts to be pending activation.
ALTER TABLE users
    MODIFY status ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active';

-- Older MySQL setups may have stored unsupported "inactive" values as an empty enum.
UPDATE users
SET status = 'inactive'
WHERE status = '' AND role = 'student';
