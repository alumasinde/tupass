-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               9.4.0 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table tupass_demo.actions
CREATE TABLE IF NOT EXISTS `actions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_action_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=51288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.actions: ~13 rows (approximately)
INSERT IGNORE INTO `actions` (`id`, `name`) VALUES
	(1, 'view'),
	(2, 'create'),
	(3, 'edit'),
	(4, 'delete'),
	(5, 'approve'),
	(6, 'access'),
	(9, 'update'),
	(12, 'checkin'),
	(13, 'checkout'),
	(14, 'print'),
	(18, 'blacklist'),
	(22, 'disable'),
	(24, 'assign');

-- Dumping structure for table tupass_demo.audit_logs
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` bigint unsigned DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_tenant` (`tenant_id`),
  KEY `idx_audit_entity` (`tenant_id`,`entity_type`,`entity_id`),
  KEY `fk_audit_user` (`user_id`,`tenant_id`),
  CONSTRAINT `fk_audit_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`, `tenant_id`) REFERENCES `users` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.audit_logs: ~54 rows (approximately)
INSERT IGNORE INTO `audit_logs` (`id`, `tenant_id`, `user_id`, `action`, `entity_type`, `entity_id`, `metadata`, `ip_address`, `created_at`) VALUES
	(1, 1, 1, 'gatepass.created', 'gatepass', 3, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-22 20:44:54"}, "needs_approval": false, "gatepass_number": "DM-2026-0005"}', '127.0.0.1', '2026-02-22 20:44:54'),
	(2, 1, 1, 'gatepass.created', 'gatepass', 4, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-22 21:22:47"}, "needs_approval": false, "gatepass_number": "DM-2026-0006"}', '127.0.0.1', '2026-02-22 21:22:47'),
	(3, 1, 1, 'gatepass.created', 'gatepass', 5, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-22 21:49:51"}, "needs_approval": false, "gatepass_number": "DM-2026-0007"}', '127.0.0.1', '2026-02-22 21:49:51'),
	(4, 1, 1, 'gatepass.created', 'gatepass', 6, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-22 21:54:11"}, "needs_approval": false, "gatepass_number": "DM-2026-0008"}', '127.0.0.1', '2026-02-22 21:54:11'),
	(5, 1, 1, 'visitor.created', 'visitor', 2, '{"name": "Amos Masinde", "_context": {"ip": "127.0.0.1", "url": "/visitors", "method": "POST", "timestamp": "2026-02-23 18:23:02"}, "id_number": "23438903", "tenant_id": 1}', '127.0.0.1', '2026-02-23 18:23:02'),
	(6, 1, 1, 'visitor.created', 'visitor', 3, '{"name": "Jane Maina", "_context": {"ip": "127.0.0.1", "url": "/visitors", "method": "POST", "timestamp": "2026-02-23 18:36:50"}, "id_number": "42334872", "tenant_id": 1}', '127.0.0.1', '2026-02-23 18:36:50'),
	(7, 1, 1, 'visit.badge_issued', 'visit', 1, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/issue-badge", "method": "POST", "timestamp": "2026-02-23 18:38:03"}, "issued_by": 1, "tenant_id": 1, "badge_code": "BDG-6B8E88B6"}', '127.0.0.1', '2026-02-23 18:38:03'),
	(8, 1, 1, 'visitor.unblacklisted', 'visitor', 1, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/1/unblacklist", "method": "POST", "timestamp": "2026-02-24 17:29:55"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 17:29:55'),
	(9, 1, 1, 'visitor.blacklisted', 'visitor', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/3/blacklist", "method": "POST", "timestamp": "2026-02-24 17:30:02"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 17:30:02'),
	(10, 1, 1, 'visitor.blacklisted', 'visitor', 2, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/2/blacklist", "method": "POST", "timestamp": "2026-02-24 17:30:05"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 17:30:05'),
	(11, 1, 1, 'visitor.unblacklisted', 'visitor', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/3/unblacklist", "method": "POST", "timestamp": "2026-02-24 17:30:07"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 17:30:07'),
	(12, 1, 1, 'visitor.unblacklisted', 'visitor', 2, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/2/unblacklist", "method": "POST", "timestamp": "2026-02-24 17:30:09"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 17:30:09'),
	(13, 1, 1, 'visit.created', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/visits", "method": "POST", "timestamp": "2026-02-24 20:02:56"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 20:02:56'),
	(14, 1, 1, 'visit.badge_issued', 'visit', 1, '{"_context": {"ip": "127.0.0.1", "url": "/badges/1/issue", "method": "POST", "timestamp": "2026-02-24 20:38:57"}, "tenant_id": 1, "badge_code": "BDG-6A9D3057"}', '127.0.0.1', '2026-02-24 20:38:57'),
	(15, 1, 1, 'visit.badge_returned', 'visit', 1, '{"_context": {"ip": "127.0.0.1", "url": "/badges/1/return", "method": "POST", "timestamp": "2026-02-24 20:57:12"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 20:57:12'),
	(16, 1, 1, 'visit.checkout', 'visit', 1, '{"_context": {"ip": "127.0.0.1", "url": "/visits/1/checkout", "method": "POST", "timestamp": "2026-02-24 20:57:37"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 20:57:37'),
	(17, 1, 1, 'visit.checkin', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/visits/2/checkin", "method": "POST", "timestamp": "2026-02-24 20:57:40"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 20:57:40'),
	(18, 1, 1, 'visit.badge_issued', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/badges/2/issue", "method": "POST", "timestamp": "2026-02-24 21:03:33"}, "tenant_id": 1, "badge_code": "BDG-E67DFDA1"}', '127.0.0.1', '2026-02-24 21:03:33'),
	(19, 1, 1, 'visit.badge_returned', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/badges/2/return", "method": "POST", "timestamp": "2026-02-24 21:03:39"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:03:39'),
	(20, 1, 1, 'visit.badge_issued', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/badges/2/issue", "method": "POST", "timestamp": "2026-02-24 21:03:40"}, "tenant_id": 1, "badge_code": "BDG-FBDF51D0"}', '127.0.0.1', '2026-02-24 21:03:40'),
	(21, 1, 1, 'visit.badge_returned', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/badges/2/return", "method": "POST", "timestamp": "2026-02-24 21:03:42"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:03:42'),
	(22, 1, 1, 'visit.checkout', 'visit', 2, '{"_context": {"ip": "127.0.0.1", "url": "/visits/2/checkout", "method": "POST", "timestamp": "2026-02-24 21:09:16"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:09:16'),
	(23, 1, 1, 'visit.created', 'visit', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visits", "method": "POST", "timestamp": "2026-02-24 21:45:07"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:45:07'),
	(24, 1, 1, 'visit.checkin', 'visit', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visits/3/checkin", "method": "POST", "timestamp": "2026-02-24 21:45:12"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:45:12'),
	(25, 1, 1, 'visit.badge_issued', 'visit', 3, '{"_context": {"ip": "127.0.0.1", "url": "/badges/3/issue", "method": "POST", "timestamp": "2026-02-24 21:45:16"}, "tenant_id": 1, "badge_code": "BDG-8C4F9892"}', '127.0.0.1', '2026-02-24 21:45:16'),
	(26, 1, 1, 'visit.badge_returned', 'visit', 3, '{"_context": {"ip": "127.0.0.1", "url": "/badges/3/return", "method": "POST", "timestamp": "2026-02-24 21:45:19"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:45:19'),
	(27, 1, 1, 'visit.checkout', 'visit', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visits/3/checkout", "method": "POST", "timestamp": "2026-02-24 21:45:47"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 21:45:47'),
	(28, 1, 1, 'visitor.updated', 'visitor', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/3/update", "method": "POST", "timestamp": "2026-02-24 22:31:33"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 22:31:33'),
	(29, 1, 1, 'visitor.updated', 'visitor', 3, '{"_context": {"ip": "127.0.0.1", "url": "/visitors/3/update", "method": "POST", "timestamp": "2026-02-24 22:31:56"}, "tenant_id": 1}', '127.0.0.1', '2026-02-24 22:31:56'),
	(30, 1, 1, 'visit.created', 'visit', 4, '{"_context": {"ip": "127.0.0.1", "url": "/visits", "method": "POST", "timestamp": "2026-02-25 19:48:06"}, "tenant_id": 1}', '127.0.0.1', '2026-02-25 19:48:06'),
	(31, 1, 1, 'visit.checkin', 'visit', 4, '{"_context": {"ip": "127.0.0.1", "url": "/visits/4/checkin", "method": "POST", "timestamp": "2026-02-25 19:48:19"}, "tenant_id": 1}', '127.0.0.1', '2026-02-25 19:48:19'),
	(32, 1, 1, 'visit.badge_issued', 'visit', 4, '{"_context": {"ip": "127.0.0.1", "url": "/badges/4/issue", "method": "POST", "timestamp": "2026-02-25 19:48:27"}, "tenant_id": 1, "badge_code": "BDG-3D3964D7"}', '127.0.0.1', '2026-02-25 19:48:27'),
	(33, 1, 1, 'visit.badge_returned', 'visit', 4, '{"_context": {"ip": "127.0.0.1", "url": "/badges/4/return", "method": "POST", "timestamp": "2026-02-25 19:48:31"}, "tenant_id": 1}', '127.0.0.1', '2026-02-25 19:48:31'),
	(34, 1, 1, 'visit.checkout', 'visit', 4, '{"_context": {"ip": "127.0.0.1", "url": "/visits/4/checkout", "method": "POST", "timestamp": "2026-02-25 19:48:33"}, "tenant_id": 1}', '127.0.0.1', '2026-02-25 19:48:33'),
	(35, 1, 1, 'visit.created', 'visit', 5, '{"_context": {"ip": "127.0.0.1", "url": "/visits", "method": "POST", "timestamp": "2026-02-27 17:28:10"}, "tenant_id": 1}', '127.0.0.1', '2026-02-27 17:28:10'),
	(36, 1, 1, 'visit.checkin', 'visit', 5, '{"_context": {"ip": "127.0.0.1", "url": "/visits/5/checkin", "method": "POST", "timestamp": "2026-02-27 17:28:23"}, "tenant_id": 1}', '127.0.0.1', '2026-02-27 17:28:23'),
	(37, 1, 1, 'gatepass.created', 'gatepass', 7, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-27 17:29:07"}, "needs_approval": false, "gatepass_number": "DM-2026-0009"}', '127.0.0.1', '2026-02-27 17:29:07'),
	(38, 1, 3, 'gatepass.created', 'gatepass', 8, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-27 21:37:38"}, "needs_approval": true, "gatepass_number": "DM-2026-0010"}', '127.0.0.1', '2026-02-27 21:37:38'),
	(39, 1, 3, 'gatepass.checked_in', 'gatepass', 7, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/7/checkin", "method": "POST", "timestamp": "2026-02-27 21:37:53"}, "timestamp": "2026-02-27 21:37:53"}', '127.0.0.1', '2026-02-27 21:37:53'),
	(40, 1, 2, 'visit.badge_issued', 'visit', 5, '{"_context": {"ip": "127.0.0.1", "url": "/badges/5/issue", "method": "POST", "timestamp": "2026-02-27 23:07:48"}, "tenant_id": 1, "badge_code": "BDG-6BFF614E"}', '127.0.0.1', '2026-02-27 23:07:48'),
	(41, 1, 1, 'gatepass.created', 'gatepass', 9, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-02-28 16:22:56"}, "needs_approval": true, "gatepass_number": "DM-2026-0011"}', '127.0.0.1', '2026-02-28 16:22:56'),
	(42, 1, 1, 'gatepass.checked_out', 'gatepass', 6, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/6/checkout", "method": "POST", "timestamp": "2026-02-28 21:32:47"}, "timestamp": "2026-02-28 21:32:47"}', '127.0.0.1', '2026-02-28 21:32:47'),
	(43, 1, 1, 'visit.badge_returned', 'visit', 5, '{"_context": {"ip": "127.0.0.1", "url": "/badges/5/return", "method": "POST", "timestamp": "2026-03-01 00:31:20"}, "tenant_id": 1}', '127.0.0.1', '2026-03-01 00:31:20'),
	(44, 1, 1, 'visit.checkout', 'visit', 5, '{"_context": {"ip": "127.0.0.1", "url": "/visits/5/checkout", "method": "POST", "timestamp": "2026-03-01 00:31:23"}, "tenant_id": 1}', '127.0.0.1', '2026-03-01 00:31:23'),
	(45, 1, 1, 'visit.created', 'visit', 6, '{"_context": {"ip": "127.0.0.1", "url": "/visits", "method": "POST", "timestamp": "2026-03-01 00:31:52"}, "tenant_id": 1}', '127.0.0.1', '2026-03-01 00:31:52'),
	(46, 1, 1, 'visit.checkin', 'visit', 6, '{"_context": {"ip": "127.0.0.1", "url": "/visits/6/checkin", "method": "POST", "timestamp": "2026-03-01 00:32:07"}, "tenant_id": 1}', '127.0.0.1', '2026-03-01 00:32:07'),
	(47, 1, 1, 'gatepass.created', 'gatepass', 11, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-03-01 00:35:21"}, "needs_approval": true, "gatepass_number": "DM-2026-0012"}', '127.0.0.1', '2026-03-01 00:35:21'),
	(48, 1, 1, 'gatepass.checked_out', 'gatepass', 7, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/7/checkout", "method": "POST", "timestamp": "2026-03-04 19:34:06"}, "timestamp": "2026-03-04 19:34:06"}', '127.0.0.1', '2026-03-04 19:34:06'),
	(49, 1, 1, 'gatepass.checked_out', 'gatepass', 4, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/4/checkout", "method": "POST", "timestamp": "2026-03-04 19:34:32"}, "timestamp": "2026-03-04 19:34:32"}', '127.0.0.1', '2026-03-04 19:34:32'),
	(50, 1, 1, 'gatepass.checked_out', 'gatepass', 3, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/3/checkout", "method": "POST", "timestamp": "2026-03-04 19:34:41"}, "timestamp": "2026-03-04 19:34:41"}', '127.0.0.1', '2026-03-04 19:34:41'),
	(51, 1, 1, 'gatepass.checked_out', 'gatepass', 9, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/9/checkout", "method": "POST", "timestamp": "2026-03-04 19:48:37"}, "timestamp": "2026-03-04 19:48:37"}', '127.0.0.1', '2026-03-04 19:48:37'),
	(52, 1, 1, 'gatepass.checked_out', 'gatepass', 8, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/8/checkout", "method": "POST", "timestamp": "2026-03-04 19:48:39"}, "timestamp": "2026-03-04 19:48:39"}', '127.0.0.1', '2026-03-04 19:48:39'),
	(53, 1, 1, 'gatepass.checked_in', 'gatepass', 8, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/8/checkin", "method": "POST", "timestamp": "2026-03-04 19:48:49"}, "timestamp": "2026-03-04 19:48:49"}', '127.0.0.1', '2026-03-04 19:48:49'),
	(54, 1, 1, 'visit.badge_issued', 'visit', 6, '{"_context": {"ip": "127.0.0.1", "url": "/badges/6/issue", "method": "POST", "timestamp": "2026-03-04 20:00:45"}, "tenant_id": 1, "badge_code": "BDG-34C20133"}', '127.0.0.1', '2026-03-04 20:00:45'),
	(55, 1, 1, 'gatepass.created', 'gatepass', 12, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-03-04 20:01:23"}, "needs_approval": false, "gatepass_number": "DM-2026-0013"}', '127.0.0.1', '2026-03-04 20:01:23'),
	(56, 1, 1, 'gatepass.checked_out', 'gatepass', 12, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/12/checkout", "method": "POST", "timestamp": "2026-03-04 20:04:08"}, "timestamp": "2026-03-04 20:04:08"}', '127.0.0.1', '2026-03-04 20:04:08'),
	(57, 1, 1, 'gatepass.checked_in', 'gatepass', 12, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses/12/checkin", "method": "POST", "timestamp": "2026-03-04 20:04:16"}, "timestamp": "2026-03-04 20:04:16"}', '127.0.0.1', '2026-03-04 20:04:16'),
	(58, 1, 1, 'gatepass.created', 'gatepass', 13, '{"_context": {"ip": "127.0.0.1", "url": "/gatepasses", "method": "POST", "timestamp": "2026-03-04 20:18:31"}, "needs_approval": false, "gatepass_number": "DM-2026-0014"}', '127.0.0.1', '2026-03-04 20:18:31');

-- Dumping structure for table tupass_demo.departments
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_dept_tenant_code` (`tenant_id`,`code`),
  UNIQUE KEY `uk_dept_tenant_id` (`id`,`tenant_id`),
  KEY `idx_depts_tenant` (`tenant_id`),
  CONSTRAINT `fk_depts_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.departments: ~6 rows (approximately)
INSERT IGNORE INTO `departments` (`id`, `tenant_id`, `name`, `code`, `is_active`, `created_at`) VALUES
	(1, 1, 'IT Department', 'IT', 1, '2026-02-22 12:53:23'),
	(2, 1, 'Security', 'SEC', 1, '2026-02-22 12:53:23'),
	(3, 1, 'Human Resource', 'HR', 1, '2026-02-22 12:53:23'),
	(4, 1, 'Administration', 'ADM', 1, '2026-02-22 12:53:23'),
	(5, 1, 'Finance', 'FIN', 1, '2026-02-22 12:53:23'),
	(6, 1, 'Operations', 'OPS', 1, '2026-02-22 12:53:23');

-- Dumping structure for table tupass_demo.gatepasses
CREATE TABLE IF NOT EXISTS `gatepasses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `visit_id` bigint unsigned DEFAULT NULL,
  `gatepass_type_id` bigint unsigned DEFAULT NULL,
  `gatepass_number` varchar(100) NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `checked_in_by` bigint unsigned DEFAULT NULL,
  `checked_out_by` bigint unsigned DEFAULT NULL,
  `actual_in` datetime DEFAULT NULL,
  `actual_out` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purpose` varchar(250) DEFAULT NULL,
  `is_returnable` tinyint(1) NOT NULL DEFAULT '0',
  `expected_return_date` datetime DEFAULT NULL,
  `actual_return_date` datetime DEFAULT NULL,
  `is_fully_returned` tinyint(1) NOT NULL DEFAULT '0',
  `needs_approval` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gatepass_tenant_number` (`tenant_id`,`gatepass_number`),
  UNIQUE KEY `uk_gatepass_tenant_id` (`id`,`tenant_id`),
  KEY `idx_gatepass_status` (`tenant_id`,`status_id`),
  KEY `idx_gatepass_type` (`tenant_id`,`gatepass_type_id`),
  KEY `idx_gatepasses_department` (`tenant_id`,`department_id`),
  KEY `idx_gatepass_created_by` (`tenant_id`,`created_by`),
  KEY `idx_gatepass_lookup` (`tenant_id`,`status_id`,`created_at`),
  KEY `fk_gatepass_visit` (`visit_id`,`tenant_id`),
  KEY `fk_gatepass_type` (`gatepass_type_id`,`tenant_id`),
  KEY `fk_gatepass_status` (`status_id`,`tenant_id`),
  KEY `fk_gatepass_department` (`department_id`,`tenant_id`),
  KEY `fk_gatepass_created_by` (`created_by`,`tenant_id`),
  KEY `fk_gatepass_checked_in_by` (`checked_in_by`),
  KEY `fk_gatepass_checked_out_by` (`checked_out_by`),
  CONSTRAINT `fk_gatepass_checked_in_by` FOREIGN KEY (`checked_in_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_checked_out_by` FOREIGN KEY (`checked_out_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_created_by` FOREIGN KEY (`created_by`, `tenant_id`) REFERENCES `users` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_department` FOREIGN KEY (`department_id`, `tenant_id`) REFERENCES `departments` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_status` FOREIGN KEY (`status_id`, `tenant_id`) REFERENCES `gatepass_statuses` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_type` FOREIGN KEY (`gatepass_type_id`, `tenant_id`) REFERENCES `gatepass_types` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gatepass_visit` FOREIGN KEY (`visit_id`, `tenant_id`) REFERENCES `visits` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.gatepasses: ~9 rows (approximately)
INSERT IGNORE INTO `gatepasses` (`id`, `tenant_id`, `visit_id`, `gatepass_type_id`, `gatepass_number`, `status_id`, `department_id`, `checked_in_by`, `checked_out_by`, `actual_in`, `actual_out`, `created_by`, `created_at`, `purpose`, `is_returnable`, `expected_return_date`, `actual_return_date`, `is_fully_returned`, `needs_approval`) VALUES
	(1, 1, 1, 2, 'DM-2026-0003', 3, NULL, 1, 1, '2026-02-22 14:37:09', '2026-02-22 18:53:07', 1, '2026-02-22 13:32:22', 'Going out for Repair', 1, '2026-02-28 00:00:00', NULL, 0, 1),
	(2, 1, NULL, 2, 'DM-2026-0004', 3, NULL, 1, 1, '2026-02-22 18:59:04', '2026-02-22 18:59:33', 1, '2026-02-22 18:21:15', 'Test Gate pass out', 0, '2026-02-25 00:00:00', NULL, 0, 1),
	(3, 1, 1, 1, 'DM-2026-0005', 3, NULL, 1, 1, '2026-02-22 20:55:36', '2026-03-04 19:34:41', 1, '2026-02-22 20:44:54', 'Test Gatepass In', 0, NULL, NULL, 0, 0),
	(4, 1, 1, 1, 'DM-2026-0006', 3, NULL, 1, 1, '2026-02-22 21:23:50', '2026-03-04 19:34:32', 1, '2026-02-22 21:22:47', 'Bringing Spare Parts', 0, NULL, NULL, 0, 0),
	(5, 1, NULL, 2, 'DM-2026-0007', 2, NULL, 1, NULL, '2026-02-25 22:04:50', NULL, 1, '2026-02-22 21:49:51', 'Test Department, flash and audit log', 1, '2026-02-26 00:00:00', NULL, 0, 0),
	(6, 1, NULL, 2, 'DM-2026-0008', 3, NULL, 1, 1, '2026-02-25 22:04:40', '2026-02-28 21:32:47', 1, '2026-02-22 21:54:11', 'Test Department, flash and audit log', 1, '2026-02-26 00:00:00', NULL, 0, 0),
	(7, 1, 5, 1, 'DM-2026-0009', 3, NULL, 3, 1, '2026-02-27 21:37:53', '2026-03-04 19:34:06', 1, '2026-02-27 17:29:07', 'Test Gate pass', 0, NULL, NULL, 0, 0),
	(8, 1, NULL, 2, 'DM-2026-0010', 2, 1, 1, 1, '2026-03-04 19:48:49', '2026-03-04 19:48:39', 3, '2026-02-27 21:37:38', 'Test Approval Process', 1, '2026-03-02 00:00:00', NULL, 0, 1),
	(9, 1, NULL, 2, 'DM-2026-0011', 3, 1, NULL, 1, NULL, '2026-03-04 19:48:37', 1, '2026-02-28 16:22:56', 'Test Department', 0, NULL, NULL, 0, 1);

-- Dumping structure for table tupass_demo.gatepass_approvals
CREATE TABLE IF NOT EXISTS `gatepass_approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `workflow_instance_id` bigint unsigned NOT NULL,
  `workflow_step_id` bigint unsigned NOT NULL,
  `approver_user_id` bigint unsigned NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `comments` text,
  `acted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ga_tenant` (`tenant_id`),
  KEY `idx_ga_status` (`tenant_id`,`status`),
  KEY `idx_ga_user_pending` (`approver_user_id`,`status`),
  KEY `idx_ga_lookup` (`tenant_id`,`approver_user_id`,`status`),
  KEY `fk_ga_instance` (`workflow_instance_id`,`tenant_id`),
  KEY `fk_ga_step` (`workflow_step_id`,`tenant_id`),
  KEY `fk_ga_user` (`approver_user_id`,`tenant_id`),
  CONSTRAINT `fk_ga_instance` FOREIGN KEY (`workflow_instance_id`, `tenant_id`) REFERENCES `gatepass_workflow_instances` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ga_step` FOREIGN KEY (`workflow_step_id`, `tenant_id`) REFERENCES `workflow_steps` (`id`, `tenant_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_ga_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ga_user` FOREIGN KEY (`approver_user_id`, `tenant_id`) REFERENCES `users` (`id`, `tenant_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.gatepass_approvals: ~8 rows (approximately)
INSERT IGNORE INTO `gatepass_approvals` (`id`, `tenant_id`, `workflow_instance_id`, `workflow_step_id`, `approver_user_id`, `status`, `comments`, `acted_at`, `created_at`) VALUES
	(1, 1, 1, 1, 1, 'approved', NULL, '2026-02-22 13:55:27', '2026-02-22 13:32:22'),
	(2, 1, 2, 1, 1, 'approved', NULL, '2026-02-22 18:54:33', '2026-02-22 18:21:15'),
	(3, 1, 3, 1, 2, 'approved', NULL, '2026-02-28 21:39:12', '2026-02-27 21:37:38'),
	(4, 1, 4, 1, 2, 'approved', NULL, '2026-02-28 21:39:07', '2026-02-28 16:22:56'),
	(5, 1, 4, 2, 4, 'approved', NULL, '2026-02-28 21:54:51', '2026-02-28 21:39:07'),
	(6, 1, 3, 2, 4, 'approved', NULL, '2026-02-28 21:55:18', '2026-02-28 21:39:12'),
	(7, 1, 4, 3, 5, 'approved', NULL, '2026-02-28 21:57:12', '2026-02-28 21:54:51'),
	(8, 1, 3, 3, 5, 'approved', NULL, '2026-02-28 21:57:15', '2026-02-28 21:55:18');

-- Dumping structure for table tupass_demo.gatepass_items
CREATE TABLE IF NOT EXISTS `gatepass_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `gatepass_id` bigint unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text,
  `quantity` int NOT NULL DEFAULT '1',
  `serial_number` varchar(255) DEFAULT NULL,
  `is_returnable` tinyint(1) NOT NULL DEFAULT '0',
  `returned_quantity` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gi_gatepass` (`gatepass_id`),
  KEY `fk_gi_tenant` (`tenant_id`),
  KEY `fk_gi_gatepass` (`gatepass_id`,`tenant_id`),
  CONSTRAINT `fk_gi_gatepass` FOREIGN KEY (`gatepass_id`, `tenant_id`) REFERENCES `gatepasses` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gi_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.gatepass_items: ~13 rows (approximately)
INSERT IGNORE INTO `gatepass_items` (`id`, `tenant_id`, `gatepass_id`, `item_name`, `description`, `quantity`, `serial_number`, `is_returnable`, `returned_quantity`, `created_at`) VALUES
	(1, 1, 1, 'HP Laptop G10', 'HP Laptop G10 for IT Department', 1, '4C434HF8G7', 1, 0, '2026-02-22 07:32:22'),
	(2, 1, 2, 'Kyocera Printer', 'Kyocera Ecosys Printer', 1, 'WDG323D378F', 1, 0, '2026-02-22 15:21:15'),
	(3, 1, 3, 'Kyocera Ecosys Toner', 'Kyocera Ecosys Toner (Black)', 1, 'SDKHW234', 0, 0, '2026-02-22 17:44:54'),
	(4, 1, 3, 'Kyocera Ecosys Toner', 'Kyocera Ecosys Toner (Magenta', 1, 'KJHD43983', 0, 0, '2026-02-22 17:44:54'),
	(5, 1, 4, 'Side Mirror', 'Engine Spare parts', 1, '323D378F', 0, 0, '2026-02-22 18:22:47'),
	(6, 1, 4, 'Wiper', 'Wiper', 1, 'DFG4YHD', 0, 0, '2026-02-22 18:22:47'),
	(7, 1, 5, 'Dell Latitude E4430', 'Dell Latitude Server', 1, 'WRK34R34JF', 0, 0, '2026-02-22 18:49:51'),
	(8, 1, 6, 'Dell Latitude E4430', 'Dell Latitude Server', 1, 'WRK34R34JF', 0, 0, '2026-02-22 18:54:11'),
	(9, 1, 7, 'HP Laptop G10', 'HP Laptop G10 for IT Department', 1, 'KDHYHG49', 0, 0, '2026-02-27 14:29:07'),
	(10, 1, 8, 'JBL Party Box', 'JBL Party Box  360P', 1, 'KD3984HV94', 0, 0, '2026-02-27 18:37:38'),
	(11, 1, 9, 'Dell Latitude E424 Laptop', 'Printer', 1, 'WDG3850HF8', 0, 0, '2026-02-28 13:22:56');

-- Dumping structure for table tupass_demo.gatepass_statuses
CREATE TABLE IF NOT EXISTS `gatepass_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `code` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gs_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_gs_tenant_code` (`tenant_id`,`code`),
  UNIQUE KEY `uk_gs_tenant_id` (`id`,`tenant_id`),
  KEY `idx_gs_tenant` (`tenant_id`),
  CONSTRAINT `fk_gs_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.gatepass_statuses: ~6 rows (approximately)
INSERT IGNORE INTO `gatepass_statuses` (`id`, `tenant_id`, `name`, `code`) VALUES
	(1, 1, 'Pending', 'PENDING'),
	(2, 1, 'Checked In', 'CHECKED_IN'),
	(3, 1, 'Checked Out', 'CHECKED_OUT'),
	(4, 1, 'Returned', 'RETURNED'),
	(5, 1, 'Approved', 'APPROVED'),
	(6, 1, 'Rejected', 'REJECTED'),
	(8, 1, 'Cancelled', 'CANCELLED');

-- Dumping structure for table tupass_demo.gatepass_types
CREATE TABLE IF NOT EXISTS `gatepass_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `type_code` varchar(20) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `workflow_id` bigint unsigned DEFAULT NULL,
  `allowed_actions` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gpt_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_gpt_tenant_id` (`id`,`tenant_id`),
  KEY `idx_gpt_tenant` (`tenant_id`),
  KEY `idx_gpt_workflow` (`workflow_id`),
  KEY `fk_gpt_workflow` (`workflow_id`,`tenant_id`),
  CONSTRAINT `fk_gpt_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gpt_workflow` FOREIGN KEY (`workflow_id`, `tenant_id`) REFERENCES `workflows` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.gatepass_types: ~6 rows (approximately)
INSERT IGNORE INTO `gatepass_types` (`id`, `tenant_id`, `name`, `type_code`, `description`, `is_active`, `created_at`, `workflow_id`, `allowed_actions`) VALUES
	(1, 1, 'Gatepass In', 'IN', 'Temporary movement of items out of premises', 1, '2026-02-20 10:07:05', 2, '["checkin", "checkout"]'),
	(2, 1, 'Gatepass Out', 'OUT', 'Permanent movement of items out of premises', 1, '2026-02-20 10:07:05', 1, '["checkin", "checkout"]'),
	(3, 1, 'Visitor', 'VISITOR', 'Visitor-related item movement', 1, '2026-02-20 10:07:05', 1, '["checkin", "checkout"]'),
	(4, 1, 'Delivery', 'DELIVERY', 'Movement between branches or departments', 1, '2026-02-20 10:07:05', 1, '["checkin", "checkout"]'),
	(5, 1, 'Repair', 'REPAIR', 'Items sent out for repair and expected to return', 1, '2026-02-20 10:07:05', 1, '["checkin", "checkout"]'),
	(6, 1, 'Contractor', 'CONTRACTOR', 'Contractor tools entering or leaving site', 1, '2026-02-20 10:07:05', 1, '["checkin", "checkout"]');

-- Dumping structure for table tupass_demo.gatepass_workflow_instances
CREATE TABLE IF NOT EXISTS `gatepass_workflow_instances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `gatepass_id` bigint unsigned NOT NULL,
  `workflow_id` bigint unsigned NOT NULL,
  `current_step_order` int NOT NULL DEFAULT '1',
  `status` enum('in_progress','approved','rejected') NOT NULL DEFAULT 'in_progress',
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gwi_gatepass` (`gatepass_id`),
  UNIQUE KEY `uk_gwi_tenant_id` (`id`,`tenant_id`),
  KEY `idx_gwi_tenant` (`tenant_id`),
  KEY `idx_gwi_status` (`tenant_id`,`status`),
  KEY `idx_gwi_lookup` (`tenant_id`,`status`,`gatepass_id`),
  KEY `fk_gwi_gatepass` (`gatepass_id`,`tenant_id`),
  KEY `fk_gwi_workflow` (`workflow_id`,`tenant_id`),
  CONSTRAINT `fk_gwi_gatepass` FOREIGN KEY (`gatepass_id`, `tenant_id`) REFERENCES `gatepasses` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gwi_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gwi_workflow` FOREIGN KEY (`workflow_id`, `tenant_id`) REFERENCES `workflows` (`id`, `tenant_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.gatepass_workflow_instances: ~5 rows (approximately)
INSERT IGNORE INTO `gatepass_workflow_instances` (`id`, `tenant_id`, `gatepass_id`, `workflow_id`, `current_step_order`, `status`, `started_at`, `completed_at`) VALUES
	(1, 1, 1, 1, 1, 'approved', '2026-02-22 13:32:22', '2026-02-22 13:55:27'),
	(2, 1, 2, 1, 1, 'approved', '2026-02-22 18:21:15', '2026-02-22 18:54:33'),
	(3, 1, 8, 1, 3, 'approved', '2026-02-27 21:37:38', '2026-02-28 21:57:15'),
	(4, 1, 9, 1, 3, 'approved', '2026-02-28 16:22:56', '2026-02-28 21:57:12');

-- Dumping structure for table tupass_demo.identification_types
CREATE TABLE IF NOT EXISTS `identification_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_idtype_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_idtype_tenant_id` (`id`,`tenant_id`),
  KEY `idx_idtypes_tenant` (`tenant_id`),
  CONSTRAINT `fk_idtypes_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.identification_types: ~2 rows (approximately)
INSERT IGNORE INTO `identification_types` (`id`, `tenant_id`, `name`) VALUES
	(1, 1, 'National ID'),
	(2, 1, 'Passport');

-- Dumping structure for table tupass_demo.modules
CREATE TABLE IF NOT EXISTS `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_module_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16324 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.modules: ~9 rows (approximately)
INSERT IGNORE INTO `modules` (`id`, `name`) VALUES
	(1, 'Dashboard'),
	(2, 'Gatepass'),
	(3, 'Visitors'),
	(4, 'Users'),
	(5, 'Reports'),
	(10, 'roles'),
	(11, 'settings'),
	(12, 'audit'),
	(13, 'Approval');

-- Dumping structure for table tupass_demo.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `action_id` bigint unsigned NOT NULL,
  `module_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_perm_action_module` (`action_id`,`module_id`),
  KEY `idx_perm_module` (`module_id`),
  CONSTRAINT `fk_perm_action` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_perm_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.permissions: ~25 rows (approximately)
INSERT IGNORE INTO `permissions` (`id`, `action_id`, `module_id`) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 2, 2),
	(4, 5, 2),
	(5, 6, 1),
	(8, 9, 2),
	(9, 4, 2),
	(11, 12, 2),
	(12, 13, 2),
	(13, 14, 2),
	(14, 2, 3),
	(15, 1, 3),
	(16, 9, 3),
	(17, 18, 3),
	(18, 2, 4),
	(19, 1, 4),
	(20, 9, 4),
	(21, 22, 4),
	(22, 2, 10),
	(23, 24, 10),
	(24, 9, 10),
	(25, 9, 11),
	(26, 1, 12),
	(27, 5, 13),
	(28, 1, 13);

-- Dumping structure for table tupass_demo.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_role_tenant_id` (`id`,`tenant_id`),
  KEY `idx_roles_tenant` (`tenant_id`),
  CONSTRAINT `fk_roles_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.roles: ~6 rows (approximately)
INSERT IGNORE INTO `roles` (`id`, `tenant_id`, `name`) VALUES
	(1, 1, 'admin'),
	(2, 1, 'Security Officer'),
	(3, 1, 'Head of Department'),
	(4, 1, 'Security Manager'),
	(5, 1, 'General Manager'),
	(6, 1, 'Staff');

-- Dumping structure for table tupass_demo.role_permissions
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `idx_rp_permission` (`permission_id`),
  KEY `idx_rp_tenant` (`tenant_id`),
  KEY `fk_rp_role` (`role_id`,`tenant_id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.role_permissions: ~70 rows (approximately)
INSERT IGNORE INTO `role_permissions` (`role_id`, `tenant_id`, `permission_id`) VALUES
	(1, 1, 1),
	(1, 1, 2),
	(1, 1, 3),
	(1, 1, 4),
	(1, 1, 5),
	(1, 1, 8),
	(1, 1, 9),
	(1, 1, 11),
	(1, 1, 12),
	(1, 1, 13),
	(1, 1, 14),
	(1, 1, 15),
	(1, 1, 16),
	(1, 1, 17),
	(1, 1, 18),
	(1, 1, 19),
	(1, 1, 20),
	(1, 1, 21),
	(1, 1, 22),
	(1, 1, 23),
	(1, 1, 24),
	(1, 1, 25),
	(1, 1, 26),
	(1, 1, 27),
	(1, 1, 28),
	(2, 1, 2),
	(3, 1, 1),
	(3, 1, 2),
	(3, 1, 3),
	(3, 1, 5),
	(3, 1, 8),
	(3, 1, 13),
	(3, 1, 27),
	(3, 1, 28),
	(4, 1, 1),
	(4, 1, 2),
	(4, 1, 3),
	(4, 1, 4),
	(4, 1, 5),
	(4, 1, 8),
	(4, 1, 13),
	(4, 1, 14),
	(4, 1, 15),
	(4, 1, 16),
	(4, 1, 17),
	(4, 1, 27),
	(4, 1, 28),
	(5, 1, 1),
	(5, 1, 2),
	(5, 1, 3),
	(5, 1, 4),
	(5, 1, 5),
	(5, 1, 8),
	(5, 1, 9),
	(5, 1, 11),
	(5, 1, 12),
	(5, 1, 13),
	(5, 1, 14),
	(5, 1, 15),
	(5, 1, 16),
	(5, 1, 17),
	(5, 1, 25),
	(5, 1, 27),
	(5, 1, 28),
	(6, 1, 1),
	(6, 1, 2),
	(6, 1, 3),
	(6, 1, 5),
	(6, 1, 14),
	(6, 1, 15);

-- Dumping structure for table tupass_demo.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `token` char(43) NOT NULL,
  `data` blob NOT NULL,
  `expiry` timestamp(6) NOT NULL,
  PRIMARY KEY (`token`),
  KEY `expiry_idx` (`expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.sessions: ~0 rows (approximately)

-- Dumping structure for table tupass_demo.tenants
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `country` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tenant_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.tenants: ~0 rows (approximately)
INSERT IGNORE INTO `tenants` (`id`, `name`, `code`, `email`, `phone`, `country`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Glee Hotel Limited', 'GLEE', 'admin@albatechsolutions.com', '+254700000000', 'Kenya', 1, '2026-02-20 07:45:46', '2026-02-25 19:46:54');

-- Dumping structure for table tupass_demo.tenant_settings
CREATE TABLE IF NOT EXISTS `tenant_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `setting_key` varchar(150) NOT NULL,
  `setting_value` json DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ts_tenant_key` (`tenant_id`,`setting_key`),
  KEY `idx_ts_tenant` (`tenant_id`),
  CONSTRAINT `fk_ts_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.tenant_settings: ~2 rows (approximately)
INSERT IGNORE INTO `tenant_settings` (`id`, `tenant_id`, `setting_key`, `setting_value`, `updated_at`) VALUES
	(1, 1, 'gatepass_numbering', '{"prefix": "DM", "padding": 4, "sequence": 15, "current_year": "2026", "include_year": true, "reset_yearly": true, "include_month": false}', NULL),
	(3, 1, 'company_profile', '{"email": "", "phone": "", "country": "", "company_name": ""}', NULL);

-- Dumping structure for table tupass_demo.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `username` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_tenant_email` (`tenant_id`,`email`),
  UNIQUE KEY `uk_user_tenant_username` (`tenant_id`,`username`),
  UNIQUE KEY `uk_user_tenant_id` (`id`,`tenant_id`),
  KEY `idx_users_tenant` (`tenant_id`),
  KEY `idx_users_dept` (`department_id`),
  KEY `fk_users_dept` (`department_id`,`tenant_id`),
  KEY `idx_users_reset_token` (`reset_token`),
  KEY `idx_users_reset_expires` (`reset_expires`),
  CONSTRAINT `fk_users_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_users_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.users: ~5 rows (approximately)
INSERT IGNORE INTO `users` (`id`, `tenant_id`, `email`, `password_hash`, `first_name`, `last_name`, `username`, `is_active`, `is_admin`, `created_at`, `updated_at`, `reset_token`, `reset_expires`, `department_id`) VALUES
	(1, 1, 'alumasinde@gmail.com', '$2y$10$tbmI5jOcqPKyCC7YITydROFwsuv1LbACubR1PTGOzy87KoPw7GMPy', 'Albert', 'Masinde', 'alumasinde', 1, 1, '2026-02-20 07:45:46', '2026-02-22 12:55:03', NULL, NULL, 1),
	(2, 1, 'hod@gmail.com', '$2y$10$HrqooVslqGc91w0FbUnf8OzAJvqUvZyFMbHAFMg5cLhbvZw1JV6xO', 'Paul', 'Mwangi', 'pmwangi', 1, 0, '2026-02-25 22:17:21', '2026-02-27 21:39:07', NULL, NULL, 1),
	(3, 1, 'user@gmail.com', '$2y$10$nu/3/KH1BUo4XaLBLSnDn.rw5HeHBX0ixEVNY3emBhpKoOeatMVDa', 'Sandra', 'Atieno', 'satieno', 1, 0, '2026-02-25 22:17:21', '2026-02-27 21:36:37', NULL, NULL, 1),
	(4, 1, 'secmanager@gmail.com', '$2y$10$sZXiDJbrFVXUuM2vRwri4.0ZBDUT1gIJ5rXGoFQ/hocvzmIoG6r9u', 'George', 'Okoth', 'gokoth', 1, 0, '2026-02-25 22:17:21', '2026-02-28 21:40:43', NULL, NULL, 2),
	(5, 1, 'gm@gmail.com', '$2y$10$k8Rjvq62rOUqjm1w1vvYDetQwSRLRo0.f2yc9B32OFEoQLwa5Mi66', 'Mercy', 'Wambui', 'mwambui', 1, 0, '2026-02-25 22:17:21', '2026-02-28 21:57:05', NULL, NULL, 4);

-- Dumping structure for table tupass_demo.user_roles
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `idx_ur_role` (`role_id`),
  KEY `idx_ur_tenant` (`tenant_id`),
  KEY `fk_ur_user` (`user_id`,`tenant_id`),
  KEY `fk_ur_role` (`role_id`,`tenant_id`),
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`, `tenant_id`) REFERENCES `roles` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`, `tenant_id`) REFERENCES `users` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.user_roles: ~6 rows (approximately)
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `tenant_id`) VALUES
	(1, 1, 1),
	(2, 3, 1),
	(3, 6, 1),
	(4, 2, 1),
	(4, 4, 1),
	(5, 5, 1);

-- Dumping structure for table tupass_demo.visitors
CREATE TABLE IF NOT EXISTS `visitors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `first_name` varchar(120) NOT NULL,
  `last_name` varchar(120) NOT NULL,
  `id_type_id` bigint unsigned DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `risk_score` int NOT NULL DEFAULT '0',
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` bigint unsigned NOT NULL DEFAULT (0),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_visitor_tenant_id` (`id`,`tenant_id`),
  KEY `idx_visitors_tenant` (`tenant_id`),
  KEY `idx_visitor_phone` (`tenant_id`,`phone`),
  KEY `idx_visitors_name` (`tenant_id`,`last_name`,`first_name`),
  KEY `fk_visitors_id_type` (`id_type_id`,`tenant_id`),
  KEY `fk_visitors_company` (`company_id`,`tenant_id`),
  CONSTRAINT `fk_visitors_company` FOREIGN KEY (`company_id`, `tenant_id`) REFERENCES `visitor_companies` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visitors_id_type` FOREIGN KEY (`id_type_id`, `tenant_id`) REFERENCES `identification_types` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visitors_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visitors: ~3 rows (approximately)
INSERT IGNORE INTO `visitors` (`id`, `tenant_id`, `first_name`, `last_name`, `id_type_id`, `id_number`, `phone`, `email`, `company_id`, `risk_score`, `is_blacklisted`, `created_at`, `created_by`) VALUES
	(1, 1, 'John', 'Doe', 1, '12345678', '+254711111111', 'johndoe@gmail.com', 1, 0, 0, '2026-02-20 07:45:46', 1),
	(2, 1, 'Amos', 'Masinde', 1, '23438903', '0733908379', 'amosbarasa@gmail.com', 2, 0, 0, '2026-02-23 18:23:02', 1),
	(3, 1, 'Jane', 'Maina', 1, '42334872', '0725064005', 'jmaina@outlook.com', 2, 0, 0, '2026-02-23 18:36:50', 1);

-- Dumping structure for table tupass_demo.visitor_companies
CREATE TABLE IF NOT EXISTS `visitor_companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vc_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_vc_tenant_id` (`id`,`tenant_id`),
  KEY `idx_vc_tenant` (`tenant_id`),
  CONSTRAINT `fk_vc_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visitor_companies: ~2 rows (approximately)
INSERT IGNORE INTO `visitor_companies` (`id`, `tenant_id`, `name`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Tech Supplies Ltd', '2026-02-20 07:45:46', NULL),
	(2, 1, 'Glee Hotel Ltd', '2026-02-23 18:36:50', '2026-03-06 19:42:54');

-- Dumping structure for table tupass_demo.visitor_watchlist
CREATE TABLE IF NOT EXISTS `visitor_watchlist` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `visitor_id` bigint unsigned NOT NULL,
  `severity` varchar(50) DEFAULT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vw_tenant` (`tenant_id`),
  KEY `idx_vw_visitor` (`visitor_id`),
  KEY `fk_vw_visitor` (`visitor_id`,`tenant_id`),
  CONSTRAINT `fk_vw_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vw_visitor` FOREIGN KEY (`visitor_id`, `tenant_id`) REFERENCES `visitors` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visitor_watchlist: ~0 rows (approximately)

-- Dumping structure for table tupass_demo.visits
CREATE TABLE IF NOT EXISTS `visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `department_id` bigint unsigned NOT NULL,
  `visitor_id` bigint unsigned NOT NULL,
  `host_user_id` bigint unsigned DEFAULT NULL,
  `visit_type_id` bigint unsigned DEFAULT NULL,
  `visit_status_id` bigint unsigned DEFAULT NULL,
  `purpose` text,
  `expected_in` datetime DEFAULT NULL,
  `expected_out` datetime DEFAULT NULL,
  `checkin_time` datetime DEFAULT NULL,
  `checkout_time` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT (now()),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_visit_tenant_id` (`id`,`tenant_id`),
  KEY `idx_visits_tenant` (`tenant_id`),
  KEY `idx_visit_lookup` (`tenant_id`,`visitor_id`),
  KEY `idx_visits_dept` (`tenant_id`,`department_id`),
  KEY `fk_visits_dept` (`department_id`,`tenant_id`),
  KEY `fk_visits_visitor` (`visitor_id`,`tenant_id`),
  KEY `fk_visits_host_user` (`host_user_id`,`tenant_id`),
  KEY `fk_visits_type` (`visit_type_id`,`tenant_id`),
  KEY `fk_visits_status` (`visit_status_id`,`tenant_id`),
  KEY `fk_visits_created_by` (`created_by`,`tenant_id`),
  CONSTRAINT `fk_visits_created_by` FOREIGN KEY (`created_by`, `tenant_id`) REFERENCES `users` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_dept` FOREIGN KEY (`department_id`, `tenant_id`) REFERENCES `departments` (`id`, `tenant_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_visits_host_user` FOREIGN KEY (`host_user_id`, `tenant_id`) REFERENCES `users` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_status` FOREIGN KEY (`visit_status_id`, `tenant_id`) REFERENCES `visit_statuses` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_type` FOREIGN KEY (`visit_type_id`, `tenant_id`) REFERENCES `visit_types` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_visitor` FOREIGN KEY (`visitor_id`, `tenant_id`) REFERENCES `visitors` (`id`, `tenant_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visits: ~6 rows (approximately)
INSERT IGNORE INTO `visits` (`id`, `tenant_id`, `department_id`, `visitor_id`, `host_user_id`, `visit_type_id`, `visit_status_id`, `purpose`, `expected_in`, `expected_out`, `checkin_time`, `checkout_time`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 1, 3, 'Deliver equipment', NULL, NULL, '2026-02-24 18:27:23', '2026-02-24 20:57:37', 1, '2026-02-20 07:45:46', '2026-02-24 20:57:37'),
	(2, 1, 1, 2, 1, 1, 3, 'Test Visit', '2026-02-24 20:04:00', '2026-02-24 21:02:00', '2026-02-24 20:57:40', '2026-02-24 21:09:16', 1, '2026-02-24 20:02:56', '2026-02-24 21:09:16'),
	(3, 1, 1, 2, 1, 2, 3, 'Test Meeting', '2026-02-24 21:45:00', '2026-02-24 22:30:00', '2026-02-24 21:45:12', '2026-02-24 21:45:47', 1, '2026-02-24 21:45:07', '2026-02-24 21:45:47'),
	(4, 1, 1, 3, 1, 2, 3, 'Test Meeting', '2026-02-25 20:00:00', '2026-02-25 20:45:00', '2026-02-25 19:48:19', '2026-02-25 19:48:33', 1, '2026-02-25 19:48:06', '2026-02-25 19:48:33'),
	(5, 1, 1, 3, 3, 1, 3, 'For a Business Meeting', '2026-02-27 18:30:00', '2026-02-27 18:30:00', '2026-02-27 17:28:23', '2026-03-01 00:31:23', 1, '2026-02-27 17:28:10', '2026-03-01 00:31:23'),
	(6, 1, 4, 1, 1, 2, 2, 'Test', '2026-03-01 00:31:00', '2026-03-01 03:31:00', '2026-03-01 00:32:07', NULL, 1, '2026-03-01 00:31:52', '2026-03-01 00:32:07');

-- Dumping structure for table tupass_demo.visit_badges
CREATE TABLE IF NOT EXISTS `visit_badges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `visit_id` bigint unsigned NOT NULL,
  `badge_code` varchar(120) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `printed_at` datetime DEFAULT NULL,
  `returned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vb_tenant_code` (`tenant_id`,`badge_code`),
  KEY `idx_vb_visit` (`visit_id`),
  KEY `fk_vb_visit` (`visit_id`,`tenant_id`),
  CONSTRAINT `fk_vb_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vb_visit` FOREIGN KEY (`visit_id`, `tenant_id`) REFERENCES `visits` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visit_badges: ~6 rows (approximately)
INSERT IGNORE INTO `visit_badges` (`id`, `tenant_id`, `visit_id`, `badge_code`, `is_active`, `printed_at`, `returned_at`) VALUES
	(1, 1, 1, 'BDG-6B8E88B6', 0, '2026-02-23 18:38:03', '2026-02-24 20:38:57'),
	(2, 1, 1, 'BDG-6A9D3057', 0, '2026-02-24 20:38:57', '2026-02-24 20:57:12'),
	(3, 1, 2, 'BDG-E67DFDA1', 1, '2026-02-24 21:03:33', '2026-02-24 21:03:39'),
	(4, 1, 2, 'BDG-FBDF51D0', 0, '2026-02-24 21:03:40', '2026-02-24 21:03:42'),
	(5, 1, 3, 'BDG-8C4F9892', 0, '2026-02-24 21:45:16', '2026-02-24 21:45:19'),
	(6, 1, 4, 'BDG-3D3964D7', 0, '2026-02-25 19:48:27', '2026-02-25 19:48:31'),
	(7, 1, 5, 'BDG-6BFF614E', 0, '2026-02-27 23:07:48', '2026-03-01 00:31:19'),
	(8, 1, 6, 'BDG-34C20133', 1, '2026-03-04 20:00:45', NULL);

-- Dumping structure for table tupass_demo.visit_statuses
CREATE TABLE IF NOT EXISTS `visit_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `code` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vs_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_vs_tenant_code` (`tenant_id`,`code`),
  UNIQUE KEY `uk_vs_tenant_id` (`id`,`tenant_id`),
  KEY `idx_vs_tenant` (`tenant_id`),
  CONSTRAINT `fk_vs_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visit_statuses: ~3 rows (approximately)
INSERT IGNORE INTO `visit_statuses` (`id`, `tenant_id`, `name`, `code`) VALUES
	(1, 1, 'Scheduled', 'scheduled'),
	(2, 1, 'Checked In', 'checked_in'),
	(3, 1, 'Completed', 'checked_out');

-- Dumping structure for table tupass_demo.visit_types
CREATE TABLE IF NOT EXISTS `visit_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vt_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_vt_tenant_id` (`id`,`tenant_id`),
  KEY `idx_vt_tenant` (`tenant_id`),
  CONSTRAINT `fk_vt_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.visit_types: ~0 rows (approximately)
INSERT IGNORE INTO `visit_types` (`id`, `tenant_id`, `name`) VALUES
	(1, 1, 'Business'),
	(2, 1, 'Meeting');

-- Dumping structure for table tupass_demo.workflows
CREATE TABLE IF NOT EXISTS `workflows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(250) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wf_tenant_name` (`tenant_id`,`name`),
  UNIQUE KEY `uk_wf_tenant_id` (`id`,`tenant_id`),
  KEY `idx_wf_tenant` (`tenant_id`),
  CONSTRAINT `fk_wf_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.workflows: ~2 rows (approximately)
INSERT IGNORE INTO `workflows` (`id`, `tenant_id`, `name`, `description`, `is_active`, `created_at`) VALUES
	(1, 1, 'Default Gatepass Workflow', 'Default Gatepass Workflow Configured', 1, '2026-02-20 07:45:47'),
	(2, 1, 'Multi-Step', 'Multi Step Approval', 1, '2026-02-21 15:33:34');

-- Dumping structure for table tupass_demo.workflow_gatepass_type
CREATE TABLE IF NOT EXISTS `workflow_gatepass_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `workflow_id` bigint unsigned NOT NULL,
  `gatepass_type_id` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_workflow_type` (`tenant_id`,`workflow_id`,`gatepass_type_id`),
  KEY `fk_wgt_workflow` (`workflow_id`),
  KEY `fk_wgt_gatepass_type` (`gatepass_type_id`),
  CONSTRAINT `fk_wgt_gatepass_type` FOREIGN KEY (`gatepass_type_id`) REFERENCES `gatepass_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wgt_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wgt_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.workflow_gatepass_type: ~2 rows (approximately)
INSERT IGNORE INTO `workflow_gatepass_type` (`id`, `tenant_id`, `workflow_id`, `gatepass_type_id`, `created_at`) VALUES
	(1, 1, 1, 1, '2026-03-01 00:29:37'),
	(2, 1, 1, 2, '2026-03-01 00:29:42');

-- Dumping structure for table tupass_demo.workflow_steps
CREATE TABLE IF NOT EXISTS `workflow_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `workflow_id` bigint unsigned NOT NULL,
  `name` varchar(150) NOT NULL DEFAULT '',
  `role_id` bigint unsigned NOT NULL,
  `step_order` int NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `department_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ws_wf_order` (`workflow_id`,`step_order`),
  UNIQUE KEY `uk_ws_tenant_id` (`id`,`tenant_id`),
  KEY `idx_ws_tenant` (`tenant_id`),
  KEY `idx_ws_role` (`role_id`),
  KEY `idx_ws_dept` (`department_id`),
  KEY `fk_ws_workflow` (`workflow_id`,`tenant_id`),
  KEY `fk_ws_role` (`role_id`,`tenant_id`),
  KEY `fk_ws_dept` (`department_id`,`tenant_id`),
  CONSTRAINT `fk_ws_dept` FOREIGN KEY (`department_id`, `tenant_id`) REFERENCES `departments` (`id`, `tenant_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ws_role` FOREIGN KEY (`role_id`, `tenant_id`) REFERENCES `roles` (`id`, `tenant_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_ws_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ws_workflow` FOREIGN KEY (`workflow_id`, `tenant_id`) REFERENCES `workflows` (`id`, `tenant_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table tupass_demo.workflow_steps: ~3 rows (approximately)
INSERT IGNORE INTO `workflow_steps` (`id`, `tenant_id`, `workflow_id`, `name`, `role_id`, `step_order`, `is_mandatory`, `department_id`) VALUES
	(1, 1, 1, 'HOD', 3, 1, 1, NULL),
	(2, 1, 1, 'Security Manager', 4, 2, 1, NULL),
	(3, 1, 1, 'General Manager', 5, 3, 0, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
