-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 27, 2026 at 09:02 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u921830511_teacherpk`
--

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) NOT NULL,
  `batch_name` varchar(200) NOT NULL,
  `batch_code` varchar(50) NOT NULL,
  `class_id` int(11) NOT NULL COMMENT 'Links to classes table (can be class or course)',
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `max_students` int(11) DEFAULT NULL COMMENT 'Maximum number of students allowed',
  `enrollment_status` enum('open','closed','full') NOT NULL DEFAULT 'open',
  `meeting_schedule` text DEFAULT NULL COMMENT 'Schedule information (e.g., Mon/Wed 7-8 PM)',
  `zoom_meeting_id` varchar(100) DEFAULT NULL,
  `zoom_meeting_link` varchar(500) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL COMMENT 'Teacher/instructor assigned to batch',
  `status` enum('active','inactive','completed','archived') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `batch_name`, `batch_code`, `class_id`, `description`, `start_date`, `end_date`, `max_students`, `enrollment_status`, `meeting_schedule`, `zoom_meeting_id`, `zoom_meeting_link`, `instructor_id`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Fahem ul Quran Korea', 'FQC-kor', 13, 'Registration Form For South Korea', '2025-11-07', '2025-12-07', 300, 'open', '08:30 PM', '85781721582', 'https://us06web.zoom.us/j/85781721582?pwd=X5WG9Zvt8hNUYwbXOmhDhoAiV9fGmL.1', NULL, 'active', 1, '2025-11-07 03:53:48', '2025-11-07 15:15:27'),
(2, 'Fahem ul Quran Hong Kong', 'FQC-HK', 13, 'a Batch created for Hong Kong people', '2025-11-10', '2025-11-27', 150, 'open', '10:00 PM', '85781721582', 'https://us06web.zoom.us/j/85781721582?pwd=X5WG9Zvt8hNUYwbXOmhDhoAiV9fGmL.1', NULL, 'active', 1, '2025-11-07 15:28:10', '2025-11-07 15:28:10');

-- --------------------------------------------------------

--
-- Table structure for table `batch_enrollments`
--

CREATE TABLE `batch_enrollments` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `enrollment_status` enum('pending','active','suspended','completed','dropped') DEFAULT 'pending',
  `progress_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Student progress in batch',
  `attendance_count` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL COMMENT 'Admin notes about student',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch_enrollments`
--

INSERT INTO `batch_enrollments` (`id`, `batch_id`, `user_id`, `enrollment_date`, `enrollment_status`, `progress_percentage`, `attendance_count`, `notes`, `approved_by`, `approved_at`, `completion_date`, `created_at`, `updated_at`) VALUES
(1, 1, 30, '2025-11-07 04:42:43', 'active', 0.00, 0, NULL, NULL, NULL, NULL, '2025-11-07 04:42:43', '2025-11-07 04:42:43'),
(2, 1, 97, '2025-11-07 04:44:52', 'active', 0.00, 0, NULL, NULL, NULL, NULL, '2025-11-07 04:44:52', '2025-11-07 04:44:52'),
(3, 1, 100, '2025-11-10 11:34:48', 'pending', 0.00, 0, '', NULL, NULL, NULL, '2025-11-10 11:34:48', '2025-11-10 11:34:48'),
(4, 2, 105, '2025-11-11 13:54:23', 'pending', 0.00, 0, '', NULL, NULL, NULL, '2025-11-11 13:54:23', '2025-11-11 13:54:23'),
(5, 2, 107, '2025-11-12 04:11:59', 'pending', 0.00, 0, '', NULL, NULL, NULL, '2025-11-12 04:11:59', '2025-11-12 04:11:59'),
(6, 2, 130, '2025-11-16 13:45:11', 'pending', 0.00, 0, '', NULL, NULL, NULL, '2025-11-16 13:45:11', '2025-11-16 13:45:11'),
(7, 2, 133, '2025-11-27 12:47:51', 'pending', 0.00, 0, '', NULL, NULL, NULL, '2025-11-27 12:47:51', '2025-11-27 12:47:51');

-- --------------------------------------------------------

--
-- Table structure for table `batch_registration_links`
--

CREATE TABLE `batch_registration_links` (
  `id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `link_token` varchar(100) NOT NULL COMMENT 'Unique token for registration link',
  `link_type` enum('public','private','one-time') DEFAULT 'public',
  `max_uses` int(11) DEFAULT NULL COMMENT 'Max number of registrations (NULL = unlimited)',
  `current_uses` int(11) DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `class_code` varchar(20) NOT NULL,
  `type` enum('class','course') NOT NULL DEFAULT 'class',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `registration_open` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'For courses: 1=open, 0=closed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `class_code`, `type`, `description`, `image`, `registration_open`, `created_at`, `updated_at`, `status`) VALUES
(13, 'Fahem ul Quran', 'FQC (Public. 01)', 'course', 'A unique multimedia Translation Quran', 'class_68e69a9894ed8_1759943320.png', 1, '2025-10-01 11:42:24', '2025-10-08 17:08:40', 'active'),
(16, 'Kids Counseling Class (Kids Festival)', 'KSC-SK', 'course', 'A class focused on the religious and moral education of children. Kids Festival  \r\nA children&amp;amp;#039;s program consisting of videos, cartoons, Quran translations, Prophetic Hadiths, Seerat-un-Nabi ?, manners and ethics, prayer, basic jurisprudential knowledge, quizzes, painting, and other activities.', NULL, 0, '2025-10-15 16:36:11', '2025-11-07 00:59:48', 'active'),
(17, 'Translation Quran for Tahfeez ul Quran', 'TR-Tahfeez', 'class', 'Multimedia teaching method to develop the ability of students in the Hifz department to understand the literal and contextual meanings of the Holy Quran', 'class_691c1ddd4765d_1763450333.jpg', 1, '2025-11-18 07:18:53', '2025-11-18 07:19:51', 'active'),
(18, 'Translation Quran Class6th', 'TRS-6th', 'class', '6th class translation Quran syllabus PTB', 'class_6960ff3ea89d3_1767964478.png', 1, '2026-01-09 13:14:38', '2026-01-15 17:30:53', 'active'),
(19, 'Aasan Tarjuma Quran Course', 'ATQC', 'course', '', NULL, 1, '2026-03-10 09:59:05', '2026-03-10 09:59:05', 'active'),
(20, 'Tarjuma-e-Quran Course Baraye Shoba Hifz', 'TQCSF', 'course', '', 'class_69bb61607b822_1773887840.jpeg', 1, '2026-03-19 02:23:01', '2026-03-19 02:37:20', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `class_inquiries`
--

CREATE TABLE `class_inquiries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL COMMENT 'Can be class or course ID',
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `preferred_time_slot` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_inquiries`
--

INSERT INTO `class_inquiries` (`id`, `user_id`, `class_id`, `whatsapp_number`, `country`, `address`, `contact_email`, `preferred_time_slot`, `status`, `admin_notes`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(5, 38, 13, '+923145778453', 'Pakistan', 'Vpo Dalwal, Teh Choa Saiden Shah, District Chakwal', 'tariq72000@gmail.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-03 07:46:33', '2025-10-01 23:58:43', '2025-10-03 07:46:33'),
(6, 40, 13, '+8210 9753 3302', 'South Korea', 'Haripur', 'safad2443@gmail.com', '7:00 PM - 7:40 PM', 'approved', 'Quick approved', 1, '2025-10-07 02:36:25', '2025-10-04 12:04:56', '2025-10-07 02:36:25'),
(7, 45, 13, '03074152753', 'South Korea', 'Village Rupochak Tehsil Zafarwal District Narowal', 'Umairsulehri786@gmail.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-07 02:36:19', '2025-10-04 14:06:02', '2025-10-07 02:36:19'),
(8, 47, 13, '+821082790786', 'South Korea', 'Busan South Korea', 'jamshaidkorea786@gmail.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-07 02:36:14', '2025-10-04 20:21:29', '2025-10-07 02:36:14'),
(9, 32, 13, '+821055980786', 'South Korea', '평택시 진위면 하북3길22', 'wasimr178@gmail.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-08 16:21:02', '2025-10-08 04:04:31', '2025-10-08 16:21:02'),
(10, 12, 13, '0307302281', 'South Korea', 'Anshan koria', 'gm.alvi@minhaj.edu.pk', '7:00 PM - 7:40 PM', 'approved', 'Quick approved', 1, '2025-10-09 01:27:30', '2025-10-08 16:26:25', '2025-10-09 01:27:30'),
(11, 64, 13, '01066267355', 'Pakistan', 'Hwasing', 'khokharadnan900@gmail.com', 'Other (if possible)', 'approved', 'Quick approved', 1, '2025-10-09 15:20:50', '2025-10-09 01:28:33', '2025-10-09 15:20:50'),
(12, 20, 13, '00821056717865', 'Pakistan', 'South korea', 'zahid.siddique77@gmail.com', '7:00 PM - 7:40 PM', 'approved', 'Quick approved', 1, '2025-10-09 15:19:25', '2025-10-09 10:23:29', '2025-10-09 15:19:25'),
(13, 33, 13, '010-4258-0786', 'South Korea', '경기도 양주시 남면 개나리5길1,2층', 'Muhammadrahmanmr883@gmail.com', '7:00 PM - 7:40 PM', 'approved', 'Quick approved', 1, '2025-10-11 23:14:49', '2025-10-10 09:32:53', '2025-10-11 23:14:49'),
(14, 79, 13, '+8201057370266', 'South Korea', '경기도 양주시 남면 개나리길114', 'wasif350@gmail.com', '7:00 PM - 7:40 PM', 'approved', 'Quick approved', 1, '2025-10-15 03:49:06', '2025-10-14 09:37:10', '2025-10-15 03:49:06'),
(15, 13, 13, '+821048980299', 'South Korea', 'South korea', 'aqeelmujahid@gmail.com', '7:00 PM - 7:40 PM', 'approved', 'Quick approved', 1, '2025-10-15 03:48:59', '2025-10-14 11:50:31', '2025-10-15 03:48:59'),
(16, 81, 13, '+821051737579', 'South Korea', 'Samileo4', 'aliamjad757kr@gmail.com', '8:20 PM - 9:00 PM', 'approved', 'Quick approved', 1, '2025-10-15 03:48:53', '2025-10-14 12:16:54', '2025-10-15 03:48:53'),
(17, 80, 13, '01059095324', 'South Korea', 'Incheon', 'alibutt50012@gnail.com', '8:20 PM - 9:00 PM', 'approved', 'Quick approved', 1, '2025-10-15 16:21:23', '2025-10-15 11:59:11', '2025-10-15 16:21:23'),
(18, 82, 13, '+852 55435390', 'China', 'Hong Kong Kwai Chung', 'anssl2509@gmail.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-15 16:21:18', '2025-10-15 14:12:04', '2025-10-15 16:21:18'),
(19, 85, 13, '00852 98277258', 'China', 'Hong kong', 'apnachhachh@gmail.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-17 09:55:46', '2025-10-16 04:24:19', '2025-10-17 09:55:46'),
(20, 87, 13, '923339013106', 'Pakistan', 'Karachi', 'write2masroor@yahoo.com', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-21 04:02:23', '2025-10-18 07:48:27', '2025-10-21 04:02:23'),
(21, 88, 13, '+923038448503', 'China', 'Hong Kong', 'm.laeeq121@hotmail.comm', '9:00 PM - 9:40 PM', 'approved', 'Quick approved', 1, '2025-10-21 04:02:30', '2025-10-20 10:41:07', '2025-10-21 04:02:30'),
(22, 37, 13, '+821073814195', 'South Korea', 'Incheon south korea', 'gulirshad@gmail.com', '8:20 PM - 9:00 PM', 'approved', 'Quick approved', 1, '2025-11-07 03:54:40', '2025-10-30 12:16:33', '2025-11-07 03:54:40'),
(23, 97, 13, '03434565658', 'Pakistan', 'Ø²Ø§ÙˆÛŒÛ Ø·Ø±ÛŒÙ‚Û Ù…Ø­Ù…Ø¯ÛŒÛ Ø­Ù‚ÛŒÙ‚Û Ú©Ú¾Ø§Ø±ÛŒØ§Úº', 'ameermkhan658@gmail.com', 'Other (if possible)', 'approved', 'Quick approved', 1, '2025-11-07 03:54:33', '2025-11-04 06:56:24', '2025-11-07 03:54:33'),
(24, 30, 13, '03184621902', 'South Korea', 'siafianspfa', 'cyberpunk3@gmail.com', '', 'approved', 'Quick approved', 1, '2025-11-07 04:42:24', '2025-11-07 04:40:30', '2025-11-07 04:42:24'),
(25, 100, 13, '03020441961', 'Pakistan', 'Mehar fayyaz colony fateh garh Lahore Pakistan', 'faizashahzad752@gmail.com', 'Evening/ Morning', 'approved', 'Quick approved', 1, '2025-11-11 11:45:37', '2025-11-09 18:32:15', '2025-11-11 11:45:37'),
(26, 105, 13, '56077014', 'China', 'Rm 4016 40/F shek cheunv house shek lei estate kwai chung N.T', 'basat4@hotmail.com', '', 'approved', 'Quick approved', 1, '2025-11-13 15:48:16', '2025-11-11 13:54:10', '2025-11-13 15:48:16'),
(27, 107, 13, '67601404', 'China', 'Flat 1715, floor 17, Kwai Hau Street, Shing Kwok House, Kwai Shing East Estate, NT.', 'areebrashid10@gmail.com', '9-10 PM', 'approved', 'Quick approved', 1, '2025-11-13 15:48:30', '2025-11-12 04:11:07', '2025-11-13 15:48:30'),
(28, 109, 13, '90180974', 'China', 'Choi Hung estate, Kowloon, Hongkong', 'afeefarehmanali@gmail.com', '', 'approved', 'Quick approved', 1, '2025-11-13 15:48:36', '2025-11-12 12:52:40', '2025-11-13 15:48:36'),
(29, 104, 13, '59770007', 'China', 'Room E,F/9,wai yin building,432 castle peak road,kwai chung.N.T', 'sehrishraja121@gmail.com', '', 'approved', 'Quick approved', 1, '2025-11-13 15:48:25', '2025-11-12 13:51:28', '2025-11-13 15:48:25'),
(30, 112, 13, '91479097', 'China', 'Hong Kong', 'asim115.ak@gmail.com', '', 'approved', 'Quick approved', 1, '2025-11-13 15:48:21', '2025-11-12 13:52:31', '2025-11-13 15:48:21'),
(31, 114, 13, '01027572320', 'South Korea', 'Ansan South Korea', 'k.hasan906@yahoo.com', '', 'approved', 'Quick approved', 1, '2025-11-13 15:48:04', '2025-11-13 09:00:26', '2025-11-13 15:48:04'),
(32, 130, 13, '62115654', 'China', 'Room 1714, 17/f, Shek Kai house, Shek Lei Estate Kwai Chung N.T. Hong Kong', 'mahmoodsonia06@gmail.com', '', 'approved', 'Quick approved', 1, '2025-11-23 11:13:38', '2025-11-16 13:44:40', '2025-11-23 11:13:38'),
(33, 133, 13, '0085268258899', 'China', '2113 floor yiu sin House wong Tai sin', 'alibabuali@yahoo.com', '', 'pending', NULL, NULL, NULL, '2025-11-27 12:47:35', '2025-11-27 12:47:35');

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `lecture_title` varchar(200) NOT NULL,
  `syllabus_id` int(11) NOT NULL,
  `lecture_type` enum('video','audio','file','text','pptx_embed','multiple') NOT NULL,
  `content_url` varchar(500) DEFAULT NULL,
  `text_content` longtext DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `lecture_order` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `lecture_title`, `syllabus_id`, `lecture_type`, `content_url`, `text_content`, `file_name`, `file_size`, `duration_minutes`, `lecture_order`, `description`, `image`, `created_at`, `updated_at`, `status`) VALUES
(1, 'introduction', 1, 'file', 'uploads/68c86cbea523b_1757965502.pdf', NULL, 'Purple Playfun Kids Award Certificate (2).pdf', 310067, NULL, 1, 'wwe', NULL, '2025-09-15 19:45:02', '2025-09-24 20:09:55', 'inactive'),
(2, '01. Lecture 1 noun in 6th syllabus 1', 1, 'file', 'uploads/68c8aedb595e8_1757982427.pptx', NULL, '01. Lecture 1 noune in 6th sylabus 1.pptx', 26474495, NULL, 2, '', NULL, '2025-09-16 00:27:07', '2025-09-24 20:10:00', 'inactive'),
(3, 'Lecture 3 Power Point', 1, 'pptx_embed', 'lectures/class-6/Lecture-3.php', NULL, NULL, NULL, NULL, 3, 'Attached Power Point file', NULL, '2025-09-17 22:12:59', '2025-09-24 20:10:06', 'inactive'),
(4, 'Lecture 4 Power Point', 1, 'pptx_embed', 'lectures/class-6/Lecture-4.php', NULL, NULL, NULL, NULL, 4, 'Attached Power Point file to view', NULL, '2025-09-17 22:14:02', '2025-09-24 20:10:10', 'inactive'),
(5, 'Lecture 2 Power Point', 1, 'pptx_embed', 'lectures/class-6/Lecture-2.php', NULL, NULL, NULL, NULL, 5, '', NULL, '2025-09-18 20:28:42', '2025-09-24 20:10:14', 'inactive'),
(6, 'Slide 01', 3, 'file', 'uploads/68df7cf6dff0a_1759476982.pdf', NULL, 'DSTP3.0-Batch-01_GRD101_3 (1).pdf', 216985, NULL, 1, '', NULL, '2025-10-03 07:36:22', '2025-10-03 07:41:00', 'inactive'),
(7, 'Briefing for Fahem ul Quran', 3, 'multiple', NULL, NULL, NULL, NULL, 40, 2, 'A unique multi-media Quran Translation Course to develop the ability to translate the Quran among the general public', 'uploads/lectures/lecture_68edbb0fa6cf7_1760410383.jpg', '2025-10-03 07:39:30', '2025-11-01 05:08:37', 'active'),
(8, '01. Lecture 1', 3, 'file', 'uploads/68edbbc8e6df8_1760410568.pdf', NULL, '01. Lecture 1 .pdf', 9432984, 40, 3, 'A unique multi-media Quran Translation Course to develop the ability to translate the Quran among the general public', 'uploads/lectures/lecture_68edb5143ff93_1760408852.jpg', '2025-10-03 07:44:39', '2025-10-14 02:56:28', 'inactive'),
(9, '02. Lecture 2 sura Fataha', 3, 'video', 'uploads/68ed01018841d_1760362753.mp4', NULL, 'تعارفی ویڈیو.mp4', 42107447, 40, 1, '', NULL, '2025-10-13 13:39:13', '2025-10-13 13:40:29', 'inactive'),
(10, 'Introductory video Fahem ul Quran', 3, 'video', 'uploads/6905934ae4afd_1761973066.mp4', NULL, 'Introductry video.mp4', 42107447, 10, 1, 'Introductory video multimedia Translation Quran', 'uploads/lectures/lecture_68edba2c020c5_1760410156.jpg', '2025-10-14 01:42:46', '2025-11-01 04:57:47', 'active'),
(11, '02. Lecture 2 sura Fataha', 3, 'file', 'uploads/68edb4dd094bf_1760408797.pdf', NULL, '02. Lecture 2 sura Fataha .pdf', 4451355, 40, 4, 'Second lecture of Fahem ul Quran course course', 'uploads/lectures/lecture_68edb4dd0b9c8_1760408797.jpg', '2025-10-14 02:15:37', '2025-10-14 02:56:35', 'inactive'),
(12, '01. Lecture 1', 3, 'file', 'uploads/68f8a6f3147af_1761126131.pdf', NULL, '01. Lecture 1 .pdf', 9432984, 40, 3, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_68edbc5c0ab0d_1760410716.jpg', '2025-10-14 02:58:36', '2025-10-22 09:42:11', 'active'),
(13, '02. Lecture 2 sura Fataha', 3, 'file', 'uploads/68edbd1102e4f_1760410897.pdf', NULL, '02. Lecture 2 sura Fataha .pdf', 4451355, 40, 4, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_68edbd1104f2a_1760410897.jpg', '2025-10-14 03:01:37', '2025-10-14 03:01:37', 'active'),
(14, '03. Lecture 3 sura al-Baqarah 1 to 5', 3, 'file', 'uploads/68edbf38bc6d0_1760411448.pdf', NULL, '03. Lecture 3 sura al-baqara 1 to 5 .pdf', 6647698, 40, 5, 'unique Translation Quran method', 'uploads/lectures/lecture_68edbf38bf19e_1760411448.jpg', '2025-10-14 03:10:48', '2025-10-14 03:10:48', 'active'),
(15, '04. Lecture sura al-baqara 6 to 10', 3, 'file', 'uploads/68edc0f512ae4_1760411893.pdf', NULL, '04. Lecture sura al-baqara 6 to 10.pdf', 6968545, 40, 6, 'unique Translation Quran method', 'uploads/lectures/lecture_68edc0be786c5_1760411838.jpg', '2025-10-14 03:17:18', '2025-10-14 03:18:13', 'active'),
(16, '05. Lecture sura al-Baqarah 11 to 15', 3, 'file', 'uploads/68f26ccc7fe78_1760718028.pdf', NULL, '05. Lecture sura al-baqara 11 to 15.pdf', 5220608, 40, 7, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_68f26ccc81e3c_1760718028.jpg', '2025-10-17 16:20:28', '2025-10-17 16:20:28', 'active'),
(17, '06. Lecture sura al-Baqarah 16 to 20', 3, 'file', 'uploads/68f271cd33f88_1760719309.pdf', NULL, '06. Lecture sura al-baqara 16 to 20 .pdf', 7250968, 40, 8, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_68f271cd36870_1760719309.jpg', '2025-10-17 16:41:49', '2025-10-17 16:41:49', 'active'),
(18, 'lecture 1', 3, 'pptx_embed', 'uploads/68f734f92183c_1761031417.pptx', NULL, '01. Lecture 1 .pptx', 16187519, 40, 1, '', NULL, '2025-10-21 07:23:37', '2025-10-21 07:26:00', 'inactive'),
(19, '14.  Lecture sura al-baqara 56 to 60', 3, 'file', 'uploads/690f8561673f1_1762624865.pdf', NULL, '14.  Lecture sura al-baqara 56 to 60.pdf', 7976138, 40, 16, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_69064dd1db474_1762020817.jpg', '2025-11-01 18:12:19', '2025-11-08 18:01:05', 'active'),
(20, '15.  Lecture sura al-baqara 61 to 65', 3, 'file', 'uploads/690f85b0b24ce_1762624944.pdf', NULL, '15.  Lecture sura al-baqara 61 to 65.pdf', 11640368, 40, 17, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_69064f901fb3a_1762021264.jpg', '2025-11-01 18:21:04', '2025-11-08 18:02:24', 'active'),
(21, '16.  Lecture sura al-Baqarah 66 to 70', 3, 'file', 'uploads/690f84b96f209_1762624697.pdf', NULL, '16.  Lecture sura al-baqara 66 to 70.pdf', 7527615, 40, 18, '', 'uploads/lectures/lecture_690f844edfb67_1762624590.jpg', '2025-11-08 17:55:36', '2025-11-08 17:58:17', 'active'),
(22, '17.  Lecture sura al-baqara 71to 75', 3, 'file', 'uploads/690f8720af5e8_1762625312.pdf', NULL, '17.  Lecture sura al-baqara 71to 75.pdf', 7181424, 40, 19, '', 'uploads/lectures/lecture_690f8720b3748_1762625312.jpg', '2025-11-08 18:08:32', '2025-11-08 18:08:32', 'active'),
(23, '18.  Lecture sura al-baqara 76 to 80', 3, 'file', 'uploads/690f87c3b3644_1762625475.pdf', NULL, '18.  Lecture sura al-baqara 76 to 80  .pdf', 9356599, 40, 20, '', 'uploads/lectures/lecture_690f87c3badb8_1762625475.jpg', '2025-11-08 18:11:15', '2025-11-08 18:11:15', 'active'),
(24, '19.  Lecture sura al-baqara 81 to 85', 3, 'file', 'uploads/690f8868050d6_1762625640.pdf', NULL, '19.  Lecture sura al-baqara 81 to 85.pdf', 8013203, NULL, 21, '', 'uploads/lectures/lecture_690f886808bc6_1762625640.jpg', '2025-11-08 18:14:00', '2025-11-08 18:14:00', 'active'),
(25, '20.  Lecture sura al-baqara 86 to 90', 3, 'file', 'uploads/690f892d820cc_1762625837.pdf', NULL, '20.  Lecture sura al-baqara 86 to 90.pdf', 7327193, 40, 22, '', 'uploads/lectures/lecture_690f892d8590d_1762625837.jpg', '2025-11-08 18:17:17', '2025-11-08 18:17:17', 'active'),
(26, '21.  Lecture sura al-baqara 91 to 100', 3, 'file', 'uploads/690f8b4c1723b_1762626380.pdf', NULL, '21.  Lecture sura al-baqara 91 to 100.pdf', 5553388, 40, 23, '', 'uploads/lectures/lecture_690f8b4c1a0c7_1762626380.jpg', '2025-11-08 18:26:20', '2025-11-08 18:26:20', 'active'),
(27, '07. Lecture sura al-baqara 21 to 25', 3, 'file', 'uploads/6915f5a47428e_1763046820.pdf', NULL, '07. Lecture sura al-baqara 21 to 25.pdf', 8489535, NULL, 9, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f579862c3_1763046777.jpg', '2025-11-13 15:12:21', '2025-11-13 15:13:40', 'active'),
(28, '08. Lecture sura al-baqara 26 to 30', 3, 'file', 'uploads/6915f6109d67c_1763046928.pdf', NULL, '08. Lecture sura al-baqara 26 to 30.pdf', 9319664, 40, 10, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f610a2a02_1763046928.jpg', '2025-11-13 15:15:28', '2025-11-13 15:15:28', 'active'),
(29, '09.  Lecture sura al-baqara 31 to 35', 3, 'file', 'uploads/6915f8f312f4f_1763047667.pdf', NULL, '09.  Lecture sura al-baqara 31 to 35.pdf', 7275178, 40, 11, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f6c5e5b8b_1763047109.jpg', '2025-11-13 15:18:29', '2025-11-13 15:27:47', 'active'),
(30, '10.  Lecture sura al-baqara 36 to 40', 3, 'file', 'uploads/691c2fa54d6fa_1763454885.pdf', NULL, '10.  Lecture sura al-baqara 36 to 40.pdf', 7320044, 40, 12, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f89e3276b_1763047582.jpg', '2025-11-13 15:26:22', '2025-11-18 08:34:45', 'active'),
(31, '11.  Lecture sura al-baqara 41 to 45', 3, 'file', 'uploads/6915f9559d076_1763047765.pdf', NULL, '11.  Lecture sura al-baqara 41 to 45.pdf', 10708865, 40, 13, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f955a28a0_1763047765.jpg', '2025-11-13 15:29:25', '2025-11-13 15:29:25', 'active'),
(32, '12.  Lecture sura al-baqara 46 to 50', 3, 'file', 'uploads/6915f9b4b7fdf_1763047860.pdf', NULL, '12.  Lecture sura al-baqara 46 to 50.pdf', 7216806, 40, 14, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f9b4bb593_1763047860.jpg', '2025-11-13 15:31:00', '2025-11-13 15:31:00', 'active'),
(33, '13.  Lecture sura al-baqara 51 to 55', 3, 'file', 'uploads/6915f9e91ad2b_1763047913.pdf', NULL, '13.  Lecture sura al-baqara 51 to 55.pdf', 9311852, NULL, 15, 'unique multimedia Translation Quran method', 'uploads/lectures/lecture_6915f9e91f950_1763047913.jpg', '2025-11-13 15:31:53', '2025-11-13 15:31:53', 'active'),
(34, '22.  Lecture sura al-baqara 101 to 110', 3, 'file', 'uploads/6915fa52579b7_1763048018.pdf', NULL, '22.  Lecture sura al-baqara 101 to 110.pdf', 7234206, 40, 24, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_6915fa525b992_1763048018.jpg', '2025-11-13 15:33:38', '2025-11-13 15:33:38', 'active'),
(35, '23.  Lecture sura al-baqara 111 to 120', 3, 'file', 'uploads/6915fa9820fb2_1763048088.pdf', NULL, '23.  Lecture sura al-baqara 111 to 120.pdf', 8125487, 40, 25, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_6915fa98254f5_1763048088.jpg', '2025-11-13 15:34:48', '2025-11-13 15:34:48', 'active'),
(36, '25. Lecture sura al-baqara 131 to 141', 3, 'file', 'uploads/6915fbb4e7fd7_1763048372.pdf', NULL, '25. Lecture sura al-baqara 131 to 141.pdf', 7081049, 40, 27, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_6915fbb4eb716_1763048372.jpg', '2025-11-13 15:39:32', '2025-11-13 15:39:32', 'active'),
(37, '24.  Lecture sura al-baqara 121 to 130', 3, 'audio', 'uploads/6915fc6f7f0c0_1763048559.pdf', NULL, '24.  Lecture sura al-baqara 121 to 130.pdf', 7468818, 40, 26, 'Unique multimedia translation Quran method', 'uploads/lectures/lecture_6915fc6f8391d_1763048559.jpg', '2025-11-13 15:42:39', '2025-11-13 15:42:39', 'active'),
(38, '02. Lecture 2 Nouns Para 1', 5, 'multiple', NULL, NULL, NULL, NULL, 30, 2, 'Multimedia teaching method to develop the ability of students in the Hifz depart', 'uploads/lectures/lecture_691c23aeaae4b_1763451822.jpg', '2025-11-18 07:43:42', '2026-01-08 19:06:28', 'inactive'),
(39, '03. Lecture 3 Multi-use Letters', 5, 'file', 'uploads/691eb0ac6d073_1763618988.pdf', NULL, '03. Lecture 3 Multi-use Leters .pdf', 5326845, 30, 3, 'Multimedia teaching method to develop the ability of students in the Hifz department to understand the literal and contextual meanings of the Holy Quran', 'uploads/lectures/lecture_691eb0ac70449_1763618988.jpg', '2025-11-20 06:09:48', '2026-01-08 19:06:11', 'inactive'),
(40, '01.Brefing for Quran', 5, 'multiple', NULL, NULL, NULL, NULL, 30, 1, 'Multimedia teaching method to develop the ability of students in the Hifz department to understand the literal and contextual meanings of the Holy Quran', 'uploads/lectures/lecture_691eb8a91126e_1763621033.jpg', '2025-11-20 06:43:53', '2026-01-08 19:06:42', 'inactive'),
(41, 'Brefing for translation Tarjuma a Quran', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_69686708e336f_1768449800.png', '2026-01-13 17:11:56', '2026-01-15 04:03:20', 'active'),
(42, '02. Lecture 2 Nouns', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 2, '', 'uploads/lectures/lecture_69686806c990d_1768450054.png', '2026-01-13 17:14:26', '2026-01-15 04:07:34', 'active'),
(43, 'Lecture 3 letters (Haroof)', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 3, '', 'uploads/lectures/lecture_69686934423dc_1768450356.png', '2026-01-13 17:17:10', '2026-01-15 04:12:36', 'active'),
(44, '04. Sura Fataha', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 4, '', 'uploads/lectures/lecture_69686995555b1_1768450453.png', '2026-01-13 17:18:49', '2026-01-15 04:14:13', 'active'),
(45, '05. Sura Baqara 1-5', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 5, '', 'uploads/lectures/lecture_69686a191af3c_1768450585.png', '2026-01-13 17:20:25', '2026-01-15 04:16:25', 'active'),
(46, 'Lecture 1', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_6967b3053748b_1768403717.png', '2026-01-14 14:01:09', '2026-01-14 15:15:17', 'active'),
(47, 'Lecture 2', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 2, '', 'uploads/lectures/lecture_6967b46f2e1a6_1768404079.png', '2026-01-14 14:07:11', '2026-01-14 15:21:19', 'active'),
(48, 'Lecture 3', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 3, '', 'uploads/lectures/lecture_6967bb33cdd1f_1768405811.png', '2026-01-14 14:08:22', '2026-01-15 03:38:35', 'active'),
(49, 'Lecture 4', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 4, '', 'uploads/lectures/lecture_6967c63a5037b_1768408634.png', '2026-01-14 14:08:54', '2026-01-14 16:37:14', 'active'),
(50, 'Lecture 5', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 5, '', 'uploads/lectures/lecture_6967c76a9c029_1768408938.png', '2026-01-14 14:09:44', '2026-01-14 16:42:18', 'active'),
(51, 'Lecture 6', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 6, '', NULL, '2026-01-30 19:01:13', '2026-01-30 19:04:33', 'active'),
(52, 'Lecture 7', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 7, '', NULL, '2026-01-30 19:02:48', '2026-01-30 19:02:48', 'active'),
(53, 'Lecture 8', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 8, '', NULL, '2026-01-30 19:03:25', '2026-01-30 19:03:25', 'active'),
(54, 'Lecture 9', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 9, '', NULL, '2026-01-30 19:04:49', '2026-01-30 19:05:46', 'active'),
(55, 'Lecture 10', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 10, '', NULL, '2026-01-30 19:05:24', '2026-01-30 19:05:24', 'active'),
(56, 'Grammar', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 11, '', NULL, '2026-01-30 19:39:05', '2026-01-30 19:39:05', 'active'),
(57, 'Test', 6, 'multiple', NULL, NULL, NULL, NULL, NULL, 12, '', NULL, '2026-01-30 19:40:07', '2026-01-30 19:40:07', 'active'),
(58, 'Lecture 6', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 6, '', NULL, '2026-02-02 15:26:05', '2026-02-02 15:26:05', 'active'),
(59, 'Lecture 7', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 7, '', NULL, '2026-02-02 16:07:39', '2026-02-02 16:07:39', 'active'),
(60, 'Lecture 8', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 8, '', NULL, '2026-02-02 16:10:46', '2026-02-02 16:10:46', 'active'),
(61, 'Lecture 9', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 9, '', NULL, '2026-02-02 16:27:57', '2026-02-02 16:27:57', 'active'),
(62, 'Lecture 10', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 10, '', NULL, '2026-02-02 16:41:09', '2026-02-02 16:41:09', 'active'),
(63, 'Lecture 11', 5, 'multiple', NULL, NULL, NULL, NULL, NULL, 11, '', NULL, '2026-02-02 16:42:27', '2026-02-02 16:42:27', 'active'),
(64, 'Aasan Tarjuma Quran Course(Lec 1)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, 'Lacture NO 1 to Lacture 7', 'uploads/lectures/lecture_69affa62c07d9_1773140578.jpeg', '2026-03-10 11:02:58', '2026-03-10 11:02:58', 'active'),
(65, 'Aasan Tarjuma Quran Course(Lec 2)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 2, 'Lacture no 2', 'uploads/lectures/lecture_69affaeb13f73_1773140715.jpeg', '2026-03-10 11:05:15', '2026-03-10 11:10:35', 'active'),
(66, 'Aasan Tarjuma Quran Course(Lec3)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 3, '', 'uploads/lectures/lecture_69affb35b3265_1773140789.jpeg', '2026-03-10 11:06:29', '2026-03-10 11:12:35', 'active'),
(67, 'Aasan Tarjuma Quran Course(Lec 4)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_69affb7479b4c_1773140852.jpeg', '2026-03-10 11:07:32', '2026-03-10 11:13:17', 'inactive'),
(68, 'Aasan Tarjuma Quran Course(Lec 4)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_69affb78050c0_1773140856.jpeg', '2026-03-10 11:07:36', '2026-03-10 11:13:23', 'inactive'),
(69, 'Aasan Tarjuma Quran Course(Lec 4)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 4, '', 'uploads/lectures/lecture_69affd1e54f30_1773141278.jpeg', '2026-03-10 11:14:38', '2026-03-10 11:14:38', 'active'),
(70, 'Aasan Tarjuma Quran Course(Lec 5)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 5, '', 'uploads/lectures/lecture_69affd8887322_1773141384.jpeg', '2026-03-10 11:16:24', '2026-03-10 11:16:24', 'active'),
(71, 'Aasan Tarjuma Quran Course(Lec 6)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 6, '', 'uploads/lectures/lecture_69affe1e31f81_1773141534.jpeg', '2026-03-10 11:18:54', '2026-03-10 11:18:54', 'active'),
(72, 'Aasan Tarjuma Quran Course(Lec 7)', 7, 'multiple', NULL, NULL, NULL, NULL, NULL, 7, '', 'uploads/lectures/lecture_69affeb3ce23a_1773141683.jpeg', '2026-03-10 11:21:23', '2026-03-10 11:21:23', 'active'),
(73, 'Aasan Tarjuma Quran Course(Lec 8)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 8, '', 'uploads/lectures/lecture_69afff2eda583_1773141806.jpeg', '2026-03-10 11:23:26', '2026-03-10 11:23:26', 'active'),
(74, 'Aasan Tarjuma Quran Course(Lec 9)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 9, '', 'uploads/lectures/lecture_69afff6ed0680_1773141870.jpeg', '2026-03-10 11:24:30', '2026-03-10 11:24:30', 'active'),
(75, 'Aasan Tarjuma Quran Course(Lec 10)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 10, '', 'uploads/lectures/lecture_69b0084645e9a_1773144134.jpeg', '2026-03-10 12:02:14', '2026-03-10 12:02:14', 'active'),
(76, 'Aasan Tarjuma Quran Course(Lec 11)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 11, '', 'uploads/lectures/lecture_69b00893adbf3_1773144211.jpeg', '2026-03-10 12:03:31', '2026-03-10 12:03:31', 'active'),
(77, 'Aasan Tarjuma Quran Course(Lec 12)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 12, '', 'uploads/lectures/lecture_69b008e5b8ef4_1773144293.jpeg', '2026-03-10 12:04:53', '2026-03-10 12:04:53', 'active'),
(78, 'Aasan Tarjuma Quran Course(Lec 13)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 13, '', 'uploads/lectures/lecture_69b0091765f12_1773144343.jpeg', '2026-03-10 12:05:43', '2026-03-10 12:05:43', 'active'),
(79, 'Aasan Tarjuma Quran Course(Lec 14)', 8, 'multiple', NULL, NULL, NULL, NULL, NULL, 14, '', 'uploads/lectures/lecture_69b00954a79d1_1773144404.jpeg', '2026-03-10 12:06:44', '2026-03-10 12:06:44', 'active'),
(80, 'Aasan Tarjuma Quran Course(Lec 15)', 9, 'multiple', NULL, NULL, NULL, NULL, 1, 15, '', 'uploads/lectures/lecture_69b00b6489ecc_1773144932.jpeg', '2026-03-10 12:15:32', '2026-03-10 12:15:32', 'active'),
(81, 'Aasan Tarjuma Quran Course(Lec 16)', 9, 'multiple', NULL, NULL, NULL, NULL, 1, 16, '', NULL, '2026-03-10 12:18:08', '2026-03-10 12:18:34', 'inactive'),
(82, 'Aasan Tarjuma Quran Course(Lec 16)', 9, 'multiple', NULL, NULL, NULL, NULL, 1, 16, '', 'uploads/lectures/lecture_69b00c086f949_1773145096.jpeg', '2026-03-10 12:18:16', '2026-03-10 12:18:16', 'active'),
(83, 'Aasan Tarjuma Quran Course(Lec 17)', 9, 'multiple', NULL, NULL, NULL, NULL, 1, 17, '', 'uploads/lectures/lecture_69b00c73525ca_1773145203.jpeg', '2026-03-10 12:20:03', '2026-03-10 12:20:03', 'active'),
(84, 'Aasan Tarjuma Quran Course(Lec 18)', 9, 'multiple', NULL, NULL, NULL, NULL, NULL, 18, '', 'uploads/lectures/lecture_69b00cc4c4f87_1773145284.jpeg', '2026-03-10 12:21:24', '2026-03-10 12:21:24', 'active'),
(85, 'Aasan Tarjuma Quran Course(Lec 19)', 9, 'multiple', NULL, NULL, NULL, NULL, NULL, 19, '', 'uploads/lectures/lecture_69b00d1b21e71_1773145371.jpeg', '2026-03-10 12:22:51', '2026-03-10 12:22:51', 'active'),
(86, 'Aasan Tarjuma Quran Course(Lec 20)', 9, 'multiple', NULL, NULL, NULL, NULL, NULL, 20, '', NULL, '2026-03-10 12:25:07', '2026-03-10 12:25:29', 'inactive'),
(87, 'Aasan Tarjuma Quran Course(Lec 20)', 9, 'multiple', NULL, NULL, NULL, NULL, NULL, 20, '', 'uploads/lectures/lecture_69b00e15df340_1773145621.jpeg', '2026-03-10 12:27:01', '2026-03-10 12:27:01', 'active'),
(88, 'Aasan Tarjuma Quran Course(Lec 21)', 9, 'multiple', NULL, NULL, NULL, NULL, NULL, 21, '', 'uploads/lectures/lecture_69b0108d29b7d_1773146253.jpeg', '2026-03-10 12:37:33', '2026-03-10 12:37:33', 'active'),
(89, 'Aasan Tarjuma Quran Course(Lec 22)', 10, 'multiple', NULL, NULL, NULL, NULL, NULL, 22, '', 'uploads/lectures/lecture_69b0143fa65ab_1773147199.jpeg', '2026-03-10 12:53:19', '2026-03-10 12:53:19', 'active'),
(90, 'Aasan Tarjuma Quran Course(Lec 23)', 10, 'multiple', NULL, NULL, NULL, NULL, NULL, 23, '', 'uploads/lectures/lecture_69b0154a6b677_1773147466.jpeg', '2026-03-10 12:57:46', '2026-03-10 12:57:46', 'active'),
(91, 'Aasan Tarjuma Quran Course(Lec 24)', 10, 'multiple', NULL, NULL, NULL, NULL, NULL, 24, '', 'uploads/lectures/lecture_69b015c26537e_1773147586.jpeg', '2026-03-10 12:59:46', '2026-03-10 12:59:46', 'active'),
(92, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 1)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_69bb6353c4151_1773888339.jpeg', '2026-03-19 02:45:39', '2026-03-31 10:59:42', 'inactive'),
(93, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 2)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 2, '', 'uploads/lectures/lecture_69bc3cce1dfcf_1773944014.jpeg', '2026-03-19 18:13:34', '2026-03-31 10:58:44', 'active'),
(94, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 2)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 2, '', 'uploads/lectures/lecture_69bc3cdab8a59_1773944026.jpeg', '2026-03-19 18:13:46', '2026-03-19 18:15:11', 'inactive'),
(95, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 3)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 3, '', 'uploads/lectures/lecture_69bc3eac07b4e_1773944492.jpeg', '2026-03-19 18:21:32', '2026-03-19 18:21:32', 'active'),
(96, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 4)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 4, '', 'uploads/lectures/lecture_69bc3f06ba3e6_1773944582.jpeg', '2026-03-19 18:23:02', '2026-03-19 18:23:02', 'active'),
(97, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 5)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 5, '', 'uploads/lectures/lecture_69bc3fe747dbc_1773944807.jpeg', '2026-03-19 18:26:47', '2026-03-19 18:26:47', 'active'),
(98, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 6)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 6, '', 'uploads/lectures/lecture_69bc4046cd4bc_1773944902.jpeg', '2026-03-19 18:28:22', '2026-03-19 18:28:22', 'active'),
(99, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 7)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 7, '', 'uploads/lectures/lecture_69bc409d24b75_1773944989.jpeg', '2026-03-19 18:29:49', '2026-03-19 18:29:49', 'active'),
(100, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 8)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 8, '', 'uploads/lectures/lecture_69bc40ddaf9ee_1773945053.jpeg', '2026-03-19 18:30:53', '2026-03-19 18:34:20', 'inactive'),
(101, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 9)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 9, '', 'uploads/lectures/lecture_69bc4114637be_1773945108.jpeg', '2026-03-19 18:31:48', '2026-03-19 18:34:48', 'active'),
(102, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 8)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 8, '', 'uploads/lectures/lecture_69bc424453a13_1773945412.jpeg', '2026-03-19 18:36:52', '2026-03-19 18:36:52', 'active'),
(103, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 10)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 10, '', 'uploads/lectures/lecture_69bc42be75faf_1773945534.jpeg', '2026-03-19 18:38:54', '2026-03-19 18:38:54', 'active'),
(104, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 11)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 11, '', 'uploads/lectures/lecture_69bc430baab25_1773945611.jpeg', '2026-03-19 18:40:11', '2026-03-19 18:40:11', 'active'),
(105, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 12)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 12, '', 'uploads/lectures/lecture_69bc43468db4f_1773945670.jpeg', '2026-03-19 18:41:10', '2026-03-19 18:41:10', 'active'),
(106, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 13)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 13, '', 'uploads/lectures/lecture_69bc43885e780_1773945736.jpeg', '2026-03-19 18:42:16', '2026-03-19 18:42:16', 'active'),
(107, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 14)', 12, 'multiple', NULL, NULL, NULL, NULL, NULL, 14, '', 'uploads/lectures/lecture_69bc440b313ec_1773945867.jpeg', '2026-03-19 18:44:27', '2026-03-19 18:44:27', 'active'),
(108, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 15)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_69bc448f27b9f_1773945999.jpeg', '2026-03-19 18:46:39', '2026-03-19 18:46:39', 'active'),
(109, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 16)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 16, '', 'uploads/lectures/lecture_69bc44ff11891_1773946111.jpeg', '2026-03-19 18:48:31', '2026-03-19 18:48:31', 'active'),
(110, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 17)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 17, '', 'uploads/lectures/lecture_69bc461912ecc_1773946393.jpeg', '2026-03-19 18:53:13', '2026-03-19 18:53:13', 'active'),
(111, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 18)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 18, '', 'uploads/lectures/lecture_69bc4671128a7_1773946481.jpeg', '2026-03-19 18:54:41', '2026-03-19 18:54:41', 'active'),
(112, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 19)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 19, '', 'uploads/lectures/lecture_69bc46d214cf3_1773946578.jpeg', '2026-03-19 18:56:18', '2026-03-19 18:56:18', 'active'),
(113, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 20)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 20, '', 'uploads/lectures/lecture_69bc472eb3c09_1773946670.jpeg', '2026-03-19 18:57:50', '2026-03-19 18:57:50', 'active'),
(114, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 21)', 13, 'multiple', NULL, NULL, NULL, NULL, NULL, 21, '', 'uploads/lectures/lecture_69bc47cfcb43b_1773946831.jpeg', '2026-03-19 19:00:31', '2026-03-19 19:00:31', 'active'),
(115, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 22)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 22, '', 'uploads/lectures/lecture_69bc50b51ee7d_1773949109.jpeg', '2026-03-19 19:38:29', '2026-03-19 19:38:29', 'active'),
(116, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 23)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 23, '', 'uploads/lectures/lecture_69bc5103d39e0_1773949187.jpeg', '2026-03-19 19:39:47', '2026-03-19 19:39:47', 'active'),
(117, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 24)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 24, '', 'uploads/lectures/lecture_69bc514561f86_1773949253.jpeg', '2026-03-19 19:40:53', '2026-03-19 19:40:53', 'active'),
(118, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 25)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 25, '', 'uploads/lectures/lecture_69bc51efaa335_1773949423.jpeg', '2026-03-19 19:43:43', '2026-03-19 19:43:43', 'active'),
(119, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 26)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 26, '', 'uploads/lectures/lecture_69bc52506eb8b_1773949520.jpeg', '2026-03-19 19:45:20', '2026-03-19 19:45:20', 'active'),
(120, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 27)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 27, '', 'uploads/lectures/lecture_69bc52b4b04ac_1773949620.jpeg', '2026-03-19 19:47:00', '2026-03-19 19:47:00', 'active'),
(121, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 28)', 14, 'multiple', NULL, NULL, NULL, NULL, NULL, 28, '', 'uploads/lectures/lecture_69bc530f07591_1773949711.jpeg', '2026-03-19 19:48:31', '2026-03-19 19:48:31', 'active'),
(122, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 29)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 29, '', 'uploads/lectures/lecture_69bc53b78d048_1773949879.jpeg', '2026-03-19 19:51:19', '2026-03-19 19:51:19', 'active'),
(123, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 30)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 30, '', 'uploads/lectures/lecture_69bc54dcb9e7b_1773950172.jpeg', '2026-03-19 19:56:12', '2026-03-19 19:56:12', 'active'),
(124, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 31)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 31, '', 'uploads/lectures/lecture_69bc554f250d1_1773950287.jpeg', '2026-03-19 19:58:07', '2026-03-19 19:58:07', 'active'),
(125, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 32)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 32, '', 'uploads/lectures/lecture_69bc55b098b98_1773950384.jpeg', '2026-03-19 19:59:44', '2026-03-19 19:59:44', 'active'),
(126, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 33)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 33, '', 'uploads/lectures/lecture_69bc56307ea61_1773950512.jpeg', '2026-03-19 20:01:52', '2026-03-19 20:01:52', 'active'),
(127, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 34)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 34, '', 'uploads/lectures/lecture_69bc56943f1cc_1773950612.jpeg', '2026-03-19 20:03:32', '2026-03-19 20:03:32', 'active'),
(128, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 35)', 15, 'multiple', NULL, NULL, NULL, NULL, NULL, 35, '', 'uploads/lectures/lecture_69cb8e2632cc9_1774947878.jpeg', '2026-03-19 20:06:12', '2026-03-31 09:04:38', 'active'),
(129, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 36)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 36, '', 'uploads/lectures/lecture_69cb8ed3ee585_1774948051.jpeg', '2026-03-31 09:07:31', '2026-03-31 09:07:31', 'active'),
(130, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 37)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 37, '', 'uploads/lectures/lecture_69cb8f6e79df0_1774948206.jpeg', '2026-03-31 09:10:06', '2026-03-31 09:10:06', 'active'),
(131, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 38)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 38, '', 'uploads/lectures/lecture_69cb90061cf48_1774948358.jpeg', '2026-03-31 09:12:38', '2026-03-31 09:12:38', 'active'),
(132, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 39)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 39, '', 'uploads/lectures/lecture_69cb90a17a961_1774948513.jpeg', '2026-03-31 09:15:13', '2026-03-31 09:15:13', 'active'),
(133, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 40)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 40, '', 'uploads/lectures/lecture_69cb9105b3998_1774948613.jpeg', '2026-03-31 09:16:53', '2026-03-31 09:16:53', 'active'),
(134, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 41)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 41, '', 'uploads/lectures/lecture_69cb92286516c_1774948904.jpeg', '2026-03-31 09:21:44', '2026-03-31 09:21:44', 'active'),
(135, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 42)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 42, '', 'uploads/lectures/lecture_69cb92a9bc6d5_1774949033.jpeg', '2026-03-31 09:23:53', '2026-03-31 09:23:53', 'active'),
(136, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 43)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 43, '', NULL, '2026-03-31 09:27:27', '2026-03-31 09:30:32', 'inactive'),
(137, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 43)', 16, 'multiple', NULL, NULL, NULL, NULL, NULL, 43, '', 'uploads/lectures/lecture_69cb947714427_1774949495.jpeg', '2026-03-31 09:31:35', '2026-03-31 09:31:35', 'active'),
(138, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 44)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 44, '', 'uploads/lectures/lecture_69cb94b2a380f_1774949554.jpeg', '2026-03-31 09:32:34', '2026-03-31 09:32:34', 'active'),
(139, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 45)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 45, '', 'uploads/lectures/lecture_69cb94ea1677a_1774949610.jpeg', '2026-03-31 09:33:30', '2026-03-31 09:33:30', 'active'),
(140, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 46)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 46, '', 'uploads/lectures/lecture_69cb952489c2c_1774949668.jpeg', '2026-03-31 09:34:28', '2026-03-31 09:34:28', 'active'),
(141, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 47)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 47, '', 'uploads/lectures/lecture_69cb956e16f25_1774949742.jpeg', '2026-03-31 09:35:42', '2026-03-31 09:35:42', 'active'),
(142, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 48)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 48, '', 'uploads/lectures/lecture_69cb95a80775d_1774949800.jpeg', '2026-03-31 09:36:40', '2026-03-31 09:36:40', 'active'),
(143, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 49)', 17, 'multiple', NULL, NULL, NULL, NULL, NULL, 49, '', 'uploads/lectures/lecture_69cb95e7ef0bf_1774949863.jpeg', '2026-03-31 09:37:43', '2026-03-31 09:37:43', 'active'),
(144, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 50)', 18, 'multiple', NULL, NULL, NULL, NULL, NULL, 50, '', 'uploads/lectures/lecture_69cb96ba7c78c_1774950074.jpeg', '2026-03-31 09:41:14', '2026-03-31 09:41:14', 'active'),
(145, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 51)', 18, 'multiple', NULL, NULL, NULL, NULL, NULL, 51, '', 'uploads/lectures/lecture_69cb970f13281_1774950159.jpeg', '2026-03-31 09:42:39', '2026-03-31 09:42:39', 'active'),
(146, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 52)', 18, 'multiple', NULL, NULL, NULL, NULL, NULL, 52, '', 'uploads/lectures/lecture_69cb97938b602_1774950291.jpeg', '2026-03-31 09:44:51', '2026-03-31 09:44:51', 'active'),
(147, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 53)', 18, 'multiple', NULL, NULL, NULL, NULL, NULL, 53, '', 'uploads/lectures/lecture_69cb97d23bbe5_1774950354.jpeg', '2026-03-31 09:45:54', '2026-03-31 10:11:29', 'active'),
(148, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 1)', 11, 'pptx_embed', 'uploads/69cba6863c29e_1774954118.pptx', NULL, '01.Brefing for translation Tarjuma a Quran .pptx', 22011004, NULL, 1, '', 'uploads/lectures/lecture_69cba68646e4a_1774954118.jpeg', '2026-03-31 10:48:38', '2026-03-31 10:48:58', 'inactive'),
(149, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 1)', 11, 'multiple', NULL, NULL, NULL, NULL, NULL, 1, '', 'uploads/lectures/lecture_69cba94f12161_1774954831.jpeg', '2026-03-31 11:00:31', '2026-03-31 11:00:31', 'active'),
(150, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (Lec 1)', 11, 'pptx_embed', 'uploads/69cba9b485736_1774954932.pptx', NULL, '01.Brefing for translation Tarjuma a Quran .pptx', 22011004, NULL, 1, '', 'uploads/lectures/lecture_69cba9b49162f_1774954932.jpeg', '2026-03-31 11:02:12', '2026-03-31 11:11:11', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `lecture_files`
--

CREATE TABLE `lecture_files` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `file_type` enum('pdf','pptx','video','audio','text') NOT NULL,
  `file_url` text DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `text_content` longtext DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `display_order` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecture_files`
--

INSERT INTO `lecture_files` (`id`, `lecture_id`, `file_type`, `file_url`, `file_name`, `file_size`, `text_content`, `duration_minutes`, `display_order`, `created_at`, `updated_at`, `status`) VALUES
(1, 7, 'pdf', 'uploads/68f6bddf74f5c_1761000927.pdf', '68edbb0fa322b_1760410383.pdf', 10617809, NULL, NULL, 1, '2025-10-20 22:55:27', '2025-10-20 22:55:27', 'active'),
(2, 7, 'pptx', 'lectures\\fehm-ul-quran\\briefing\\index.html', NULL, NULL, NULL, NULL, 2, '2025-10-20 22:55:27', '2025-10-20 22:55:27', 'active'),
(3, 12, 'pdf', 'uploads/68f796f51fb94_1761056501.pdf', '01. Lecture 1 .pdf', 9432984, NULL, NULL, 1, '2025-10-21 14:21:41', '2025-10-21 14:21:41', 'active'),
(4, 12, 'video', 'uploads/68f796f52497c_1761056501.mp4', 'video1435652764.mp4', 1186563, NULL, 0, 2, '2025-10-21 14:21:41', '2025-10-21 14:21:41', 'active'),
(5, 12, 'pdf', 'uploads/68f7a89e768b2_1761061022.pdf', '01. Lecture 1 .pdf', 9432984, NULL, NULL, 1, '2025-10-21 15:37:02', '2025-10-21 15:37:02', 'active'),
(6, 38, 'pdf', 'uploads/691eabdfc6cc1_1763617759.pdf', '02. Lecture 2 Nouns para 1.pdf', 8086123, NULL, NULL, 1, '2025-11-20 05:49:19', '2025-11-20 05:49:19', 'active'),
(7, 38, 'pptx', 'file:///E:/Final%20work%20Translation%20Quran/%D8%AA%D8%B1%D8%AC%DB%81%20%D9%82%D8%B1%D8%A7%D9%86%20%D8%A8%D8%B1%D8%A7%D8%A6%DB%92%20%D8%AD%D9%81%D8%B8/02.%20Lecture%202%20Nouns%20para%201%20(Published)/index.html', NULL, NULL, NULL, NULL, 2, '2025-11-20 05:49:19', '2025-11-20 05:49:19', 'active'),
(8, 40, 'pdf', 'uploads/691eb8a911e86_1763621033.pdf', '01.Brefing for Quran .pdf', 10036689, NULL, NULL, 1, '2025-11-20 06:43:53', '2025-11-20 06:43:53', 'active'),
(9, 40, 'video', 'uploads/691eb8a917ae2_1763621033.mp4', 'Introduction TQP.mp4', 16235147, NULL, 12, 2, '2025-11-20 06:43:53', '2025-11-20 06:43:53', 'active'),
(10, 41, 'pptx', 'lectures/hafiz-ul-quran/lecture-1/', NULL, NULL, NULL, NULL, 1, '2026-01-13 17:11:56', '2026-01-13 17:11:56', 'active'),
(11, 42, 'pptx', 'lectures/hafiz-ul-quran/lecture-2/', NULL, NULL, NULL, NULL, 1, '2026-01-13 17:14:26', '2026-01-13 17:14:26', 'active'),
(12, 43, 'pptx', 'lectures/hafiz-ul-quran/lecture-3/', NULL, NULL, NULL, NULL, 1, '2026-01-13 17:17:10', '2026-01-13 17:17:10', 'active'),
(13, 44, 'pptx', 'lectures/hafiz-ul-quran/lecture-4/', NULL, NULL, NULL, NULL, 1, '2026-01-13 17:18:49', '2026-01-13 17:18:49', 'active'),
(14, 45, 'pptx', 'lectures/hafiz-ul-quran/lecture-5/', NULL, NULL, NULL, NULL, 1, '2026-01-13 17:20:25', '2026-01-13 17:20:25', 'active'),
(15, 46, 'pptx', 'lectures/class-6/lecture-1/', NULL, NULL, NULL, NULL, 1, '2026-01-14 14:01:09', '2026-01-14 14:01:09', 'active'),
(16, 47, 'pptx', 'lectures/class-6/lecture-2/', NULL, NULL, NULL, NULL, 1, '2026-01-14 14:07:11', '2026-01-14 14:07:11', 'active'),
(17, 48, 'pptx', 'lectures/class-6/lecture-3', NULL, NULL, NULL, NULL, 1, '2026-01-14 14:08:22', '2026-01-14 14:08:22', 'active'),
(18, 49, 'pptx', 'lectures/class-6/lecture-4', NULL, NULL, NULL, NULL, 1, '2026-01-14 14:08:54', '2026-01-14 14:08:54', 'active'),
(19, 50, 'pptx', 'lectures/class-6/lecture-5', NULL, NULL, NULL, NULL, 1, '2026-01-14 14:09:44', '2026-01-14 14:09:44', 'active'),
(20, 46, 'pdf', 'uploads/6967b30539140_1768403717.pdf', '01. Lecture 1 noune in 6th sylabus.pdf', 6905211, NULL, NULL, 1, '2026-01-14 15:15:17', '2026-01-14 15:15:17', 'active'),
(21, 48, 'pptx', 'lectures/class-6/lecture-3/', NULL, NULL, NULL, NULL, 1, '2026-01-14 15:16:52', '2026-01-14 15:16:52', 'active'),
(22, 47, 'pdf', 'uploads/6967b46f2fe09_1768404079.pdf', '02. Lecture 2 letter  in 6th sylabus.pdf', 5990880, NULL, NULL, 1, '2026-01-14 15:21:19', '2026-01-14 15:21:19', 'active'),
(23, 48, 'pdf', 'uploads/6967bb33cfcd6_1768405811.pdf', '03. Lecture 3  Surah Fatiha.pdf', 8884708, NULL, NULL, 1, '2026-01-14 15:50:11', '2026-01-14 15:50:11', 'active'),
(24, 49, 'pdf', 'uploads/6967c63a51a60_1768408634.pdf', '04. Lecture 4  Surah Al-Fil.pdf', 9907050, NULL, NULL, 1, '2026-01-14 16:37:14', '2026-01-14 16:37:14', 'active'),
(25, 50, 'pdf', 'uploads/6967c76a9d55e_1768408938.pdf', '05. Lecture 5  Surah Al-Quraish.pdf', 6853376, NULL, NULL, 1, '2026-01-14 16:42:18', '2026-01-14 16:42:18', 'active'),
(26, 48, 'pdf', 'uploads/6968611238952_1768448274.pdf', '03. Lecture 3  Surah Fatiha.pdf', 8884708, NULL, NULL, 1, '2026-01-15 03:37:54', '2026-01-15 03:37:54', 'active'),
(27, 41, 'pdf', 'uploads/69686708e7f3a_1768449800.pdf', '01.Brefing for translation Tarjuma a Quran .pdf', 16916450, NULL, NULL, 1, '2026-01-15 04:03:20', '2026-01-15 04:03:20', 'active'),
(28, 42, 'pdf', 'uploads/69686806cadef_1768450054.pdf', '02. Lecture 2 Nouns para 1.pdf', 8087893, NULL, NULL, 1, '2026-01-15 04:07:34', '2026-01-15 04:07:34', 'active'),
(29, 43, 'pdf', 'uploads/6968693443802_1768450356.pdf', '03.  final Lecture  Ø­Ø±ÙˆÙ .pdf', 5588341, NULL, NULL, 1, '2026-01-15 04:12:36', '2026-01-15 04:12:36', 'active'),
(30, 44, 'pdf', 'uploads/696869955658a_1768450453.pdf', '04. Sura Fataha .pdf', 7135425, NULL, NULL, 1, '2026-01-15 04:14:13', '2026-01-15 04:14:13', 'active'),
(31, 45, 'pdf', 'uploads/69686a191c16b_1768450585.pdf', '05. Sura Baqara  1-5.pdf', 5207836, NULL, NULL, 1, '2026-01-15 04:16:25', '2026-01-15 04:16:25', 'active'),
(32, 51, 'pptx', 'lectures/class-6/lecture-6/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:01:13', '2026-01-30 19:01:13', 'active'),
(33, 52, 'pptx', 'lectures/class-6/lecture-7/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:02:48', '2026-01-30 19:02:48', 'active'),
(34, 53, 'pptx', 'lectures/class-6/lecture-8/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:03:25', '2026-01-30 19:03:25', 'active'),
(35, 54, 'pptx', 'lectures/class-6/lecture-9/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:04:49', '2026-01-30 19:04:49', 'active'),
(36, 55, 'pptx', 'lectures/class-6/lecture-10/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:05:24', '2026-01-30 19:05:24', 'active'),
(37, 56, 'pptx', 'lectures/class-6/Grammar/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:39:05', '2026-01-30 19:39:05', 'active'),
(38, 57, 'pptx', 'lectures/class-6/test/index.html', NULL, NULL, NULL, NULL, 1, '2026-01-30 19:40:07', '2026-01-30 19:40:07', 'active'),
(39, 58, 'pptx', 'lectures/hafiz-ul-quran/lecture-6/index.html', NULL, NULL, NULL, NULL, 1, '2026-02-02 15:26:05', '2026-02-02 15:26:05', 'active'),
(40, 59, 'pptx', 'lectures/hafiz-ul-quran/lecture-7/index.html', NULL, NULL, NULL, NULL, 1, '2026-02-02 16:07:39', '2026-02-02 16:07:39', 'active'),
(41, 60, 'pptx', 'lectures/hafiz-ul-quran/lecture-8/index.html', NULL, NULL, NULL, NULL, 1, '2026-02-02 16:10:46', '2026-02-02 16:10:46', 'active'),
(42, 61, 'pptx', 'lectures/hafiz-ul-quran/lecture-9/index.html', NULL, NULL, NULL, NULL, 1, '2026-02-02 16:27:57', '2026-02-02 16:27:57', 'active'),
(43, 62, 'pptx', 'lectures/hafiz-ul-quran/lecture-10/index.html', NULL, NULL, NULL, NULL, 1, '2026-02-02 16:41:09', '2026-02-02 16:41:09', 'active'),
(44, 63, 'pptx', 'lectures/hafiz-ul-quran/lecture-11/index.html', NULL, NULL, NULL, NULL, 1, '2026-02-02 16:42:27', '2026-02-02 16:42:27', 'active'),
(45, 49, 'video', 'https://www.facebook.com/reel/1419950129756479', NULL, NULL, NULL, 0, 1, '2026-02-13 20:31:08', '2026-02-13 20:31:08', 'active'),
(46, 49, 'video', 'https://www.facebook.com/reel/1419950129756479', NULL, NULL, NULL, 0, 1, '2026-02-13 20:55:29', '2026-02-13 20:55:29', 'active'),
(47, 64, 'pdf', 'uploads/69affa62c164f_1773140578.pdf', '01. Lecture 1  (1).pdf', 15204144, NULL, NULL, 1, '2026-03-10 11:02:58', '2026-03-10 11:02:58', 'active'),
(48, 65, 'pdf', 'uploads/69affaeb14ad7_1773140715.pdf', '02. Lecture 2 sura Fataha .pdf', 7261307, NULL, NULL, 1, '2026-03-10 11:05:15', '2026-03-10 11:05:15', 'active'),
(49, 66, 'pdf', 'uploads/69affb35b3b3a_1773140789.pdf', '03. Lecture 3 sura al-baqara 1 to 5 .pdf', 11266119, NULL, NULL, 1, '2026-03-10 11:06:29', '2026-03-10 11:06:29', 'active'),
(50, 67, 'pdf', 'uploads/69affb747a237_1773140852.pdf', '04. Lecture sura al-baqara 6 to 10.pdf', 10961917, NULL, NULL, 1, '2026-03-10 11:07:32', '2026-03-10 11:07:32', 'active'),
(51, 68, 'pdf', 'uploads/69affb78055b5_1773140856.pdf', '04. Lecture sura al-baqara 6 to 10.pdf', 10961917, NULL, NULL, 1, '2026-03-10 11:07:36', '2026-03-10 11:07:36', 'active'),
(52, 65, 'pdf', 'uploads/69affc2b6372e_1773141035.pdf', '02. Lecture 2 sura Fataha .pdf', 7261307, NULL, NULL, 1, '2026-03-10 11:10:35', '2026-03-10 11:10:35', 'active'),
(53, 66, 'pdf', 'uploads/69affca3a010b_1773141155.pdf', '03. Lecture 3 sura al-baqara 1 to 5 .pdf', 11266119, NULL, NULL, 1, '2026-03-10 11:12:35', '2026-03-10 11:12:35', 'active'),
(54, 69, 'pdf', 'uploads/69affd1e558fe_1773141278.pdf', '04. Lecture sura al-baqara 6 to 10.pdf', 10961917, NULL, NULL, 1, '2026-03-10 11:14:38', '2026-03-10 11:14:38', 'active'),
(55, 70, 'pdf', 'uploads/69affd8887bfe_1773141384.pdf', '05. Lecture sura al-baqara 11 to 15.pdf', 11355094, NULL, NULL, 1, '2026-03-10 11:16:24', '2026-03-10 11:16:24', 'active'),
(56, 71, 'pdf', 'uploads/69affe1e32762_1773141534.pdf', '06. Lecture sura al-baqara 16 to 20.pdf', 7912195, NULL, NULL, 1, '2026-03-10 11:18:54', '2026-03-10 11:18:54', 'active'),
(57, 72, 'pdf', 'uploads/69affeb3cedc7_1773141683.pdf', '07. Lecture sura al-baqara 21 to 25.pdf', 8906443, NULL, NULL, 1, '2026-03-10 11:21:23', '2026-03-10 11:21:23', 'active'),
(58, 73, 'pdf', 'uploads/69afff2edaf5e_1773141806.pdf', '08. Lecture sura al-baqara 26 to 30.pdf', 9684494, NULL, NULL, 1, '2026-03-10 11:23:26', '2026-03-10 11:23:26', 'active'),
(59, 74, 'pdf', 'uploads/69afff6ed0cfb_1773141870.pdf', '09.  Lecture sura al-baqara 31 to 35.pdf', 7614152, NULL, NULL, 1, '2026-03-10 11:24:30', '2026-03-10 11:24:30', 'active'),
(60, 75, 'pdf', 'uploads/69b0084646708_1773144134.pdf', '10.  Lecture sura al-baqara 36 to 40.pdf', 7405794, NULL, NULL, 1, '2026-03-10 12:02:14', '2026-03-10 12:02:14', 'active'),
(61, 76, 'pdf', 'uploads/69b00893ae629_1773144211.pdf', '11.  Lecture sura al-baqara 41 to 45.pdf', 9030470, NULL, NULL, 1, '2026-03-10 12:03:31', '2026-03-10 12:03:31', 'active'),
(62, 77, 'pdf', 'uploads/69b008e5b954c_1773144293.pdf', '12.  Lecture sura al-baqara 46 to 50.pdf', 9781487, NULL, NULL, 1, '2026-03-10 12:04:53', '2026-03-10 12:04:53', 'active'),
(63, 78, 'pdf', 'uploads/69b009176665d_1773144343.pdf', '13.  Lecture sura al-baqara 51 to 55.pdf', 9710565, NULL, NULL, 1, '2026-03-10 12:05:43', '2026-03-10 12:05:43', 'active'),
(64, 79, 'pdf', 'uploads/69b00954a84d5_1773144404.pdf', '14.  Lecture sura al-baqara 56 to 60.pdf', 9134308, NULL, NULL, 1, '2026-03-10 12:06:44', '2026-03-10 12:06:44', 'active'),
(65, 80, 'pdf', 'uploads/69b00b648aa2b_1773144932.pdf', '15.  Lecture sura al-baqara 61 to 65.pdf', 12420719, NULL, NULL, 1, '2026-03-10 12:15:32', '2026-03-10 12:15:32', 'active'),
(66, 81, 'pdf', 'uploads/69b00c00ccfe8_1773145088.pdf', '16.  Lecture sura al-baqara 66 to 70.pdf', 8485473, NULL, NULL, 1, '2026-03-10 12:18:08', '2026-03-10 12:18:08', 'active'),
(67, 82, 'pdf', 'uploads/69b00c086ff27_1773145096.pdf', '16.  Lecture sura al-baqara 66 to 70.pdf', 8485473, NULL, NULL, 1, '2026-03-10 12:18:16', '2026-03-10 12:18:16', 'active'),
(68, 83, 'pdf', 'uploads/69b00c7352e5f_1773145203.pdf', '17.  Lecture sura al-baqara 71to 75.pdf', 7928926, NULL, NULL, 1, '2026-03-10 12:20:03', '2026-03-10 12:20:03', 'active'),
(69, 84, 'pdf', 'uploads/69b00cc4c58a1_1773145284.pdf', '18.  Lecture sura al-baqara 76 to 80  .pdf', 9567277, NULL, NULL, 1, '2026-03-10 12:21:24', '2026-03-10 12:21:24', 'active'),
(70, 85, 'pdf', 'uploads/69b00d1b225f5_1773145371.pdf', '19.  Lecture sura al-baqara 81 to 85.pdf', 8867255, NULL, NULL, 1, '2026-03-10 12:22:51', '2026-03-10 12:22:51', 'active'),
(71, 86, 'pdf', 'uploads/69b00da3979cc_1773145507.pdf', '20.  Lecture sura al-baqara 86 to 90.pdf', 8395133, NULL, NULL, 1, '2026-03-10 12:25:07', '2026-03-10 12:25:07', 'active'),
(72, 87, 'pdf', 'uploads/69b00e15dfe17_1773145621.pdf', '20.  Lecture sura al-baqara 86 to 90.pdf', 8395133, NULL, NULL, 1, '2026-03-10 12:27:01', '2026-03-10 12:27:01', 'active'),
(73, 88, 'pdf', 'uploads/69b0108d2a23c_1773146253.pdf', '21.  Lecture sura al-baqara 91 to 100.pdf', 6367331, NULL, NULL, 1, '2026-03-10 12:37:33', '2026-03-10 12:37:33', 'active'),
(74, 89, 'pdf', 'uploads/69b0143fa734b_1773147199.pdf', '22.  Lecture sura al-baqara 101 to 110.pdf', 8081570, NULL, NULL, 1, '2026-03-10 12:53:19', '2026-03-10 12:53:19', 'active'),
(75, 90, 'pdf', 'uploads/69b0154a6c175_1773147466.pdf', '23.  Lecture sura al-baqara 111 to 120.pdf', 8029511, NULL, NULL, 1, '2026-03-10 12:57:46', '2026-03-10 12:57:46', 'active'),
(76, 91, 'pdf', 'uploads/69b015c265bd8_1773147586.pdf', '24.  Lecture sura al-baqara 121 to 130.pdf', 7787429, NULL, NULL, 1, '2026-03-10 12:59:46', '2026-03-10 12:59:46', 'active'),
(77, 49, 'video', 'https://www.facebook.com/reel/1419950129756479', NULL, NULL, NULL, 0, 1, '2026-03-18 13:08:17', '2026-03-18 13:08:17', 'active'),
(78, 92, 'pdf', 'uploads/69bb6353c4a17_1773888339.pdf', '01.Brefing for translation Tarjuma a Quran .pdf', 16846187, NULL, NULL, 1, '2026-03-19 02:45:39', '2026-03-19 02:45:39', 'active'),
(79, 93, 'pdf', 'uploads/69bc3cce1e907_1773944014.pdf', '02. Lecture 2 Nouns para 1.pdf', 8121608, NULL, NULL, 1, '2026-03-19 18:13:34', '2026-03-19 18:13:34', 'active'),
(80, 94, 'pdf', 'uploads/69bc3cdab9103_1773944026.pdf', '02. Lecture 2 Nouns para 1.pdf', 8121608, NULL, NULL, 1, '2026-03-19 18:13:46', '2026-03-19 18:13:46', 'active'),
(81, 95, 'pdf', 'uploads/69bc3eac08847_1773944492.pdf', '03.  final Lecture 2 Ø­Ø±ÙˆÙ .pdf', 5618008, NULL, NULL, 1, '2026-03-19 18:21:32', '2026-03-19 18:21:32', 'active'),
(82, 96, 'pdf', 'uploads/69bc3f06baba2_1773944582.pdf', '04. Sura Fataha .pdf', 6679220, NULL, NULL, 1, '2026-03-19 18:23:02', '2026-03-19 18:23:02', 'active'),
(83, 97, 'pdf', 'uploads/69bc3fe7488d8_1773944807.pdf', '05. Sura Baqara  1-5.pdf', 6606738, NULL, NULL, 1, '2026-03-19 18:26:47', '2026-03-19 18:26:47', 'active'),
(84, 98, 'pdf', 'uploads/69bc4046cdc60_1773944902.pdf', '06. Sura Baqara  6-9.pdf', 8254289, NULL, NULL, 1, '2026-03-19 18:28:22', '2026-03-19 18:28:22', 'active'),
(85, 99, 'pdf', 'uploads/69bc409d251e8_1773944989.pdf', '07.  Sura Baqara 10-13.pdf', 6242227, NULL, NULL, 1, '2026-03-19 18:29:49', '2026-03-19 18:29:49', 'active'),
(86, 100, 'pdf', 'uploads/69bc40ddb036c_1773945053.pdf', '08.  Sura Baqara 14-18.pdf', 6899492, NULL, NULL, 1, '2026-03-19 18:30:53', '2026-03-19 18:30:53', 'active'),
(87, 101, 'pdf', 'uploads/69bc411463edf_1773945108.pdf', '09.  Sura Baqara 19-21 .pdf', 7149986, NULL, NULL, 1, '2026-03-19 18:31:48', '2026-03-19 18:31:48', 'active'),
(88, 102, 'pdf', 'uploads/69bc42445425b_1773945412.pdf', '08.  Sura Baqara 14-18.pdf', 6899492, NULL, NULL, 1, '2026-03-19 18:36:52', '2026-03-19 18:36:52', 'active'),
(89, 103, 'pdf', 'uploads/69bc42be765a5_1773945534.pdf', '10.  Sura Baqara 22-24 .pdf', 6696390, NULL, NULL, 1, '2026-03-19 18:38:54', '2026-03-19 18:38:54', 'active'),
(90, 104, 'pdf', 'uploads/69bc430bab433_1773945611.pdf', '11.  Sura Baqara 25-26 .pdf', 9161553, NULL, NULL, 1, '2026-03-19 18:40:11', '2026-03-19 18:40:11', 'active'),
(91, 105, 'pdf', 'uploads/69bc43468e5e9_1773945670.pdf', '12.  Sura Baqara 27-30.pdf', 5606792, NULL, NULL, 1, '2026-03-19 18:41:10', '2026-03-19 18:41:10', 'active'),
(92, 106, 'pdf', 'uploads/69bc43885ee26_1773945736.pdf', '13. Sura Baqara 31 to 33.pdf', 5680596, NULL, NULL, 1, '2026-03-19 18:42:16', '2026-03-19 18:42:16', 'active'),
(93, 107, 'pdf', 'uploads/69bc440b31bc0_1773945867.pdf', '14.  Sura AlBaqara 34 to 36.pdf', 5469348, NULL, NULL, 1, '2026-03-19 18:44:27', '2026-03-19 18:44:27', 'active'),
(94, 108, 'pdf', 'uploads/69bc448f2837a_1773945999.pdf', '15. Sura AlBaqara 37 to 40 .pdf', 4167038, NULL, NULL, 1, '2026-03-19 18:46:39', '2026-03-19 18:46:39', 'active'),
(95, 109, 'pdf', 'uploads/69bc44ff11f9d_1773946111.pdf', '16.  Sura AlBaqara 41 to 44.pdf', 5254039, NULL, NULL, 1, '2026-03-19 18:48:31', '2026-03-19 18:48:31', 'active'),
(96, 110, 'pdf', 'uploads/69bc461913a4e_1773946393.pdf', '17.  Sura AlBaqara 45 to 48.pdf', 4609242, NULL, NULL, 1, '2026-03-19 18:53:13', '2026-03-19 18:53:13', 'active'),
(97, 111, 'pdf', 'uploads/69bc4671131c6_1773946481.pdf', '18. Sura AlBaqara 49 to 51 .pdf', 5277153, NULL, NULL, 1, '2026-03-19 18:54:41', '2026-03-19 18:54:41', 'active'),
(98, 112, 'pdf', 'uploads/69bc46d215549_1773946578.pdf', '19.  ØªØ±Ø¬Ù…Û Ú©Ø±Ù†Û’ Ú©Û’ Ù‚ÙˆØ§Ø¹Ø¯ .pdf', 4131348, NULL, NULL, 1, '2026-03-19 18:56:18', '2026-03-19 18:56:18', 'active'),
(99, 113, 'pdf', 'uploads/69bc472eb44f3_1773946670.pdf', '20.  Ø¶Ù…Ø§Ø¦Ø± Ú©Ø§ Ø¨ÛŒØ§Ù†.pdf', 5597218, NULL, NULL, 1, '2026-03-19 18:57:50', '2026-03-19 18:57:50', 'active'),
(100, 114, 'pdf', 'uploads/69bc47cfcba8f_1773946831.pdf', '21.   Sura AlBaqara 52 to 54 .pdf', 4000639, NULL, NULL, 1, '2026-03-19 19:00:31', '2026-03-19 19:00:31', 'active'),
(101, 115, 'pdf', 'uploads/69bc50b51ff25_1773949109.pdf', '22.  Sura AlBaqara 55 to 57.pdf', 4295653, NULL, NULL, 1, '2026-03-19 19:38:29', '2026-03-19 19:38:29', 'active'),
(102, 116, 'pdf', 'uploads/69bc5103d4001_1773949187.pdf', '23.  Sura AlBaqara 58 to 59 .pdf', 3667833, NULL, NULL, 1, '2026-03-19 19:39:47', '2026-03-19 19:39:47', 'active'),
(103, 117, 'pdf', 'uploads/69bc5145625e5_1773949253.pdf', '24.  Sura AlBaqara 60 .pdf', 3341312, NULL, NULL, 1, '2026-03-19 19:40:53', '2026-03-19 19:40:53', 'active'),
(104, 118, 'pdf', 'uploads/69bc51efaaa13_1773949423.pdf', '25.  Sura AlBaqara 61 .pdf', 6098887, NULL, NULL, 1, '2026-03-19 19:43:43', '2026-03-19 19:43:43', 'active'),
(105, 119, 'pdf', 'uploads/69bc52506f1a7_1773949520.pdf', '26.  Sura AlBaqara 62 to 63.pdf', 3605682, NULL, NULL, 1, '2026-03-19 19:45:20', '2026-03-19 19:45:20', 'active'),
(106, 120, 'pdf', 'uploads/69bc52b4b0af8_1773949620.pdf', '27.   Sura AlBaqara 64 to 66.pdf', 3482257, NULL, NULL, 1, '2026-03-19 19:47:00', '2026-03-19 19:47:00', 'active'),
(107, 121, 'pdf', 'uploads/69bc530f07be6_1773949711.pdf', '28.  Sura AlBaqara 67 to 68.pdf', 2933377, NULL, NULL, 1, '2026-03-19 19:48:31', '2026-03-19 19:48:31', 'active'),
(108, 122, 'pdf', 'uploads/69bc53b78da7f_1773949879.pdf', '29.  Sura AlBaqara 69 to 70.pdf', 4175532, NULL, NULL, 1, '2026-03-19 19:51:19', '2026-03-19 19:51:19', 'active'),
(109, 123, 'pdf', 'uploads/69bc54dcba54b_1773950172.pdf', '30. Sura AlBaqara 71to 73.pdf', 4603736, NULL, NULL, 1, '2026-03-19 19:56:12', '2026-03-19 19:56:12', 'active'),
(110, 124, 'pdf', 'uploads/69bc554f2573f_1773950287.pdf', '31.   ÙØ¹Ù„ Ù…Ø§Ø¶ÛŒ Ú©Ø§ Ø¨ÛŒØ§Ù† .pdf', 1522130, NULL, NULL, 1, '2026-03-19 19:58:07', '2026-03-19 19:58:07', 'active'),
(111, 125, 'pdf', 'uploads/69bc55b099435_1773950384.pdf', '32.  Sura AlBaqara 74 to 75.pdf', 4981127, NULL, NULL, 1, '2026-03-19 19:59:44', '2026-03-19 19:59:44', 'active'),
(112, 126, 'pdf', 'uploads/69bc56307f01d_1773950512.pdf', '33. Sura AlBaqara 76 to 78.pdf', 4090698, NULL, NULL, 1, '2026-03-19 20:01:52', '2026-03-19 20:01:52', 'active'),
(113, 127, 'pdf', 'uploads/69bc56943f7e2_1773950612.pdf', '34. Sura AlBaqara 79 to 81.pdf', 6581812, NULL, NULL, 1, '2026-03-19 20:03:32', '2026-03-19 20:03:32', 'active'),
(114, 128, 'pdf', 'uploads/69bc57343bdfd_1773950772.pdf', '35 . Sura AlBaqara 82 to 84 .pdf', 6736780, NULL, NULL, 1, '2026-03-19 20:06:12', '2026-03-19 20:06:12', 'active'),
(115, 128, 'pdf', 'uploads/69cb8e2633449_1774947878.pdf', '35 . Sura AlBaqara 82 to 84 .pdf', 6736780, NULL, NULL, 1, '2026-03-31 09:04:38', '2026-03-31 09:04:38', 'active'),
(116, 130, 'pdf', 'uploads/69cb8f6e7a7d0_1774948206.pdf', '37. Sura AlBaqara 87 to 89.pdf', 5826841, NULL, NULL, 1, '2026-03-31 09:10:06', '2026-03-31 09:10:06', 'active'),
(117, 131, 'pdf', 'uploads/69cb90061d6a9_1774948358.pdf', '38. Sura AlBaqara 90 to 91.pdf', 3587500, NULL, NULL, 1, '2026-03-31 09:12:38', '2026-03-31 09:12:38', 'active'),
(118, 132, 'pdf', 'uploads/69cb90a17b9d6_1774948513.pdf', '39. Sura AlBaqara 92 to94 .pdf', 5162268, NULL, NULL, 1, '2026-03-31 09:15:13', '2026-03-31 09:15:13', 'active'),
(119, 133, 'pdf', 'uploads/69cb9105b3e99_1774948613.pdf', '40. Sura AlBaqara 95 to 98 .pdf', 5331566, NULL, NULL, 1, '2026-03-31 09:16:53', '2026-03-31 09:16:53', 'active'),
(120, 134, 'pdf', 'uploads/69cb922865952_1774948904.pdf', '41. Sura AlBaqara 99 to 101.pdf', 5217100, NULL, NULL, 1, '2026-03-31 09:21:44', '2026-03-31 09:21:44', 'active'),
(121, 135, 'pdf', 'uploads/69cb92a9bcd6a_1774949033.pdf', '42. Sura AlBaqara 102.pdf', 5146402, NULL, NULL, 1, '2026-03-31 09:23:53', '2026-03-31 09:23:53', 'active'),
(122, 136, 'pdf', 'uploads/69cb937f26b91_1774949247.pdf', '43.  Sura AlBaqara 103 to 106.pdf', 6405873, NULL, NULL, 1, '2026-03-31 09:27:27', '2026-03-31 09:27:27', 'active'),
(123, 137, 'pdf', 'uploads/69cb947714ee0_1774949495.pdf', '43.  Sura AlBaqara 103 to 106.pdf', 6405873, NULL, NULL, 1, '2026-03-31 09:31:35', '2026-03-31 09:31:35', 'active'),
(124, 138, 'pdf', 'uploads/69cb94b2a3db9_1774949554.pdf', '44. Sura AlBaqara 107 to 109.pdf', 3643096, NULL, NULL, 1, '2026-03-31 09:32:34', '2026-03-31 09:32:34', 'active'),
(125, 139, 'pdf', 'uploads/69cb94ea16d4d_1774949610.pdf', '45. Sura AlBaqara 110 to 112.pdf', 4164960, NULL, NULL, 1, '2026-03-31 09:33:30', '2026-03-31 09:33:30', 'active'),
(126, 140, 'pdf', 'uploads/69cb95248a134_1774949668.pdf', '46. Sura AlBaqara 113 to 115.pdf', 6337017, NULL, NULL, 1, '2026-03-31 09:34:28', '2026-03-31 09:34:28', 'active'),
(127, 141, 'pdf', 'uploads/69cb956e17428_1774949742.pdf', '47.Sura AlBaqara 116 to 119 .pdf', 5954059, NULL, NULL, 1, '2026-03-31 09:35:42', '2026-03-31 09:35:42', 'active'),
(128, 142, 'pdf', 'uploads/69cb95a807c9d_1774949800.pdf', '48. Sura AlBaqara 120 to 121.pdf', 4878618, NULL, NULL, 1, '2026-03-31 09:36:40', '2026-03-31 09:36:40', 'active'),
(129, 143, 'pdf', 'uploads/69cb95e7ef670_1774949863.pdf', '49. Sura AlBaqara 122 to 124.pdf', 4508461, NULL, NULL, 1, '2026-03-31 09:37:43', '2026-03-31 09:37:43', 'active'),
(130, 144, 'pdf', 'uploads/69cb96ba7ccba_1774950074.pdf', '50. Sura AlBaqara 125 to 126.pdf', 5501657, NULL, NULL, 1, '2026-03-31 09:41:14', '2026-03-31 09:41:14', 'active'),
(131, 145, 'pdf', 'uploads/69cb970f13900_1774950159.pdf', '51. Sura AlBaqara 127 to 129.pdf', 4173225, NULL, NULL, 1, '2026-03-31 09:42:39', '2026-03-31 09:42:39', 'active'),
(132, 146, 'pdf', 'uploads/69cb97938bb5a_1774950291.pdf', '52. Sura AlBaqara 130 to 133.pdf', 3967732, NULL, NULL, 1, '2026-03-31 09:44:51', '2026-03-31 09:44:51', 'active'),
(133, 147, 'pdf', 'uploads/69cb97d23c391_1774950354.pdf', '53. Sura AlBaqara 134 to 136.pdf', 4918484, NULL, NULL, 1, '2026-03-31 09:45:54', '2026-03-31 09:45:54', 'active'),
(134, 147, 'pdf', 'uploads/69cb9dd1bf559_1774951889.pdf', '53. Sura AlBaqara 134 to 136.pdf', 4918484, NULL, NULL, 1, '2026-03-31 10:11:29', '2026-03-31 10:11:29', 'active'),
(135, 92, 'pdf', 'uploads/69cba843d1c1f_1774954563.pdf', '01.Brefing for translation Tarjuma a Quran .pdf', 16846187, NULL, NULL, 1, '2026-03-31 10:56:03', '2026-03-31 10:56:03', 'active'),
(136, 93, 'pdf', 'uploads/69cba8e442feb_1774954724.pdf', '02. Lecture 2 Nouns para 1.pdf', 8121608, NULL, NULL, 1, '2026-03-31 10:58:44', '2026-03-31 10:58:44', 'active'),
(137, 149, 'pdf', 'uploads/69cba94f126ff_1774954831.pdf', '01.Brefing for translation Tarjuma a Quran .pdf', 16846187, NULL, NULL, 1, '2026-03-31 11:00:31', '2026-03-31 11:00:31', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `description`, `address`, `phone`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Tech University System', 'Leading technology education organization', '123 Tech Street, Silicon Valley', '+1-555-0100', 'info@techuniversity.edu', 'active', '2025-09-15 20:11:16', '2025-09-15 20:11:16'),
(2, 'Community College Network', 'Affordable education for all communities', '456 Community Ave, Downtown', '+1-555-0200', 'contact@ccnetwork.edu', 'active', '2025-09-15 20:11:16', '2025-09-15 20:11:16'),
(4, 'Nazim-u-Madaris Pakistan', 'Degree of International Martyrdom in Arabic and Islamic Sciences degree issued under â€œNizam Al Madaris Pakistanâ€. O Arabic and Islamic will be equal. Degrees / certificates issued by Nizam-ul-Madaris Pakistan will be acceptable for educational, teaching and administrative positions / services in all levels of civil and military, government and semi-government and non-government institutions.', '365-M Block, Model Town, Lahore, Pakistan', '042111140140', 'info@nizam-ul-madaris.edu.pk', 'active', '2025-11-17 14:44:26', '2025-11-17 14:44:26');

-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE `pending_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 5,
  `organization_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `can_access_all_classes` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `selected_classes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_classes`)),
  `created_by` int(11) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `submission_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_users`
--

INSERT INTO `pending_users` (`id`, `username`, `email`, `full_name`, `address`, `phone`, `gender`, `password`, `role_id`, `organization_id`, `school_id`, `can_access_all_classes`, `status`, `selected_classes`, `created_by`, `admin_notes`, `admin_id`, `submission_timestamp`, `created_at`, `updated_at`) VALUES
(1, 'cyberpunk', 'cyberpunk@example.com', 'cyberpunk', 'House#651, Sector 2, D-1 Township Lahore, 365 M Model Town Lahore', '03314057324', 'Male', '$2y$10$hl7gLMNUkFZ4csEUB461fOGwO2VRFmMNwp7WmPQ1KEQJcctf7qqlu', 5, NULL, NULL, 0, 'approved', '[\"8\",\"6\",\"1\"]', NULL, '', 1, '2025-09-22 13:04:02', '2025-09-22 13:04:02', '2025-09-22 18:06:12'),
(2, 'test_user', 'ghulammujtaba969@gmail.com', 'Test User', '780 Elmwood Drive Brooklyn, 780 Elmwood Drive Brooklyn', '+923314057324', 'Male', '$2y$10$ZR5fbkgMXwXRoFRrOthAbeSuAxggvFEu2AvFIdWkBU1Un4gNvCvGe', 5, NULL, NULL, 0, 'rejected', '[]', NULL, '', 1, '2025-09-22 20:30:04', '2025-09-22 20:30:04', '2025-09-22 20:30:52'),
(4, 'test_user2', 'test_user@example.com', 'Test User', '780 Elmwood Drive Brooklyn, 780 Elmwood Drive Brooklyn', '03184621902', 'Male', '$2y$10$SJ39bw0cuHSagvOrB5VKAeFohoAO1LDWDn/9JVPZr4ax6QSn6Ge16', 5, NULL, NULL, 0, 'approved', '[]', NULL, '', 1, '2025-09-22 20:31:59', '2025-09-22 20:31:59', '2025-09-22 20:32:14'),
(5, 'G M Alvi', 'gm.alvi@minhaj.edu.pk', 'Ghulam murtaza', '651. 2D1 township Lahore', '0307302281', 'Male', '$2y$10$T7lkt8vn0er71/YTP.dWaulmM7UH4yrQYmxnPkeZ5dj6yEzqGcfHS', 5, NULL, NULL, 0, 'approved', '[]', NULL, '', 1, '2025-09-24 16:51:58', '2025-09-24 16:51:58', '2025-11-07 15:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Full system access and control', '2025-09-15 20:09:39'),
(2, 'Organization Admin', 'Manages schools and users within their organization', '2025-09-15 20:09:39'),
(3, 'School Admin', 'Manages teachers and classes within their school', '2025-09-15 20:09:39'),
(4, 'Teacher', 'Manages assigned classes and lectures', '2025-09-15 20:09:39'),
(5, 'Solo Student', 'Individual student with direct Super Admin assignment', '2025-09-15 20:09:39');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `organization_id`, `name`, `description`, `address`, `phone`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tech University - Main Campus', 'Main campus with engineering and computer science programs', '123 Tech Street, Silicon Valley', '+1-555-0101', 'main@techuniversity.edu', 'active', '2025-09-15 20:11:16', '2025-09-15 20:11:16'),
(2, 1, 'Tech University - North Campus', 'North campus focusing on business and liberal arts', '789 North Ave, Uptown', '+1-555-0102', 'north@techuniversity.edu', 'active', '2025-09-15 20:11:16', '2025-09-15 20:11:16'),
(3, 2, 'Downtown Community College', 'Urban campus serving diverse student population', '456 Community Ave, Downtown', '+1-555-0201', 'downtown@ccnetwork.edu', 'active', '2025-09-15 20:11:16', '2025-09-15 20:11:16'),
(4, 4, 'Test School', '', '365-M Block, Model Town, Lahore, Pakistan', '+92 42 111 140 140', 'testschool@theteacher.pk', 'active', '2025-11-17 14:46:08', '2025-11-17 14:46:08'),
(5, 4, 'Jamia Charagia Nazima', '', '', '', '', 'active', '2026-02-08 16:41:42', '2026-02-08 16:41:42'),
(6, 4, 'Al Qadr Islamic Center', '', 'Sialkot Pakistan', '', '', 'active', '2026-02-08 17:05:39', '2026-02-08 17:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `class_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `subject_code`, `class_id`, `description`, `image`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Trajuma tul Quran', 'TQ-6th PTB', 1, '', NULL, '2025-09-15 19:40:01', '2025-09-16 01:14:21', 'active'),
(2, 'Trajuma tul Quran', 'TQ 7th PTB', 3, 'A multi-media teaching method to develop the ability to translate the Quran among students of the 7th class', NULL, '2025-09-16 01:10:53', '2025-09-16 01:12:24', 'active'),
(3, 'Fahem ul Quran', 'FQC for public', 7, 'a Malti media teaching mathed', NULL, '2025-09-24 20:14:39', '2025-09-24 20:14:39', 'active'),
(4, 'Translation Quran', 'FQCP-01', 13, 'A unique multi-media Quran Translation Course to develop the ability to translate the Quran among the general public', NULL, '2025-10-03 16:27:02', '2025-10-03 16:27:02', 'active'),
(5, 'Quran, Hadith, Seerah, Fiqh, etiquette, and morals', 'KCS-01-SK', 16, 'Consisting of the Quran, Hadith, Seerah, Fiqh, etiquette, and morals', NULL, '2025-10-15 16:51:04', '2025-10-15 16:51:04', 'active'),
(6, 'Translation Quran (Para 1)', 'TR-Tahfeez', 17, 'Multimedia teaching method to develop the ability of students in the Hifz department to understand the literal and contextual meanings of the Holy Quran', 'uploads/subjects/subject_691c1f047ad49_1763450628.jpg', '2025-11-18 07:23:48', '2025-11-18 07:23:48', 'active'),
(7, 'Translation Quran', 'TR-6th', 18, '', NULL, '2026-01-14 13:45:51', '2026-01-15 17:32:23', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `syllabi`
--

CREATE TABLE `syllabi` (
  `id` int(11) NOT NULL,
  `syllabus_title` varchar(200) NOT NULL,
  `subject_id` int(11) DEFAULT NULL COMMENT 'Required for classes, NULL for courses',
  `class_id` int(11) DEFAULT NULL COMMENT 'Direct course link, NULL for regular classes',
  `description` text DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `syllabi`
--

INSERT INTO `syllabi` (`id`, `syllabus_title`, `subject_id`, `class_id`, `description`, `objectives`, `duration_weeks`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Surah Fatiah To Surah Al Bakarah (01-200)', 1, NULL, 'The Syllabus includes Surah Fatiah to Surah Al Bakarah (Ayat 01 - 200)', 'Learn Translation', 24, '2025-09-15 19:43:18', '2025-09-15 19:43:18', 'active'),
(2, 'Translation Quran', 3, NULL, 'a course consists of 25 lectures', 'to understand the meaning of Quran', 4, '2025-09-26 17:14:08', '2025-09-26 17:14:08', 'active'),
(3, 'Fahem ul Quran', NULL, 13, 'A unique multimedia Translation Quran method', '', 1, '2025-10-01 15:17:48', '2025-10-01 15:17:48', 'active'),
(4, 'Quran, Hadith, Seerah, Fiqh, etiquette, and morals', NULL, 16, '', '', 1, '2025-10-15 16:45:16', '2025-10-15 16:45:16', 'active'),
(5, 'Translation Quran', 6, NULL, 'Multimedia teaching method to develop the ability of students in the Hifz department to understand the literal and contextual meanings of the Holy Quran', '', 16, '2025-11-18 07:28:01', '2025-11-18 07:28:01', 'active'),
(6, 'Class-6th', 7, NULL, 'Syllabus includes:\r\n\r\nSurah Fatiah and Last 10 Surah Of Holy Quran', '', 20, '2026-01-14 13:59:17', '2026-01-17 10:16:26', 'active'),
(7, 'Aasan Tarjuma Quran Course Week 1', NULL, 19, 'Lecture 1 to Lecture 7', 'Lecture 1 to Lecture 7', 1, '2026-03-10 10:00:02', '2026-03-10 10:00:02', 'active'),
(8, 'Aasan Tarjuma Quran Course Week 2', NULL, 19, 'Lecture 8 to 14', 'Lecture 8 to 14', 1, '2026-03-10 10:04:01', '2026-03-10 10:04:01', 'active'),
(9, 'Aasan Tarjuma Quran Course Week 3', NULL, 19, 'Lacture 15 to Lacture  21', 'Lacture 15 to Lacture  21', 1, '2026-03-10 12:09:03', '2026-03-10 12:09:03', 'active'),
(10, 'Aasan Tarjuma Quran Course Week 4', NULL, 19, 'Lacture No 22 to Lacture 28', 'Lacture No 22 to Lacture 28', 1, '2026-03-10 12:44:22', '2026-03-10 12:44:22', 'active'),
(11, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 1)', NULL, 20, 'Lecture 1 to Lecture 7', '', 1, '2026-03-19 02:27:52', '2026-03-19 02:40:05', 'active'),
(12, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 2)', NULL, 20, 'lecture 8 to lacture 14', '', 1, '2026-03-19 18:34:04', '2026-03-19 19:04:56', 'active'),
(13, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 3)', NULL, 20, 'Lacture 15 to Lacture no 21', '', 1, '2026-03-19 18:45:32', '2026-03-19 18:45:32', 'active'),
(14, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 4)', NULL, 20, 'Lacture no 22 to Lacture no 28', '', 1, '2026-03-19 19:03:51', '2026-03-19 19:03:51', 'active'),
(15, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 5)', NULL, 20, 'Lacture no 29 to Lacture no 35', '', 1, '2026-03-19 19:49:40', '2026-03-19 19:49:40', 'active'),
(16, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 6)', NULL, 20, 'Lacture no 36 to lacture no 42', '', 1, '2026-03-31 09:06:19', '2026-03-31 09:06:19', 'active'),
(17, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 7)', NULL, 20, 'Lacture no 43 to 49', '', 1, '2026-03-31 09:26:05', '2026-03-31 09:26:05', 'active'),
(18, 'Tarjuma-e-Quran Course Baraye Shoba Hifz (week 8)', NULL, 20, 'Lavture no 50 to 56', '', 1, '2026-03-31 09:39:11', '2026-03-31 09:39:11', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `organization_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `can_access_all_classes` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `password`, `gender`, `address`, `timezone`, `country`, `phone`, `whatsapp_number`, `photo`, `created_at`, `updated_at`, `is_active`, `role_id`, `organization_id`, `school_id`, `can_access_all_classes`, `status`, `created_by`) VALUES
(1, 'superadmin', 'admin@syllabus.com', 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0', NULL, NULL, NULL, NULL, NULL, 'uploads/users/user_1.jpg', '2025-09-15 19:36:23', '2025-11-07 04:33:00', 1, 1, NULL, NULL, 1, 'active', NULL),
(2, 'organization_user', 'organizationuser@example.com', 'Oragnaization User', '$2y$10$NLh3oUqEwqi8pdOr4FWe2OmrcHKGZPrOklRS6eWinalcpUC7AGeby', '0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-15 21:02:10', '2025-09-16 23:50:37', 1, 2, 1, NULL, 0, 'active', 1),
(3, 'school_admin', 'school_admin@example.com', 'School Admin', '$2y$10$9Drx/qA8SeQ9ZTJopSvHW.OL.SJxAqeH8Q6.gkFKAQTM2laFTko1.', '0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-16 23:53:48', '2025-09-16 23:53:48', 1, 3, 1, 1, 0, 'active', 1),
(4, 'school_teacher', 'school_teacher@example.com', 'Teacher', '$2y$10$pCDsa30S7ivagbarzbKP3up/S4sihqwp5nbF8q.E2FxGxjr/BxN32', '0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-16 23:58:56', '2025-09-16 23:58:56', 1, 4, 1, NULL, 0, 'active', 3),
(5, 'ghulammujtaba969', 'ghulammujtaba969@gmail.com', 'Ghulam Mujtaba', '$2y$10$PhuzaUN334abz9//p4wj8OekPXRGKOUsVuKMKXb/vgkJbt3MYPdea', '0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 20:51:53', '2025-09-22 18:38:13', 1, 2, 2, 3, 0, 'active', 1),
(6, 'gm.alvi', 'gm.alvi@jimq.edu.pk', 'Ghulam Murtaza', '$2y$10$zWUXeSZwQ02JmKjTqV/.hegDTIsVWYHSygTIX5fNubdYZTSObxmuG', '0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 20:54:28', '2025-09-17 20:54:48', 1, 1, NULL, NULL, 1, 'active', 1),
(7, 'ahmadkhan0071', 'ahmadkhan0071@outlook.com', 'Mujtaba Alvi', '$2y$10$o2azn3c1jy/MdoKItN1bFuxU0IirurCAr/PXSzoqCn/7MvViBScUC', '0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-17 21:33:42', '2025-09-17 21:33:42', 1, 2, 1, NULL, 0, 'active', 6),
(8, 'cyberpunk', 'cyberpunk@example.com', 'cyberpunk', '$2y$10$hl7gLMNUkFZ4csEUB461fOGwO2VRFmMNwp7WmPQ1KEQJcctf7qqlu', 'Male', 'House#651, Sector 2, D-1 Township Lahore, 365 M Model Town Lahore', NULL, NULL, '03314057324', NULL, NULL, '2025-09-22 18:06:12', '2025-09-22 18:06:12', 1, 5, NULL, NULL, 0, 'active', 1),
(9, 'bibi_sadia', 'bibi_sadia@gmail.com', 'Sadia Bibi', '$2y$10$50UOZ8.WgbNxX6gzh8MILe/1TBRiI.YNjsmJMU6P3eNo5Ir5vJd.i', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-22 18:26:11', '2025-09-22 18:26:11', 1, 3, 2, 3, 0, 'active', 5),
(11, 'test_user2', 'test_user@example.com', 'Test User', '$2y$10$SJ39bw0cuHSagvOrB5VKAeFohoAO1LDWDn/9JVPZr4ax6QSn6Ge16', 'Male', '780 Elmwood Drive Brooklyn, 780 Elmwood Drive Brooklyn', NULL, NULL, '03184621902', NULL, NULL, '2025-09-22 20:32:14', '2025-09-22 20:32:14', 1, 5, NULL, NULL, 0, 'active', 1),
(12, 'Gmalvi92', 'gmalvi92@gmail.com', 'Ghulam murtaza', '$2y$10$5wWmYBnZg6TpHc/Kx0p2Jerg5mlzgzGlaCZO9iB2vLZZ9I1a9fsWW', 'Male', 'Anshan koria', NULL, 'North Korea', '0307302281', '0307302281', NULL, '2025-09-27 07:35:10', '2025-11-11 11:36:34', 1, 5, NULL, NULL, 0, 'active', NULL),
(13, 'Aqeelmujahid', 'aqeelmujahid@gmail.com', 'Aqeel Mujahid', '$2y$10$gSVtdY4Ko0opyRCl6PskOO/0BWkS/c2jL1DOo48mCUOfjhyAFnAx6', 'Male', 'South korea', NULL, NULL, '+821048980299', NULL, NULL, '2025-09-27 09:54:08', '2025-09-27 09:54:08', 1, 5, NULL, NULL, 0, 'active', NULL),
(14, 'Shahzad Ali', 'toplinksltd@yahoo.com', 'Shahzad Ali', '$2y$10$o2ls973xvVxJSUxIw2KNHu7hs2hfo.SExYCL5z5raaVfuK5r5Y5yS', 'Male', 'Younsu Gu younsu dong 634', NULL, NULL, '+82 10 8731 0571', NULL, NULL, '2025-09-27 10:01:21', '2025-09-27 10:01:21', 1, 5, NULL, NULL, 0, 'active', NULL),
(15, 'GULFAM0571', 'worldwide.links786@gmail.com', 'GULFAM ARSHAD BHATTI', '$2y$10$dcnS3xl357AxpEkQbY476uMwaSeF2V8tx0gKI6ftx6eeBMexNSlXy', 'Male', 'Incheon Metropolitan City', NULL, NULL, '01035070571', NULL, NULL, '2025-09-27 10:02:40', '2025-09-27 10:02:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(16, 'muneebahmad2801', 'muneebahmad2801@gmail.com', 'Muneeb Ahmad', '$2y$10$7AtRAATQ7GN.5AYk7h/8LOoMHPBUYeKrndzwZKcC4YzpbZ/wX8rsC', 'Male', 'House No: 270 Block: F-1, Wapda Town Lahore\r\nHouse No: 270 Block: F-1', NULL, NULL, '03057380511', NULL, NULL, '2025-09-27 11:19:49', '2025-09-27 11:19:49', 1, 5, NULL, NULL, 0, 'active', NULL),
(17, 'bc190402560', 'bc190402560@vu.edu.pk', 'bc190402560 GHULAM MUJTABA', '$2y$10$lJvztggfeFaRL0IzbxEf5.TSWNaoEzdZi7LZEE9tu3GMvmP//AwkG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-27 11:26:34', '2025-09-27 11:26:34', 1, 5, NULL, NULL, 0, 'active', NULL),
(18, 'Sadia', 'sadianooralvi@gmail.com', 'Sadia Noor', '$2y$10$CcatjSf..Angf2DaCpeQhOqfHqMAAHB5m1Ruj0eQRTYaDywZbc9lS', 'Female', '651- 2D -1 township Lahore', NULL, NULL, '03701420549', NULL, NULL, '2025-09-27 17:53:15', '2025-09-27 17:53:15', 1, 5, NULL, NULL, 0, 'active', NULL),
(19, 'Shayaanali', 'sstrading9480@gmail.com', 'Hafiz Waheed', '$2y$10$epkJ4cqjTGpy9NCw9K3wJuBh9p1D/fkuoWrkGlwxx6PgzDyx9OkGC', 'Male', 'Jungle gu,Incheon South Korea', NULL, NULL, '010 9480 3190', NULL, NULL, '2025-09-28 06:17:02', '2025-09-28 06:17:02', 1, 5, NULL, NULL, 0, 'active', NULL),
(20, 'rajpoot', 'zahid.siddique77@gmail.com', 'ZAHID SIDDIQUE', '$2y$10$4BbRUB02zZRfZiSf4Ij/vuOQOIMkSlW7BM2eg.QwQzIUk7xoaE3se', 'Male', 'South korea', NULL, NULL, '00821056717865', NULL, NULL, '2025-09-28 11:07:21', '2025-09-28 11:07:21', 1, 5, NULL, NULL, 0, 'active', NULL),
(21, 'HAMMAD', 'ghourihammad82@gmail.com', 'GHOURI HAMMAD HASSAN', '$2y$10$YQtWzO/mVhFE5FGWHm..t.np/cTY4MOYMVjROPCI64KvNzZi1sJOe', 'Male', '경상남도 양산시 일동8길 14-2 101호', NULL, NULL, '01074950785', NULL, NULL, '2025-09-28 11:31:18', '2025-09-28 11:31:18', 1, 5, NULL, NULL, 0, 'active', NULL),
(22, 'Ahsan Anees', 'ahsangill1816@gmail.com', 'Ahsan Anees Gill', '$2y$10$entVtOsHy9f8LIBVooWiqOeBd5k0p8L4d2lKPqCB6eb4CsKDRQyNu', 'Male', 'Gyeongsangnam-do Yangsan-si Gohyanguibom 1-gil 21-1', NULL, NULL, '01059250777', NULL, NULL, '2025-09-28 11:31:42', '2025-09-28 11:31:42', 1, 5, NULL, NULL, 0, 'active', NULL),
(23, 'Ali Shah', 'shahmahsoomali1@gmail.com', 'Mahsoom Ali Shah', '$2y$10$Ue.EpXmwJlcUb62aPDFx4OI7IWqtYeWrqedx471gPBpWQFtNnueoq', 'Male', 'Jinyeong , gimhae . south Korea', NULL, NULL, '01080881272', NULL, NULL, '2025-09-28 11:34:27', '2025-09-28 11:34:27', 1, 5, NULL, NULL, 0, 'active', NULL),
(24, 'Irfankr', 'iffikr@yahoo.com', 'IRFAN Ghafoor', '$2y$10$qsdtBtdC4cOZ7i7eTHzqF.zxdmFSGtLSvK9gzrMFKFEOtMNSCt3vG', 'Male', '755 Jung dong bucheon city south korea', NULL, NULL, '+821080596112', NULL, NULL, '2025-09-28 11:39:12', '2025-09-28 11:39:12', 1, 5, NULL, NULL, 0, 'active', NULL),
(25, 'Usman', 'usmangujjar9716@gmail.com', 'Usman  Muhammad', '$2y$10$ClhxU3dBedGca1MSAQjXkOs8tEGbs5.0fFCbO/VzbWEj7eBlvsknS', 'Male', 'Changawon', NULL, NULL, '01048362006', NULL, NULL, '2025-09-28 12:42:22', '2025-09-28 12:42:22', 1, 5, NULL, NULL, 0, 'active', NULL),
(26, 'Mufti112233', 'abdulakbar402@gmail.com', 'ABDUL AKBAR', '$2y$10$esK/u11B3SmC3gvbv.v4f.MNywEkrJftgqy5Ina5JlebcLwXkFmQ.', 'Male', 'Massan bus terminal', NULL, NULL, '01064942971', NULL, NULL, '2025-09-28 13:10:23', '2025-09-28 13:10:23', 1, 5, NULL, NULL, 0, 'active', NULL),
(27, 'Taslim. Yangsan', 'kobir.hossain.0204@gmail.com', 'Taslim Ahmed', '$2y$10$R2xtDFa.f0OyDkw/MglX6OI58L.OvpR6m3/hTYjt.xAkgkioeiZvG', 'Male', '경남 양산시 북정동 554-6', NULL, NULL, '8201027039157', NULL, NULL, '2025-09-28 14:17:46', '2025-09-28 14:17:46', 1, 5, NULL, NULL, 0, 'active', NULL),
(28, 'Nomi777', 'Noumanjaved644@gmail.com', 'Nouman khan', '$2y$10$oVVXzAN3ZOUFhd3drqjv.eWLmWc5Jk9xfJ8.BgnlFlMCUQKsk8rQC', 'Male', 'Changwon', NULL, NULL, '01075040592', NULL, NULL, '2025-09-28 15:43:49', '2025-09-28 15:43:49', 1, 5, NULL, NULL, 0, 'active', NULL),
(29, 'zafararain8811', 'zafararain8811@gmail.com', 'Ali', '$2y$10$umUPI3K0ziuw1AS7PeGWn.k8cNCKUFHNjh4rm4ePmXC8JTRtsyfRG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 16:16:12', '2025-09-28 16:16:12', 1, 5, NULL, NULL, 0, 'active', NULL),
(30, 'cyberpunk3', 'cyberpunk3@gmail.com', 'cyberpunk3', '$2y$10$/UiJ0Q.VIzpUWuMiVmEAM.sTUP5J7UEOGFhbc57vYTZfkdDD2Iy0S', 'Male', 'siafianspfa', NULL, 'Pakistan', '03184621902', '03184621902', NULL, '2025-09-29 08:48:19', '2025-11-07 04:40:55', 1, 5, NULL, NULL, 0, 'active', NULL),
(31, 'cyberpunk4', 'cyberpunk4@gmail.com', 'cyberpunk4', '$2y$10$h2hLDHlF0dkXL5cNpIWaK.XVs5/Fj5qllHjyxm7u3BPMl65pDHdRC', 'Male', 'ffsdgagfg', NULL, NULL, '03184621902', NULL, NULL, '2025-09-29 09:55:34', '2025-09-29 09:55:34', 1, 5, NULL, NULL, 0, 'active', NULL),
(32, 'WASIM', 'wasimr178@gmail.com', 'WASIM MUHAMMAD', '$2y$10$f5eI4OZvp58sD0JJukUkpOkWv1MWUe41m8CWC9YvAAwFuCliIAFwC', 'Male', '평택시 진위면 하북3길22', NULL, NULL, '+821055980786', NULL, NULL, '2025-09-30 01:08:40', '2025-09-30 01:08:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(33, 'Muhammad rahman', 'Muhammadrahmanmr883@gmail.com', 'rahman Muhammad', '$2y$10$0QNtf7Rdsr6x/1aY6ju/PO78cCE2gmeUJx0xuy1r6S74tVRQc47oC', 'Male', '경기도 양주시 남면 개나리5길1,2층', NULL, NULL, '010-4258-0786', NULL, NULL, '2025-09-30 13:24:06', '2025-09-30 13:24:06', 1, 5, NULL, NULL, 0, 'active', NULL),
(34, 'Shakeel.tahir@minhaj.edu.pk', 'shakeel.tahir@minhaj.edu.pk', 'Shakeel Ahmad Tahir', '$2y$10$7EvxdxhnVWj5AIXmr5Gz6emCHEbqSLIeGsWdeI2ucH6dAfcgNOO2S', 'Male', '203C Eden Boulevard College Road Lahore Near Military and Accounts', NULL, NULL, '0300 8272574', NULL, NULL, '2025-10-01 03:48:04', '2025-10-01 03:48:04', 1, 5, NULL, NULL, 0, 'active', NULL),
(35, 'wasif mirza', 'wasifmirza@ymail.com', 'Muhammad wasif Mirza', '$2y$10$kyjYG9EEPq4ca7nciUW3ueeQlHxFTYoWFxHRwfzTpB6pOP4tzOUdC', 'Male', 'yangju', NULL, NULL, '01057370266', NULL, NULL, '2025-10-01 13:45:21', '2025-10-01 13:45:21', 1, 5, NULL, NULL, 0, 'active', NULL),
(37, 'Gul israr', 'gulirshad@gmail.com', 'GULSHAN ISRAR', '$2y$10$Idp.i4Z4RtiVX5yOMADKWOHEY0OAxHyEs.jOTg5UuxAoi7tmJJ2x.', 'Female', 'Incheon south korea', NULL, 'South Korea', '+821073814195', '+821073814195', NULL, '2025-10-01 23:09:01', '2025-11-08 16:15:34', 1, 5, NULL, NULL, 0, 'active', NULL),
(38, 'Muhammad Tariq', 'tariq72000@gmail.com', 'Muhammad Tariq Nadeem', '$2y$10$e7oW.xYyy/I0Gj/URaIVdeZAHr8NrfdmOb4xHAIHaQBAHgqVSqXPG', 'Male', 'Vpo Dalwal, Teh Choa Saiden Shah, District Chakwal', NULL, NULL, '03304258473', NULL, NULL, '2025-10-01 23:53:47', '2025-10-01 23:53:47', 1, 5, NULL, NULL, 0, 'active', NULL),
(39, 'Mudassar Rasool', 'mudassirnoori786@gmail.com', 'Muhammad Mudassar Rasool Noori', '$2y$10$Emp1SRYfcCY4ecQY8.PJPu6EhQwePph14P.3kYLFJIfKd45y58bou', 'Male', 'مین بازار رچنا ٹاؤن تحصیل فیروز والا ضلع شیرپورہ', NULL, NULL, '03059272881', NULL, NULL, '2025-10-02 17:22:11', '2025-10-02 17:22:11', 1, 5, NULL, NULL, 0, 'active', NULL),
(40, 'abdullahsafdar84', 'safad2443@gmail.com', 'Abdullah Safdar', '$2y$10$OKtk4.ptv1GPeb0o6umcI.D9p4vYbkiDCdCO27yqcKq5x2JZxNZpG', 'Male', 'Haripur', NULL, NULL, '010 9753 3302', NULL, NULL, '2025-10-04 12:03:31', '2025-10-04 12:03:31', 1, 5, NULL, NULL, 0, 'active', NULL),
(41, 'Waseem', 'waseembinazam@gmail.com', 'Hafiz Muhammad Waseem Azam', '$2y$10$DkNtLW1IvGsVv5Ua14/D4OhLG1forbfsT1L.bkq.7IzkvlbPdcJTS', 'Male', 'House No V-64 Kiyanni Market, Chaklala Rawalpindi', NULL, NULL, '+821051165351', NULL, NULL, '2025-10-04 12:03:34', '2025-10-04 12:03:34', 1, 5, NULL, NULL, 0, 'active', NULL),
(42, 'Basharat', 'saimbasharat723@gmail.com', 'Saim', '$2y$10$h0HJ8y38tfWamkOsWlgEU.6wzANGAEhQaQhz7.8YlHqwinrhufxvu', 'Male', 'Korea', NULL, NULL, '01072551165', NULL, NULL, '2025-10-04 12:03:54', '2025-10-04 12:03:54', 1, 5, NULL, NULL, 0, 'active', NULL),
(43, 'Najeeb Ullah', 'najeebullahusmani099@gmail.com', 'Najeeb Ullah', '$2y$10$Tu6iGpPRgeK5kvGTddz4EOvnwWD0EmkLQRfbY2ggws3YE0TFu6bhS', 'Male', 'House # 1725/3860 Mohalla Madina Colony Baldia Town Karachi', NULL, NULL, '+923162929099', NULL, NULL, '2025-10-04 12:07:13', '2025-10-04 12:07:13', 1, 5, NULL, NULL, 0, 'active', NULL),
(44, 'Raheem ullah', 'raheemkhan810000@gmail.com', 'Raheem Ullah', '$2y$10$C9JLMJlvZKXt.95h.q3f4ezAXfrvxkvaRbi7urkRHzARDCaL41yNi', 'Male', 'House no 1725/3769 mahajir Camp 5 baldia town karachi', NULL, NULL, '03142457237', NULL, NULL, '2025-10-04 12:08:54', '2025-10-04 12:08:54', 1, 5, NULL, NULL, 0, 'active', NULL),
(45, 'Umair@786', 'Umairsulehri786@gmail.com', 'Muhammad Umair', '$2y$10$8PPmkdBBEJLZKfWQA3fNaOlkwDzs7owAzVGGaNOSuCWIzCYXAy/oa', 'Male', 'Village Rupochak Tehsil Zafarwal District Narowal', NULL, NULL, '03074152753', NULL, NULL, '2025-10-04 14:04:35', '2025-10-04 14:04:35', 1, 5, NULL, NULL, 0, 'active', NULL),
(46, 'Saleem285', 'mss_285@yahoo.com', 'MUHAMMAD SALEEM', '$2y$10$tcwO5hPcPhy4fcblnLWW1uA1s9aoyg30Rd2U3Tn.thYpywVj7ehqi', 'Male', 'Chak no 285 GB rajana toba tek singh', NULL, NULL, '+821043240786', NULL, NULL, '2025-10-04 15:55:32', '2025-10-04 15:55:32', 1, 5, NULL, NULL, 0, 'active', NULL),
(47, 'JAMSHAID', 'jamshaidkorea786@gmail.com', 'Muhammad', '$2y$10$zyuz20gsH0EIqIPtBOhjlOMcGY9FIQaYTzvtESLEoY7S6nFZu2mjq', 'Male', 'Busan South Korea', NULL, NULL, '+821082790786', NULL, NULL, '2025-10-04 20:17:09', '2025-10-04 20:17:09', 1, 5, NULL, NULL, 0, 'active', NULL),
(48, 'Mazhar', 'mazhar6503@gmail.compakistan078699', 'Mazhar iqbal', '$2y$10$cBihbaIcmkL1KfbJY0kySuA1i0ccQ5nrUlwR65OXpn7AWBGfSjmDa', 'Male', '부산광역시', NULL, NULL, '821031840723', NULL, NULL, '2025-10-05 06:18:38', '2025-10-05 06:18:38', 1, 5, NULL, NULL, 0, 'active', NULL),
(49, 'Majid', 'muhammadmajid1083@gmail.com', 'Muhammad majid', '$2y$10$4LpVen8T9TlG/bLx7.8GxOxUx9xAIT4JnNpYGQl0nsNug2QC1kosW', 'Male', '부산광역시 사상구 엄궁로203번길 33(엄궁동)', NULL, NULL, '01027046699', NULL, NULL, '2025-10-05 07:36:49', '2025-10-05 07:36:49', 1, 5, NULL, NULL, 0, 'active', NULL),
(50, 'Sajid.agil', 'sajidreformer@gmail.com', 'Muhammad Sajid Hussain', '$2y$10$/ehViLJmZQO4bGQjgxeDFOCEzP7uiZbkqgyvsb6yPZS8cIFTZ9wG6', 'Male', 'House no 34 street no 7 Gulshan e khudadad phase 5 near Jamil sweets golra more', NULL, NULL, '03003915206', NULL, NULL, '2025-10-05 16:58:02', '2025-10-05 16:58:02', 1, 5, NULL, NULL, 0, 'active', NULL),
(51, 'afaqlovely786@gmail.com', 'afaqlovely786@gmail.com', 'Raza Ahmed', '$2y$10$FHRL2NRosXKe5Z0XZOWI5OVTOJMV1KUv32LBG9XJIWVazCQQuZoY2', 'Male', 'Yangju si korea', NULL, NULL, '01027809904', NULL, NULL, '2025-10-07 06:47:22', '2025-10-07 06:47:22', 1, 5, NULL, NULL, 0, 'active', NULL),
(52, 'uk293285', 'uk293285@gmail.com', 'Muhammad Ubaid', '$2y$10$nPTl2CyJmeKb1F6efPPAQeehX.dmiwSW/AvX7PRp2.Aqz3Ht.u79i', 'Male', 'busan', NULL, NULL, '01021082819', NULL, NULL, '2025-10-07 06:47:40', '2025-10-07 06:47:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(53, 'Ch Haseeb', 'chhaseebswl@gmail.com', 'Haseeb', '$2y$10$70dJKLve/m/W8/M8JZwCcevYfq6FPiSyufE4G4KPm.1TpWBIDeliC', 'Male', 'Yangju', NULL, NULL, '01080501136', NULL, NULL, '2025-10-07 06:47:48', '2025-10-07 06:47:48', 1, 5, NULL, NULL, 0, 'active', NULL),
(54, 'aa820206', 'koreapak.inf@gmail.com', 'Hussain abbas', '$2y$10$zfkVP1scOAp2duUP.7qA0ellh532fEFxsgHSLDINP0AfIVgt4IlWq', 'Male', '경기도 화성시 태안로95번길 28-4 동성빌라 301호', NULL, NULL, '+821039195592', NULL, NULL, '2025-10-07 06:48:49', '2025-10-07 06:48:49', 1, 5, NULL, NULL, 0, 'active', NULL),
(55, 'Kashif', 'awan88449@gmail.com', 'Kashif Hamayyun awan', '$2y$10$OBvpsBA62iShA70AO1N3Z.wDSh/0x.Zu1R3B5/jVpPnjODOfrpi4m', 'Male', 'South korea', NULL, NULL, '+821056732892', NULL, NULL, '2025-10-07 06:49:00', '2025-10-07 06:49:00', 1, 5, NULL, NULL, 0, 'active', NULL),
(56, 'aliamjad7576', 'aliamjad75799@gmail.com', 'AMJAD ALI', '$2y$10$Ur0xA0vzHU24Qs2MMWTtYOCwReZU2ryCLiHCLipVPM2LF7KIetvjG', 'Male', 'Samilro4', NULL, NULL, '+821046837579', NULL, NULL, '2025-10-07 06:49:25', '2025-10-07 06:49:25', 1, 5, NULL, NULL, 0, 'active', NULL),
(57, 'Bhatti', 'bhatti96@hotmail.com', 'Qadoos', '$2y$10$2z8gXwqbY6M8FVMOE4zafOAPowR5pezsFCwfdtovYBAeucOGVkEEi', 'Male', 'bhatti96@hotmail.com', NULL, NULL, '01062047454', NULL, NULL, '2025-10-07 07:04:15', '2025-10-07 07:04:15', 1, 5, NULL, NULL, 0, 'active', NULL),
(58, 'Zahid_443', 'Zahid.saim443@gmail.com', 'Muhammad zahid Sharif', '$2y$10$gIYfOkddBx0pmhIsktpAiuMUKR9oHD8.jpS6tXvMOQXqKhuK/ikOq', 'Male', 'Incheon', NULL, NULL, '010 9290 8241', NULL, NULL, '2025-10-07 07:04:24', '2025-10-07 07:04:24', 1, 5, NULL, NULL, 0, 'active', NULL),
(59, 'GUJJAR JEE', 'matrading443@gmail.com', 'ASHRAF MUHAMMAD', '$2y$10$8Kky1u2Wk1ng.FrlihirxejdS.oyyGooYHdok82dGdAm3n44JmTxC', 'Male', 'Incheon', NULL, NULL, '01094438241', NULL, NULL, '2025-10-07 07:06:25', '2025-10-07 07:06:25', 1, 5, NULL, NULL, 0, 'active', NULL),
(60, 'Irfan', 'Irfanjaved286@gmail.com', 'Javed Muhammad', '$2y$10$ij7BPmh7v1YIJzk7cUiU8O1tt14PquXSkAf78WlPo2uQfa8Xs96x2', 'Male', 'South korea', NULL, NULL, '00821080213664', NULL, NULL, '2025-10-07 14:03:25', '2025-10-07 14:03:25', 1, 5, NULL, NULL, 0, 'active', NULL),
(61, 'Shair Muhammad', 'engrghouri786@gmail.com', 'Shairmuhammad Ghouri', '$2y$10$SoB.CGJLL.tpl3GmtyPuqunctlFwtTzR3zhccacyr4DGx.66cHKPa', 'Male', 'House d377 new Ghouri Muhalla Ubauro', NULL, NULL, '03073792536', NULL, NULL, '2025-10-08 04:08:40', '2025-10-08 04:08:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(62, 'ALI', 'daninm7_kr@yahoo.com', 'ALI MARDAN', '$2y$10$dlg13awnRWjNWWGJbZ56tup13KdUcOunjY5jscSb43rddB2PZOrHm', 'Male', '인천연수구 동춘동820-45', NULL, NULL, '1096900786', NULL, NULL, '2025-10-08 05:57:14', '2025-10-08 05:57:14', 1, 5, NULL, NULL, 0, 'active', NULL),
(63, 'Dr Raza', 'mraza084@gmail.com', 'Dr Muhammad Raza Qadri', '$2y$10$2lTadJ/zYVEw/RgVrVFbMueg9ae0B1rFjSLaPqUs/.hw8RgvjoUzi', 'Male', 'Garaebi, Yangju city Korea', NULL, NULL, '1031550786', NULL, NULL, '2025-10-08 22:02:00', '2025-10-08 22:02:00', 1, 5, NULL, NULL, 0, 'active', NULL),
(64, 'Adnan', 'khokharadnan900@gmail.com', 'Muhammad Adnan', '$2y$10$kI5fnYoTMM1yIQtB4qRod.yTbljnKcT3OY9zEU29yvfehvBh0rIFi', 'Male', 'Hwasing', NULL, NULL, '01066267355', NULL, NULL, '2025-10-09 01:25:49', '2025-10-09 01:25:49', 1, 5, NULL, NULL, 0, 'active', NULL),
(65, 'Aliraza', 'alijan4050@gmail.com', 'Ali Raza', '$2y$10$2YrBUAZuTHmTpNBdcpoIbe0SPWoIoIC1/BFAXXFC.TPxGBq/8nTUO', 'Male', 'Incheon', NULL, NULL, '01035720786', NULL, NULL, '2025-10-09 04:46:11', '2025-10-09 04:46:11', 1, 5, NULL, NULL, 0, 'active', NULL),
(74, 'Amber', 'amberahmedraza786@gmail.com', 'Amber', '$2y$10$2SVXM.e9xblce67JfWk6E..y0uwCn8AW4tAF7iTyHewgzN7AXwTBW', 'Female', 'gujranwala Pakistan', NULL, NULL, '03704213449', NULL, NULL, '2025-10-11 11:45:29', '2025-10-11 11:45:29', 1, 5, NULL, NULL, 0, 'active', NULL),
(79, 'wasif1988', 'wasif350@gmail.com', 'Shahzad Muhammad wasif', '$2y$10$v9jkup8wsszImKhHEUhjeu30xPgDj.Cn/Jf6UwWHCkNrgkBg3LNra', 'Male', '경기도 양주시 남면 개나리길114', NULL, NULL, '+8201057370266', NULL, NULL, '2025-10-14 09:35:26', '2025-10-14 09:35:26', 1, 5, NULL, NULL, 0, 'active', NULL),
(80, 'Shoaib Afzaal', 'alibutt50012@gnail.com', 'Shoaib Afzaal', '$2y$10$2LDKMZeXzoHILVb7cgMDLeY0AJyTUW0HbDURucFxCzyK6BBvvysai', 'Male', 'Incheon', NULL, NULL, '01059095324', NULL, NULL, '2025-10-14 11:50:10', '2025-10-14 11:50:10', 1, 5, NULL, NULL, 0, 'active', NULL),
(81, 'aliamjad7579', 'aliamjad757kr@gmail.com', 'AMJAD ALI', '$2y$10$eJhLC28C0ret6drQZm/efu8C.zWxmzPSEqa7TirUsl/ACgqlHfSTi', 'Male', 'Samileo4', NULL, NULL, '+821051737579', NULL, NULL, '2025-10-14 12:15:35', '2025-10-14 12:15:35', 1, 5, NULL, NULL, 0, 'active', NULL),
(82, 'Azeem', 'anssl2509@gmail.com', 'Azeem Sadia', '$2y$10$MFuJ3kGtIr33oqMs66yeiubT1cSAilZOVEiJCxxkPk6ge7ZvgiZHe', 'Female', 'Hong Kong Kwai Chung', NULL, NULL, '+852 55435390', NULL, NULL, '2025-10-15 14:04:48', '2025-10-15 14:04:48', 1, 5, NULL, NULL, 0, 'active', NULL),
(83, 'Shafiq786', 'gsthk@hotmail.com', 'Shafiq', '$2y$10$2VDRh1T93cJvUXfAJa8Zg.kB2g6Mu3pySnBJ0OQBC/MBtMxQrMtOy', 'Male', 'Kwai chung', NULL, NULL, '64090786', NULL, NULL, '2025-10-15 14:44:13', '2025-10-15 14:44:13', 1, 5, NULL, NULL, 0, 'active', NULL),
(84, 'Sali', 'aariuhammad1234@gmail.com', 'Malik sallah Qayyum', '$2y$10$pWpveMAjuWJVdd8YI3VFCesNEvuFj/riNxiqpF5rrtnwyFQ6wLuOa', 'Female', 'Kwai Chung', NULL, NULL, '54091591', NULL, NULL, '2025-10-15 15:06:10', '2025-10-15 15:06:10', 1, 5, NULL, NULL, 0, 'active', NULL),
(85, 'Tahir1977', 'apnachhachh@gmail.com', 'Tahir mehmood', '$2y$10$8MB3Bw/.q9tJ2L9UykQt9urHjDfd24Yr3CxDal.XUFxBnNNu.1feu', 'Male', 'Hong kong', NULL, NULL, '00852 98277258', NULL, NULL, '2025-10-16 03:28:57', '2025-10-16 03:28:57', 1, 5, NULL, NULL, 0, 'active', NULL),
(86, 'Zeeshan Baig', 'mzeeshanbaig786@gmail.com', 'M zeeshan Baig', '$2y$10$f8cHC9ZX4Uyw5Z0QwgFPN.CGMATo2PD.U.xvVMZ4tk81p8v.vKnSi', 'Male', '398 F II JOHAR TOWN Lahore', NULL, NULL, '03333315553', NULL, NULL, '2025-10-17 01:27:43', '2025-10-17 01:27:43', 1, 5, NULL, NULL, 0, 'active', NULL),
(87, 'MasroorAliSyed', 'write2masroor@yahoo.com', 'Syed Masroor Ali', '$2y$10$tVoyr6NUXyDoF7qDQgiTM.taat2YheeOQR2NoN2ye1DmnvN7RwnaG', 'Male', 'Karachi', NULL, NULL, '923339013106', NULL, NULL, '2025-10-18 07:46:37', '2025-10-18 07:46:37', 1, 5, NULL, NULL, 0, 'active', NULL),
(88, 'Laiq', 'm.laeeq121@hotmail.comm', 'Mian Laiq Ur Rehman', '$2y$10$JZ61Y3vEsp4ibGIi1QtxveyyIz11YKR9kHXd7oZifHLIpH8qRGMD2', 'Male', 'Hong Kong', NULL, NULL, '+85265714612', NULL, NULL, '2025-10-20 10:38:18', '2025-10-20 10:38:18', 1, 5, NULL, NULL, 0, 'active', NULL),
(89, 'gufranhussain17@gmail.com', 'gufranhussain17@gmail.com', 'Aamir Hussain', '$2y$10$TohChnXnmOwMCWu0vHAdmObvvYi4drsRiCy398AV2tMtOkUeXbrlS', 'Male', 'Yangju south korea', NULL, NULL, '01059259002', NULL, NULL, '2025-10-25 11:13:48', '2025-10-25 11:13:48', 1, 5, NULL, NULL, 0, 'active', NULL),
(90, 'Qais sheikh', 'qaisarkhangul44@gmail.com', 'Qaisar khan', '$2y$10$RALZfBE2FAAPJTpf4/E2c.zFPZ6A73bku8k3E/4WSLWB.oJiJeSu2', 'Male', 'Paju si Korea', NULL, NULL, '+8273964544', NULL, NULL, '2025-10-26 04:45:02', '2025-10-26 04:45:02', 1, 5, NULL, NULL, 0, 'active', NULL),
(91, 'Ahsan', 'shahzadahsan71@gmail.com', 'Shahzad ahsan', '$2y$10$7SnbVmEh7fYxjpk8Gwz0A.gTZWcKoeh/KP3KwoYgMzw7ooOU3oLpG', 'Male', 'South korea', NULL, NULL, '+821025989105', NULL, NULL, '2025-10-26 05:50:45', '2025-10-26 05:50:45', 1, 5, NULL, NULL, 0, 'active', NULL),
(92, 'Inham', 'Inhamkhurshidtanoli28@gmail.com', 'Inham khurshid', '$2y$10$DvskBs6elhdQh06lXDpcH.B.lH0IqfHJlN9Lts.8AG4CPZnNSrzQS', 'Male', 'Eunyeon myeon Yangju si', NULL, NULL, '01065169771', NULL, NULL, '2025-10-26 06:12:45', '2025-10-26 06:12:45', 1, 5, NULL, NULL, 0, 'active', NULL),
(93, 'Sohail Muhammed', 'alisohailmuhammad235@gmail.com', 'Ali', '$2y$10$l5/UtEnV/xRV85DhI4OyderFW8Khsyf/4/ZEpAoI5Pz8ly36a5OOW', 'Male', 'Pasrur tehsil pasrur district sialkot', NULL, NULL, '8201043970391', NULL, NULL, '2025-10-26 11:31:55', '2025-10-26 11:31:55', 1, 5, NULL, NULL, 0, 'active', NULL),
(94, 'Mudassar5588', 'mudassarhussain6138@gmail.com', 'Hussain', '$2y$10$cjsuCjF7pT5MA6tkApFDn.WQQzxZrJsm7VJa8oB9qkSZkr8is.PLq', 'Male', 'P o Charwa tehsil pasrur sialkot pakistan', NULL, NULL, '03016138276', NULL, NULL, '2025-10-26 11:51:54', '2025-10-26 11:51:54', 1, 5, NULL, NULL, 0, 'active', NULL),
(95, 'Maan', 'chm98792@gmail.com', 'Abdul rehman', '$2y$10$xE1KML/G2P676ucpsoy7quKakTAg0e/c1drQGCKdKeU.U/3/NuT/e', 'Male', 'íŒŒì£¼', NULL, NULL, '01034905113', NULL, NULL, '2025-10-26 12:02:52', '2025-10-26 12:02:52', 1, 5, NULL, NULL, 0, 'active', NULL),
(96, 'Aqeelyousaf', 'aqeelyousaf3@gmail.com', 'Aqeel yousaf', '$2y$10$mRrnpJUzi49O4xoIT.fSBeNRPX8e8d4hWL0VAdiMZ.ZblAW0hadRm', 'Male', 'Yangju South Korea', NULL, NULL, '1026379499', NULL, NULL, '2025-10-28 10:39:44', '2025-10-28 10:39:44', 1, 5, NULL, NULL, 0, 'active', NULL),
(97, 'AmeerMkhan', 'ameermkhan658@gmail.com', 'AmeerMkhan', '$2y$10$ZiHfCnRI5CE0SXyoKsHcJuxyQ7suyivaYsyUAoHNP7xz5Nk1oKNCS', 'Male', 'Ø²Ø§ÙˆÛŒÛ Ø·Ø±ÛŒÙ‚Û Ù…Ø­Ù…Ø¯ÛŒÛ Ø­Ù‚ÛŒÙ‚Û Ú©Ú¾Ø§Ø±ÛŒØ§Úº', NULL, NULL, '03434565658', NULL, NULL, '2025-11-04 06:43:34', '2025-11-04 06:43:34', 1, 5, NULL, NULL, 0, 'active', NULL),
(98, 'demo_infixedu', 'demo_infixedu@example.com', 'demo_infixedu', '$2y$10$EUPie0jvHZamKnrEwMzmcedRnKGNMKVylxUmvwqBAZ/oAAXEGWgry', 'Male', 'Dummy', NULL, NULL, '03001111111', NULL, NULL, '2025-11-07 04:47:48', '2025-11-07 04:47:48', 1, 5, NULL, NULL, 0, 'active', NULL),
(99, 'G M Alvi', 'gm.alvi@minhaj.edu.pk', 'Ghulam murtaza', '$2y$10$T7lkt8vn0er71/YTP.dWaulmM7UH4yrQYmxnPkeZ5dj6yEzqGcfHS', 'Male', '651. 2D1 township Lahore', NULL, NULL, '0307302281', NULL, NULL, '2025-11-07 15:32:19', '2025-11-07 15:32:19', 1, 5, NULL, NULL, 0, 'active', 1),
(100, 'Faizamuzaffar', 'faizashahzad752@gmail.com', 'Faiza Muzaffar', '$2y$10$koWThExYehF3FosjkOXpW.sDSi3paFiiziCdot24Bq.Fe24kv0Usu', 'Female', 'Mehar fayyaz colony fateh garh Lahore Pakistan', NULL, 'Pakistan', '03020441961', '03020441961', NULL, '2025-11-09 18:30:07', '2025-11-09 18:32:15', 1, 5, NULL, NULL, 0, 'active', NULL),
(101, 'Momnaaz', 'mirzamomna31@gmail.com', 'Momna maaz', '$2y$10$284sW2QVQTkrCGqL3H4s6e7z0aVMjpZ4NVzSPmrQcHdjGv8uRrqEe', 'Female', 'Nai abadi khrala Jhelum\r\nArslan karyana store', NULL, NULL, '03145417042', NULL, NULL, '2025-11-11 13:46:52', '2025-11-11 13:46:52', 1, 5, NULL, NULL, 0, 'active', NULL),
(102, 'Kosar', 'aishakosar3@gmail.com', 'Esha', '$2y$10$9Ii8rJo.Me/yfmH.3UMM8.Corx0pcATXGWA6xxAKDU0MCYLQfZYF6', 'Female', 'Kwai Chung', NULL, NULL, '55453767', NULL, NULL, '2025-11-11 13:46:59', '2025-11-11 13:46:59', 1, 5, NULL, NULL, 0, 'active', NULL),
(103, 'ARB9804', 'Arb09804@gmail.com', 'Abdur Rehman', '$2y$10$jvUXby0Sz8TlsAM8s.7NQOPMEHttI0dLa1/M2PYTm.yb64WPZZkcu', 'Male', '10C, Sun Tai Mansion, 1-6 Shamchun Street, Mong Kok, Hong Kong SAR', NULL, NULL, '+85253364527', NULL, NULL, '2025-11-11 13:47:28', '2025-11-11 13:47:28', 1, 5, NULL, NULL, 0, 'active', NULL),
(104, 'Sehrish121', 'sehrishraja121@gmail.com', 'Sehrash Dilpazir', '$2y$10$d5j.3hyOxqQ0chJ2aS1HAuHtG.uvj5/kIL5S.0gJI4Yxqbx8eTCnm', 'Female', 'Room E,F/9,wai yin building,432 castle peak road,kwai chung.N.T', NULL, 'China', '59770007', '59770007', NULL, '2025-11-11 13:51:14', '2025-11-12 13:51:28', 1, 5, NULL, NULL, 0, 'active', NULL),
(105, 'basat786', 'basat4@hotmail.com', 'Mohammad Basat Ali', '$2y$10$Daii3WV55JON0Gp/rJST/.3qb9vHzIaFI.PEKf/i5nZdV53jzj6p6', 'Male', 'Rm 4016 40/F shek cheunv house shek lei estate kwai chung N.T', NULL, 'China', '56077014', '56077014', NULL, '2025-11-11 13:51:35', '2025-11-11 13:54:10', 1, 5, NULL, NULL, 0, 'active', NULL),
(106, 'Cchharoon435@gmail.com', 'cchharoon435@gmail.com', 'Haroon', '$2y$10$82s8aU6KTRH7909ONihVIeNX9lPguTVM98s6yzU2eZiHIVO2EWqW2', 'Female', 'Lei muk shue block 6 8 floor 801 hk new territories', NULL, NULL, '95424951', NULL, NULL, '2025-11-11 14:17:22', '2025-11-11 14:17:22', 1, 5, NULL, NULL, 0, 'active', NULL),
(107, 'Areeb', 'areebrashid10@gmail.com', 'Mahmood Areeb Rashid', '$2y$10$1RVRGyrdV0Jbl/.VQJTSkekNbXd27XmxjjORx6qCEVIQmkcQOw8We', 'Male', 'Flat 1715, floor 17, Kwai Hau Street, Shing Kwok House, Kwai Shing East Estate, NT.', NULL, 'China', '67601404', '67601404', NULL, '2025-11-12 04:06:11', '2025-11-12 04:11:07', 1, 5, NULL, NULL, 0, 'active', NULL),
(108, 'cyberpunk7', 'cyberpunk7@wwe.com', 'cyberpunk7', '$2y$10$bfonV0qq/lUGlSiqojtvautsRUgF07dbMbIvlas0fYYSpLtwfEeV6', 'Male', 'cyberpunk7', NULL, NULL, '123456789123', NULL, NULL, '2025-11-12 08:12:33', '2025-11-12 08:12:33', 1, 5, NULL, NULL, 0, 'active', NULL),
(109, 'Afeefarehman', 'afeefarehmanali@gmail.com', 'Afeefa Rehman', '$2y$10$GVF.qjNOkmWksaE1NtTyZucHL8l.nhJniS0ti/I8GPWuMggYsfQau', 'Female', 'Choi Hung estate, Kowloon, Hongkong', NULL, 'China', '90180974', '90180974', NULL, '2025-11-12 12:49:35', '2025-11-12 12:52:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(110, 'Murtaza', 'jattmurtaza7@gmail.com', 'Murtaza Muhammad', '$2y$10$EcOHzMkSI0conXXniZw0mOu0r2xS7uNoT1ZJ4t4lW.ptk5b3SngZm', 'Male', 'Hong Kong', NULL, NULL, '93783707', NULL, NULL, '2025-11-12 12:54:23', '2025-11-12 12:54:23', 1, 5, NULL, NULL, 0, 'active', NULL),
(111, 'amina2427', 'riazamina2427@gmail.com', 'Amina', '$2y$10$XNgg34huNXDO7ZbhVYxnteXQSH9EdvcnWpwBPMPtWs8FiPkmRWpdm', 'Female', 'Prefer not to say', NULL, NULL, '56658567', NULL, NULL, '2025-11-12 13:48:53', '2025-11-12 13:48:53', 1, 5, NULL, NULL, 0, 'active', NULL),
(112, 'asim115.ak', 'asim115.ak@gmail.com', 'Asim Muhammad', '$2y$10$n7SfKOpyYmv3axIXb5nCd.gKDx29dGEVlTIGan3DiRjJzBZpxrFX.', 'Male', 'Hong Kong', NULL, 'China', '91479097', '91479097', NULL, '2025-11-12 13:49:37', '2025-11-12 13:52:31', 1, 5, NULL, NULL, 0, 'active', NULL),
(113, '512Faisal', 'ume_faisal@yahoo.com', 'Faisal', '$2y$10$3JTw2/AaM0JFzmsRrvuSXOveeb01FeGRkBGg0di4bmXRRBQRNQXf6', 'Male', 'Wong Tai sin', NULL, NULL, '85298676267', NULL, NULL, '2025-11-12 13:57:27', '2025-11-12 13:57:27', 1, 5, NULL, NULL, 0, 'active', NULL),
(114, 'Hasan', 'k.hasan906@yahoo.com', 'Hasan Mohammad Kamrul', '$2y$10$Z4MAoN2hg9YmJ2cHbv5ipOjoE7V0npM3CLquA6PAY7ourlhKa2GTG', 'Male', 'Ansan South Korea', NULL, 'South Korea', '01027572320', '01027572320', NULL, '2025-11-13 08:58:48', '2025-11-13 09:00:26', 1, 5, NULL, NULL, 0, 'active', NULL),
(115, 'ranamobasharahmad', 'ranamobasharahmad@gmail.com', 'Rana Mobashar Ahmad', '$2y$10$fBsEQRxQSwvcEnRc58UoM.IgoDFNFrCOp5dl8RFor.8jYikkmvnlO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-14 15:16:50', '2025-11-14 15:16:50', 1, 5, NULL, NULL, 0, 'active', NULL),
(116, 'Abeeha', 'shehnazwaseem2001@gmail.com', 'Shahnaz Akhter', '$2y$10$tcJg7a0sw.1SmD962KO6Oe.OqjQSaqzcykqSW3gjurRL2ZXG/5ko2', 'Female', 'Mustafavi House Rafique Abad Narang Mandi', NULL, NULL, '03314181299', NULL, NULL, '2025-11-15 05:41:41', '2025-11-15 05:41:41', 1, 5, NULL, NULL, 0, 'active', NULL),
(117, 'Hassan', 'hassansaeedi786@gmail.com', 'Hassan saeedi', '$2y$10$fj7dwhQcj8IBJ5k7yw7V8uVycY4Cby5svvigAEbBTqcwDSBRru2uS', 'Male', 'Sialkot', NULL, NULL, '010 2766 9401', NULL, NULL, '2025-11-15 13:06:46', '2025-11-15 13:06:46', 1, 5, NULL, NULL, 0, 'active', NULL),
(121, 'Nazir Awan', 'nazirawan895@gmail.com', 'Nazir khalil', '$2y$10$n2gPuMceuraJdR5ssWecs.5II65apEIUzUX1HIeQgTxgHymQG7kgG', 'Male', 'Pakistan', NULL, NULL, '01030866895', NULL, NULL, '2025-11-15 13:09:13', '2025-11-15 13:09:13', 1, 5, NULL, NULL, 0, 'active', NULL),
(128, 'israr', 'fsi92cn@yahoo.com', 'MUHAMMAD ISRAR UL HAQ', '$2y$10$PCbj8p3XMmuZnauC28zWquMVoVWYkwmPg0PmOM3fh05MMyHfNfLPi', 'Male', 'Incheon south korea', NULL, NULL, '01055299691', NULL, NULL, '2025-11-15 13:36:53', '2025-11-15 13:36:53', 1, 5, NULL, NULL, 0, 'active', NULL),
(129, '3840322157277', 'eman.waqar79@gmail.com', 'Waqar Ahmed', '$2y$10$UFeO2KoNsyGKFTTlOF4/yuZshI4H/cfPncZMYVvqtxoz/ia9QbKYO', 'Male', 'Haider Abad Town Sargodha', NULL, NULL, '03004028540', NULL, NULL, '2025-11-16 09:30:47', '2025-11-16 09:30:47', 1, 5, NULL, NULL, 0, 'active', NULL),
(130, 'Khadija', 'mahmoodsonia06@gmail.com', 'Begum khadija', '$2y$10$.i6hWBriq.2njjDLyIJMR.sezM/oc7k8jSAFZoQ/EZBXp.At354pW', 'Female', 'Room 1714, 17/f, Shek Kai house, Shek Lei Estate Kwai Chung N.T. Hong Kong', NULL, 'China', '62115654', '62115654', NULL, '2025-11-16 13:41:13', '2025-11-16 13:44:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(131, 'Umme Naveed', 'humtahir25@gmail.com', 'Humaira Riaz', '$2y$10$EClOq19gefMfAXaMSQCkj.jNn8xqZGflbGoKRR.gF5Gp4CRlXsYDK', 'Female', 'FLAT 1014\r\nFLAT 1014 WANG WAI HOUSE, WANG TAU HOM\r\n1014', NULL, NULL, '62791776', NULL, NULL, '2025-11-17 12:48:40', '2025-11-17 12:48:40', 1, 5, NULL, NULL, 0, 'active', NULL),
(132, 'admintestschool', 'admintestschool@theteacher.pk', 'Admin Test School', '$2y$10$sn9QHsZF1vysW4chMNXa/uHpBqgfhwlKKry8IkPYbHrNBA.y/3b3.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-17 14:48:55', '2025-11-17 14:48:55', 1, 3, 4, 4, 0, 'active', 1),
(133, 'Tanveer Hk', 'alibabuali@yahoo.com', 'Tanveer Hussain shah', '$2y$10$igxxxCLC0MaSwTC12G0QqO40m7SA7ChxdR1zCbXUtwgP5/bvsiKb.', 'Male', '2113 floor yiu sin House wong Tai sin', NULL, 'China', '0085268258899', '0085268258899', NULL, '2025-11-17 15:31:34', '2025-11-27 12:47:35', 1, 5, NULL, NULL, 0, 'active', NULL),
(134, 'Alam Iftikhar', 'iftikharalam777.ia@gmail.com', 'Alam', '$2y$10$VhYkvvLS/HCssE1E9K2fvuq7m1kRLJG.T9xgLjKq/TOOnufMeNtyu', 'Male', 'South korea', NULL, NULL, '01059150786', NULL, NULL, '2025-11-19 12:30:27', '2025-11-19 12:30:27', 1, 5, NULL, NULL, 0, 'active', NULL),
(135, 'Sultan143', 'tasawarsultan39@gmail.com', 'Sultan Tasawar', '$2y$10$lm9r4ufIPS1kzQ3oe5vSv.56rPGUyj0AHD6bexEUnbvpXPWPC7E.6', 'Male', 'South korea', NULL, NULL, '+821080941401', NULL, NULL, '2025-11-19 12:30:29', '2025-11-19 12:30:29', 1, 5, NULL, NULL, 0, 'active', NULL),
(136, 'waqasbhalli_5', 'mw32497@gmail.com', 'WAQAS', '$2y$10$xrWgBcdh0LohaCLPhzhJUOEx9YHHfmvPn2yzQmlZpQ2BmbeCcjkLa', 'Male', 'ê²½ê¸° ì‹œí¥ì‹œ ì†Œë§ê³µì›ë¡œ 323', NULL, NULL, '+821029898002', NULL, NULL, '2025-11-19 12:30:30', '2025-11-19 12:30:30', 1, 5, NULL, NULL, 0, 'active', NULL),
(137, 'Awais', 'awaishaider661@gmail.com', 'Haidar', '$2y$10$tFWkr0aglaW8qOZc3sy2Fubakp0n14bfqmiiC6QE6cPw0ptR8Vyh6', 'Male', 'Korea', NULL, NULL, '+821067865511', NULL, NULL, '2025-11-19 12:30:42', '2025-11-19 12:30:42', 1, 5, NULL, NULL, 0, 'active', NULL),
(138, 'Aqib Tanoli', 'aqibtanoli888@gmail.com', 'AQIB MUHAMMAD', '$2y$10$sJ.YfwsNGzTdK5feMWORUuYJM3WYnKQL4iewWxz9VVf6/MRHlCIta', 'Male', 'Southkorea', NULL, NULL, '+8201028254323', NULL, NULL, '2025-11-19 12:30:53', '2025-11-19 12:30:53', 1, 5, NULL, NULL, 0, 'active', NULL),
(139, 'IMRAN1', 'imran.note255@gmail.com', 'MUHAMMAD IMRAN', '$2y$10$MIYM7ntlSMnTBWkV0GdICeFwvnc24953nB9tOr2qg1Zlb/YtO9YfS', 'Male', 'Oide street no 5 house no 201', NULL, NULL, '+821056919428', NULL, NULL, '2025-11-19 12:31:18', '2025-11-19 12:31:18', 1, 5, NULL, NULL, 0, 'active', NULL),
(140, 'Irfan7250', 'irfanalisb7250@gmail.com', 'ALI IRFAN', '$2y$10$EhbjwzI67XZQsSDxZ8CRaObYE0PHm69cgpEU45s2o3rFHurlOuIQi', 'Male', 'South Korea', NULL, NULL, '01067687250', NULL, NULL, '2025-11-19 12:32:00', '2025-11-19 12:32:00', 1, 5, NULL, NULL, 0, 'active', NULL),
(141, 'Tasbih', 'tasbihullah70@gmail.com', 'Ullah Tasbih', '$2y$10$rb4k2WwEm5CW0RjekVDY/ewDEvZnL9Kilq6eag5oc7vkgYplAx7Lq', 'Male', 'South Korea', NULL, NULL, '01084759021', NULL, NULL, '2025-11-19 13:25:29', '2025-11-19 13:25:29', 1, 5, NULL, NULL, 0, 'active', NULL),
(142, 'Yasir143', 'yasirshami119@gmail.com', 'MUHAMMAD YASIR', '$2y$10$B6IMiwHSkrSI/hDSLhA.keyaAYPhas34dT/xcG30Iw4Lf/u4e4sEK', 'Male', 'South korea', NULL, NULL, '+821080958978', NULL, NULL, '2025-11-19 13:36:53', '2025-11-19 13:36:53', 1, 5, NULL, NULL, 0, 'active', NULL),
(143, 'ayubmalik', 'ayubmalik92@gmail.com', 'Muhammad Ayub malik', '$2y$10$G0n.ORVmP.qtFhoLdRhjXeKZnN7YHMptiDE3lX.1qbUABDITB8C6e', 'Male', 'Johansburg', NULL, NULL, '0027624016965', NULL, NULL, '2025-11-22 19:19:04', '2025-11-22 19:19:04', 1, 5, NULL, NULL, 0, 'active', NULL),
(144, 'tanveer95gb@gmail.com', 'tanveer95gb@gmail.com', 'Tanveer muhammad', '$2y$10$Kbhg7DXmcg0J63d9E1LM1e2bmgNYabCXHfY1LxV1T7FkzHpBqwh5q', 'Male', 'ê²½ê¸°ë„ ì‹œí¥ì‹œ ì†Œë§ê³µì›ë¡œ 127(ì •ì™•ë™)', NULL, NULL, '01058910894', NULL, NULL, '2025-11-25 12:16:52', '2025-11-25 12:16:52', 1, 5, NULL, NULL, 0, 'active', NULL),
(145, 'Malik Amir', 'malik.amir966@gmail.com', 'Malik Amir', '$2y$10$9Iaga8wUOZXNplVSX8Wage8collXmZQnAyhU9w0eFuTeMW1TREcna', 'Male', 'Korea', NULL, NULL, '01058020966', NULL, NULL, '2025-11-30 05:26:58', '2025-11-30 05:26:58', 1, 5, NULL, NULL, 0, 'active', NULL),
(146, 'waseem10066', 'waseem10066@gmail.com', 'Malik Waseem', '$2y$10$s5MCyS7WELxlO9p4Mj1Z1uqnDqgOq2hlsJ/BL6gL5YjTV5RME52KS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-01 01:05:41', '2025-12-01 01:05:41', 1, 5, NULL, NULL, 0, 'active', NULL),
(147, 'Asif', 'asif209ak@gmail.com', 'ASIF KHAN', '$2y$10$Yw/3uBa/RlPp5SeEp/ELnObvCWDdeinyIZSSD3glxt1xxcMOqhnOu', 'Male', '2/F flat209 Lok tin house tsz Lok estate tsz wan shan', NULL, NULL, '67407741', NULL, NULL, '2025-12-01 03:18:13', '2025-12-01 03:18:13', 1, 5, NULL, NULL, 0, 'active', NULL),
(148, 'Nassemkhan112', 'naseemkhan112@gmail.com', 'Muhammad Naseem Naqshbandi', '$2y$10$GGt6y9UrVmmu5b77v7TOtOxfYplbbr/sN9OoLc74h95NIbtt66RsS', 'Male', 'Tai Pak Tin St\r\n33A', NULL, NULL, '93408641', NULL, NULL, '2025-12-01 03:39:55', '2025-12-01 03:39:55', 1, 5, NULL, NULL, 0, 'active', NULL),
(149, 'Arslan', 'HafizArslan1234567890@gmail.com', 'Ahmed Arslan', '$2y$10$tjiwvGUsoVgI2JTfqDXydeO5YIglwtd8uNcCBVzq04qo.7pAMB8/W', 'Male', '2/F 14 Flat chun kwai house kwai chung estate N.T', NULL, NULL, '52169772', NULL, NULL, '2025-12-01 03:46:52', '2025-12-01 03:46:52', 1, 5, NULL, NULL, 0, 'active', NULL),
(150, 'Shukria rub', 'shukriarub@gmail.com', 'Ali Shukria Rahmat', '$2y$10$9DTcY5AdZuASebmVNIpQH.V9PrBfyEnInPA9OgDe.CBtb1jUv233m', 'Female', 'Yiu yam house\r\n hong Kong', NULL, NULL, '+85263919165', NULL, NULL, '2025-12-01 04:10:45', '2025-12-01 04:10:45', 1, 5, NULL, NULL, 0, 'active', NULL),
(151, 'Madiha', 'madihafatima0129@gmail.com', 'Madiha', '$2y$10$8RJnl7jjYNBMjNl5ui0kXOv6sT8l.yUtZdcY2FcHqvmJLs0DojCHW', 'Female', 'Hong kong', NULL, NULL, '46129572', NULL, NULL, '2025-12-01 05:51:03', '2025-12-01 05:51:03', 1, 5, NULL, NULL, 0, 'active', NULL),
(152, 'Chakwali', 'hassankahout64@gmail.com', 'Ali', '$2y$10$NN2w3h4z49ZM1iv646cjk.d2fxDx33wb1.riYt13jtBdHUnSSq5Mq', 'Male', 'Sing San house choi wan estate 212 house', NULL, NULL, '60313786', NULL, NULL, '2025-12-01 11:42:57', '2025-12-01 11:42:57', 1, 5, NULL, NULL, 0, 'active', NULL),
(156, 'Babascaty@gmail.com', 'babascaty@gmail.com', 'Waqas Ali', '$2y$10$FePGzAimyZtylEHDeMu./un5ugUdlrg8oUOvxZK.n0/poLOk2Wu/u', 'Male', 'lambi gali buunkan', NULL, NULL, '03014152722', NULL, NULL, '2025-12-06 07:51:32', '2026-02-08 17:14:36', 1, 3, 4, 6, 0, 'active', NULL),
(157, 'gnuman96', 'gnuman96@gmail.com', 'Numan', '$2y$10$9AlyNomivmpfdL8IA.1sQ.P4eYLS8QPRrR9dlJzHoixmh7YM61.r.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-09 07:01:14', '2025-12-09 07:01:14', 1, 5, NULL, NULL, 0, 'active', NULL),
(161, 'tayyab', 'tayyab786mt786@gmail.com', 'Chaudhry Muhammad Tayyab', '$2y$10$i1IxyHyBjrSqWNV/PApxp.Tol.eY0O.27BVGywacx.dX.FhVEJg56', 'Male', 'Lok fu wts Kowloon', NULL, NULL, '51786426', NULL, NULL, '2025-12-11 11:33:35', '2025-12-11 11:33:35', 1, 5, NULL, NULL, 0, 'active', NULL),
(162, 'Syed123', 'hassanasher40@gmail.com', 'Syed Muhammad Hassan Asher', '$2y$10$Em1bDSyMJFszvMuvpk7K/e0JV18NpK7vEK1GZY4RtSLiCEe8.a3xy', 'Male', 'B24, sector 11A, northkarachi', NULL, NULL, '03333619867', NULL, 'uploads/users/user_162.jpg', '2026-01-04 01:53:08', '2026-01-04 01:58:26', 1, 5, NULL, NULL, 0, 'active', NULL),
(163, 'Abbas', 'infozaheerabbaswoodworks@gmail.com', 'Zaheer', '$2y$10$Xb2RYFOIbXSUzzUskUG4hOiXOMGshO9poJz6kFt9ElVoWYRVy/LBS', 'Male', 'L', NULL, NULL, '03004674306', NULL, NULL, '2026-02-05 01:42:27', '2026-02-05 01:42:27', 1, 5, NULL, NULL, 0, 'active', NULL),
(164, 'hmfaizurrasool@gmail.com', 'hmfaizurrasool@gmail.com', 'Hafiz Muhammad Faiz Ur Rasool', '$2y$10$dLoKAIGlc2OJUREf545k3e7EEkNJXOiiGs/DVqKwwDQ.IUwWRf2t.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-08 16:26:20', '2026-02-08 16:42:00', 1, 3, 4, 5, 0, 'active', 1),
(169, 'Asad Siddiqui', 'ziag787@gmail.com', 'HAFIZ ASAD MEHMOOD SIDDIQUI', '$2y$10$MU5eCuROx2DCs6P6y9t8h.5ZxztoRTHScIF8xlf1oREZt2DjC3j9O', 'Male', 'Jamia Masjid Farooqia 18E Wah Cantt', NULL, NULL, '03105999821', NULL, NULL, '2026-02-16 02:11:42', '2026-02-16 02:11:42', 1, 5, NULL, NULL, 0, 'active', NULL),
(170, 'MKJ', 'm.kashif7860@yahoo.com', 'Muhammad Kashif Javed', '$2y$10$/37nHrGBtOWZfA0n.FWAwu2ndC.kfQc69VkCVFWafOocH/T5i4C7K', 'Male', 'Kareemabad Rawalpindi', NULL, NULL, '0311 3834136', NULL, NULL, '2026-02-20 04:53:02', '2026-02-20 04:53:02', 1, 5, NULL, NULL, 0, 'active', NULL),
(171, 'Sagir Ali', 'imransagir212@gmail.com', 'Sagir imran', '$2y$10$DSIQ9ukmAY.mONKJcF1RcuH/HGTOnVWJpDdLU2MMiRQfZnxNTGUsa', 'Male', 'Khurrianwala', NULL, NULL, '03027294566', NULL, NULL, '2026-02-20 12:28:25', '2026-02-20 12:28:25', 1, 5, NULL, NULL, 0, 'active', NULL),
(172, 'ansarigmustafa92@gmail.com', 'ansarigmustafa92@gmail.com', 'Ghulam Mustafa', '$2y$10$vz..uPFcW91VZoIb9HAntOtr9ZyEEXJVlnrEcN0zkmM4QHYJnvNgq', NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/users/user_172.jpg', '2026-03-10 09:57:16', '2026-04-18 08:03:28', 1, 1, NULL, NULL, 0, 'active', 1),
(173, 'Nooragha', 'ra.grcr@mul.edu.pk', 'Syed Noor Salahuddin', '$2y$10$8alRN00hYMsS.l8L5YTGNelo2Fg.TPRtxIen1y.ygG/9.ktxnVoSC', 'Male', '365 M block minhaj ul quran Internationa', NULL, NULL, '03488213911', NULL, NULL, '2026-03-20 02:48:10', '2026-03-20 02:48:10', 1, 5, NULL, NULL, 0, 'active', NULL),
(174, 'Zafar', 'zafarwali632@gmail.com', 'Zafar Iqbal', '$2y$10$7LjsD88Lunv87vTstbmVIOtu6bKuWoluDx8wy8otjF4a0NpQ7BlQS', 'Male', '223-16-BI, Township, Lahore', NULL, NULL, '03334758820', NULL, NULL, '2026-03-21 04:57:07', '2026-03-21 04:57:07', 1, 5, NULL, NULL, 0, 'active', NULL),
(175, 'Hgms786', 'drhgms365@gmail.com', 'Ghulam Muhi Ul Din Shahid', '$2y$10$zgOya20E6Kjv20aTR41sfOEGg5cm.jeNbd/Z2ZMzPak45sFwiRvAq', 'Male', 'Pak makkah Taugh Taile Nazeer Town Bhakkar', NULL, NULL, '03454871269', NULL, NULL, '2026-03-27 02:36:12', '2026-03-27 02:36:12', 1, 5, NULL, NULL, 0, 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_class_permissions`
--

CREATE TABLE `user_class_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `granted_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_class_permissions`
--

INSERT INTO `user_class_permissions` (`id`, `user_id`, `class_id`, `granted_by`, `created_at`) VALUES
(1, 2, 1, 1, '2025-09-15 21:09:56'),
(2, 3, 1, 2, '2025-09-17 00:01:01'),
(3, 8, 3, 1, '2025-09-22 18:40:38'),
(4, 9, 1, 1, '2025-09-22 18:57:36'),
(5, 4, 1, 1, '2025-09-17 00:35:34'),
(7, 7, 1, 6, '2025-09-17 21:33:58'),
(12, 11, 9, 1, '2025-09-22 20:32:32'),
(13, 12, 7, 1, '2025-09-27 07:36:59'),
(14, 15, 7, 1, '2025-09-27 15:07:20'),
(15, 14, 7, 1, '2025-09-27 15:07:20'),
(16, 13, 7, 1, '2025-09-27 15:07:20'),
(17, 5, 13, 1, '2025-11-07 04:37:10'),
(18, 5, 14, 1, '2025-11-07 04:37:10'),
(19, 132, 13, 1, '2025-11-17 14:51:47'),
(20, 164, 17, 1, '2026-02-08 16:42:40'),
(21, 156, 17, 1, '2026-02-08 17:16:16');

-- --------------------------------------------------------

--
-- Table structure for table `zoom_meetings`
--

CREATE TABLE `zoom_meetings` (
  `id` int(11) NOT NULL,
  `meeting_title` varchar(200) NOT NULL,
  `meeting_description` text DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `syllabus_id` int(11) DEFAULT NULL,
  `lecture_id` int(11) DEFAULT NULL,
  `zoom_meeting_id` varchar(100) DEFAULT NULL,
  `meeting_url` varchar(500) DEFAULT NULL,
  `passcode` varchar(50) DEFAULT NULL,
  `scheduled_date` datetime NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `host_email` varchar(255) DEFAULT NULL,
  `max_participants` int(11) DEFAULT 100,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `is_recurring` tinyint(1) DEFAULT 0 COMMENT 'Is this a recurring meeting',
  `recurrence_type` varchar(20) DEFAULT NULL COMMENT 'daily, weekly, monthly',
  `recurrence_interval` int(11) DEFAULT 1 COMMENT 'Repeat every X days/weeks/months',
  `recurrence_days` varchar(50) DEFAULT NULL COMMENT 'Days of week for weekly (1,2,3,4,5)',
  `recurrence_end_date` datetime DEFAULT NULL COMMENT 'When recurrence ends',
  `recurrence_end_times` int(11) DEFAULT NULL COMMENT 'Number of occurrences',
  `parent_meeting_id` int(11) DEFAULT NULL COMMENT 'Links recurring instances',
  `waiting_room` tinyint(1) DEFAULT 1 COMMENT 'Enable waiting room',
  `join_before_host` tinyint(1) DEFAULT 0 COMMENT 'Allow join before host',
  `approval_type` tinyint(1) DEFAULT 2 COMMENT '0=auto, 1=manual, 2=no registration',
  `mute_upon_entry` tinyint(1) DEFAULT 1 COMMENT 'Mute participants on entry',
  `host_video` tinyint(1) DEFAULT 1 COMMENT 'Host video on by default',
  `participant_video` tinyint(1) DEFAULT 0 COMMENT 'Participant video on by default',
  `audio_type` varchar(20) DEFAULT 'both' COMMENT 'both, voip, telephony',
  `auto_recording` varchar(20) DEFAULT 'none' COMMENT 'none, local, cloud',
  `allow_multiple_devices` tinyint(1) DEFAULT 0 COMMENT 'Allow multiple devices per user',
  `screen_sharing` varchar(20) DEFAULT 'all' COMMENT 'all or host',
  `enable_chat` tinyint(1) DEFAULT 1 COMMENT 'Enable meeting chat',
  `enable_private_chat` tinyint(1) DEFAULT 1 COMMENT 'Allow private chat',
  `enable_raise_hand` tinyint(1) DEFAULT 1 COMMENT 'Enable raise hand feature',
  `enable_reactions` tinyint(1) DEFAULT 1 COMMENT 'Enable meeting reactions',
  `enable_breakout_rooms` tinyint(1) DEFAULT 0 COMMENT 'Enable breakout rooms'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zoom_meetings`
--

INSERT INTO `zoom_meetings` (`id`, `meeting_title`, `meeting_description`, `class_id`, `subject_id`, `syllabus_id`, `lecture_id`, `zoom_meeting_id`, `meeting_url`, `passcode`, `scheduled_date`, `duration_minutes`, `host_email`, `max_participants`, `created_by`, `created_at`, `updated_at`, `status`, `is_recurring`, `recurrence_type`, `recurrence_interval`, `recurrence_days`, `recurrence_end_date`, `recurrence_end_times`, `parent_meeting_id`, `waiting_room`, `join_before_host`, `approval_type`, `mute_upon_entry`, `host_video`, `participant_video`, `audio_type`, `auto_recording`, `allow_multiple_devices`, `screen_sharing`, `enable_chat`, `enable_private_chat`, `enable_raise_hand`, `enable_reactions`, `enable_breakout_rooms`) VALUES
(1, 'Translation Quran Course 1', 'multimedia translation Quran Class', 13, 4, NULL, NULL, '', '', '', '2025-10-09 07:00:00', 60, 'admin@syllabus.com', 100, 1, '2025-10-08 16:43:12', '2025-10-08 16:45:47', 'cancelled', 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 0, 2, 1, 1, 0, 'both', 'none', 0, 'all', 1, 1, 1, 1, 0),
(2, 'Translation Quran Course 1', 'multimedia translation Quran class', 13, 4, NULL, NULL, '', '', '', '2025-10-09 19:00:00', 60, 'gm.alvi@minhaj.edu.pk', 100, 1, '2025-10-08 16:47:42', '2025-10-08 18:13:54', 'cancelled', 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 0, 2, 1, 1, 0, 'both', 'none', 0, 'all', 1, 1, 1, 1, 0),
(3, 'Class-1', 'First Meeting', 13, NULL, NULL, NULL, '', '', '', '2025-10-09 19:30:00', 60, 'gm.alvi@minhaj.edu.pk', 100, 1, '2025-10-08 16:49:17', '2025-10-08 18:13:42', 'cancelled', 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 0, 2, 1, 1, 0, 'both', 'none', 0, 'all', 1, 1, 1, 1, 0),
(4, 'Translation Quran Course South Koria Class 1', 'multimedia translation Quran class', 13, 4, NULL, NULL, '89252590853', 'https://us06web.zoom.us/j/89252590853?pwd=89Y0MUrNaJu7lGaUaJ6uaORATHqOvE.1', '194286', '2025-10-09 19:00:00', 60, 'gm.alvi@minhaj.edu.pk', 100, 1, '2025-10-08 18:14:55', '2025-10-13 13:03:02', 'cancelled', 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 0, 2, 1, 1, 0, 'both', 'none', 0, 'all', 1, 1, 1, 1, 0),
(5, 'Translation Quran Course South Koria Class 2', 'Class 2', 13, 4, NULL, NULL, '82406029021', 'https://us06web.zoom.us/j/82406029021?pwd=eYEMIbyzp4brAbZQTZal0EfhaA0MWb.1', '194286', '2025-10-09 19:40:00', 40, 'gm.alvi@minhaj.edu.pk', 100, 1, '2025-10-09 10:04:17', '2025-10-13 13:02:54', 'cancelled', 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 0, 2, 1, 1, 0, 'both', 'none', 0, 'all', 1, 1, 1, 1, 0),
(6, 'Translation Quran Course South Koria Class', 'Translation Quran Class Koria', 13, 4, NULL, NULL, '85781721582', 'https://us06web.zoom.us/j/85781721582?pwd=X5WG9Zvt8hNUYwbXOmhDhoAiV9fGmL.1', '194286', '2025-10-14 20:30:00', 40, 'gm.alvi@minhaj.edu.pk', 100, 1, '2025-10-09 11:23:15', '2025-10-15 12:21:30', 'scheduled', 1, 'daily', 1, NULL, NULL, 10, NULL, 0, 1, 2, 1, 1, 0, 'both', 'none', 1, 'host', 1, 0, 1, 1, 0),
(7, 'meeting w mujtaba', '', 13, 4, NULL, NULL, '89749346168', 'https://us06web.zoom.us/j/89749346168?pwd=M1iv5AWhnQxFv5UIhPaObruKrqJ7Ah.1', '199684', '2025-10-18 16:20:00', 60, 'gm.alvi@minhaj.edu.pk', 100, 1, '2025-10-18 06:21:01', '2025-10-19 17:23:44', 'cancelled', 0, NULL, 1, NULL, NULL, NULL, NULL, 0, 0, 2, 1, 1, 0, 'both', 'none', 0, 'all', 1, 1, 1, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batch_code` (`batch_code`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `batch_enrollments`
--
ALTER TABLE `batch_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_batch_user` (`batch_id`,`user_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `batch_registration_links`
--
ALTER TABLE `batch_registration_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link_token` (`link_token`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_code` (`class_code`);

--
-- Indexes for table `class_inquiries`
--
ALTER TABLE `class_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_class_inquiry` (`user_id`,`class_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_class_id` (`class_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_inquiry_filters` (`status`,`country`),
  ADD KEY `idx_inquiry_created` (`created_at`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lectures_syllabus` (`syllabus_id`),
  ADD KEY `idx_lectures_type` (`lecture_type`);

--
-- Indexes for table `lecture_files`
--
ALTER TABLE `lecture_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture_id` (`lecture_id`),
  ADD KEY `idx_lecture_files_type` (`file_type`),
  ADD KEY `idx_lecture_files_status` (`status`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_users`
--
ALTER TABLE `pending_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `school_id` (`school_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subject_class` (`subject_code`,`class_id`),
  ADD KEY `idx_subjects_class` (`class_id`);

--
-- Indexes for table `syllabi`
--
ALTER TABLE `syllabi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_syllabi_subject` (`subject_id`),
  ADD KEY `idx_syllabi_class` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `school_id` (`school_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `user_class_permissions`
--
ALTER TABLE `user_class_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_class` (`user_id`,`class_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `granted_by` (`granted_by`);

--
-- Indexes for table `zoom_meetings`
--
ALTER TABLE `zoom_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `syllabus_id` (`syllabus_id`),
  ADD KEY `lecture_id` (`lecture_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_zoom_meetings_waiting_room` (`waiting_room`),
  ADD KEY `idx_zoom_meetings_scheduled_date` (`scheduled_date`),
  ADD KEY `idx_zoom_meetings_status` (`status`),
  ADD KEY `idx_zoom_meetings_is_recurring` (`is_recurring`),
  ADD KEY `idx_zoom_meetings_parent_meeting` (`parent_meeting_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `batch_enrollments`
--
ALTER TABLE `batch_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `batch_registration_links`
--
ALTER TABLE `batch_registration_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `class_inquiries`
--
ALTER TABLE `class_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `lecture_files`
--
ALTER TABLE `lecture_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pending_users`
--
ALTER TABLE `pending_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `syllabi`
--
ALTER TABLE `syllabi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `user_class_permissions`
--
ALTER TABLE `user_class_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `zoom_meetings`
--
ALTER TABLE `zoom_meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_class_fk` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batches_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `batches_instructor_fk` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `batch_enrollments`
--
ALTER TABLE `batch_enrollments`
  ADD CONSTRAINT `batch_enrollments_approver_fk` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `batch_enrollments_batch_fk` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batch_enrollments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `batch_registration_links`
--
ALTER TABLE `batch_registration_links`
  ADD CONSTRAINT `batch_links_batch_fk` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batch_links_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `class_inquiries`
--
ALTER TABLE `class_inquiries`
  ADD CONSTRAINT `class_inquiries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_inquiries_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_inquiries_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lecture_files`
--
ALTER TABLE `lecture_files`
  ADD CONSTRAINT `lecture_files_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pending_users`
--
ALTER TABLE `pending_users`
  ADD CONSTRAINT `pending_users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `pending_users_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pending_users_ibfk_3` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pending_users_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pending_users_ibfk_5` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `syllabi`
--
ALTER TABLE `syllabi`
  ADD CONSTRAINT `syllabi_ibfk_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zoom_meetings`
--
ALTER TABLE `zoom_meetings`
  ADD CONSTRAINT `zoom_meetings_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `zoom_meetings_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `zoom_meetings_ibfk_3` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `zoom_meetings_ibfk_4` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `zoom_meetings_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
