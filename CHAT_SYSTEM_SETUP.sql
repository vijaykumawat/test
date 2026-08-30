-- =====================================================================
--  CHAT SYSTEM - MESSAGES TABLE SETUP (MySQL)
--  Database : gbinsura_crm
--  Run this file manually (e.g. phpMyAdmin -> Import) if you are not
--  using CodeIgniter migrations:
--      php spark migrate
-- =====================================================================

CREATE TABLE IF NOT EXISTS `messages` (
  `messageId` int NOT NULL AUTO_INCREMENT,
  `senderId` char(16) NOT NULL,
  `receiverId` char(16) NOT NULL,
  `messageText` text NOT NULL,
  `isRead` tinyint NOT NULL DEFAULT '0' COMMENT '0 = unread, 1 = read by the receiver',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`messageId`),
  KEY `idx_messages_sender` (`senderId`),
  KEY `idx_messages_receiver` (`receiverId`),
  KEY `idx_messages_pair` (`senderId`,`receiverId`),
  `isRead` tinyint NOT NULL DEFAULT '0',
  KEY `idx_messages_createdAt` (`createdAt`),
  CONSTRAINT `fk_messages_sender` FOREIGN KEY (`senderId`) REFERENCES `employee` (`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiverId`) REFERENCES `employee` (`employeeId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- If the table was created earlier WITHOUT the isRead column, add it with:
--   ALTER TABLE `messages` ADD COLUMN `isRead` tinyint NOT NULL DEFAULT '0' AFTER `messageText`;
-- (or simply run: php spark migrate)

-- ------------------------------------------------------------------
--  Sanity checks (should return results only when there is data)
-- ------------------------------------------------------------------
-- 1. All senders / receivers reference existing employees
-- SELECT COUNT(*) AS orphan_count FROM messages m
--   LEFT JOIN employee e ON e.employeeId = m.senderId
--   WHERE e.employeeId IS NULL;
--
-- 2. Sample query: 1:1 conversation between two employees
-- SELECT * FROM messages
--   WHERE (senderId = '<empA>' AND receiverId = '<empB>')
--      OR (senderId = '<empB>' AND receiverId = '<empA>')
--   ORDER BY messageId ASC;