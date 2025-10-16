-- SQL script to add approval fields to services table
-- Run this manually in phpMyAdmin or MySQL client

USE db_tkdn;

-- Add approval_notes field if not exists
ALTER TABLE services 
ADD COLUMN IF NOT EXISTS approval_notes TEXT NULL AFTER approved_at;

-- Add notes field if not exists
ALTER TABLE services 
ADD COLUMN IF NOT EXISTS notes TEXT NULL AFTER approval_notes;

-- Add updated_by field if not exists
ALTER TABLE services 
ADD COLUMN IF NOT EXISTS updated_by VARCHAR(26) NULL AFTER notes;

-- Verify the columns were added
SHOW COLUMNS FROM services LIKE '%notes%';
SHOW COLUMNS FROM services LIKE 'updated_by';
