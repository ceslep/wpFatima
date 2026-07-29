CREATE DATABASE IF NOT EXISTS wpfatima CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wpfatima;

CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wa_number VARCHAR(20) NOT NULL UNIQUE,
    contact_name VARCHAR(100) DEFAULT NULL,
    last_message TEXT DEFAULT NULL,
    last_message_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_wa_number (wa_number),
    INDEX idx_last_message_at (last_message_at DESC)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    direction ENUM('inbound', 'outbound') NOT NULL,
    body TEXT NOT NULL,
    twilio_message_sid VARCHAR(50) DEFAULT NULL,
    status ENUM('queued', 'sent', 'delivered', 'read', 'failed') DEFAULT 'queued',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
