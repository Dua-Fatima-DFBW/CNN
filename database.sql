CREATE DATABASE IF NOT EXISTS `rsoa_rsoa311_24`;
USE `rsoa_rsoa311_24`;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(20) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(400) DEFAULT 'default.png',
  `status` varchar(255) DEFAULT 'Hey there! I am using WhatsApp',
  `is_online` tinyint(1) DEFAULT 0,
  `last_seen` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `incoming_msg_id` int(11) NOT NULL,
  `outgoing_msg_id` int(11) NOT NULL,
  `msg` varchar(1000) DEFAULT NULL,
  `msg_type` enum('text', 'image') DEFAULT 'text',
  `file_path` varchar(400) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `saved_user_id` int(11) NOT NULL,
  `saved_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`contact_id`),
  UNIQUE KEY `unique_contact` (`user_id`, `saved_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
