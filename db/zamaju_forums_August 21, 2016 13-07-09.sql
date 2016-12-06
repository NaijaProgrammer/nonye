-- ---------------------------------------------------------
--
-- SIMPLE SQL Dump
-- 
-- http://www.nawa.me/
--
-- Host Connection Info: localhost via TCP/IP
-- Generation Time: August 21, 2016 at 13:07 PM ( Europe/Paris )
-- Server version: 5.6.17
-- PHP Version: 5.5.12
--
-- ---------------------------------------------------------



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- ---------------------------------------------------------
--
-- Table structure for table : `tiny_url_master`
--
-- ---------------------------------------------------------

CREATE TABLE `tiny_url_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `long_url` varchar(256) NOT NULL,
  `tiny_url` varchar(200) NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tiny_url_master`
--

INSERT INTO `tiny_url_master` (`id`, `long_url`, `tiny_url`, `created_date`) VALUES
(1, 'http://localhost/sites/zamaju-forums/posts/1/this-is-the-first-post', 'http://localhost/sites/zamaju-forums/p/1', '2016-07-31 14:11:32'),
(2, 'http://localhost/sites/zamaju-forums/posts/2/first-test-of-auto-embed', 'http://localhost/sites/zamaju-forums/p/2', '2016-08-03 20:47:01'),
(3, 'http://localhost/sites/zamaju-forums/posts/3/this-is-a-youtube-video', 'http://localhost/sites/zamaju-forums/p/3', '2016-08-03 21:01:28'),
(4, 'http://localhost/sites/zamaju-forums/posts/4/testing-all', 'http://localhost/sites/zamaju-forums/p/4', '2016-08-03 22:11:46'),
(5, 'http://localhost/sites/zamaju-forums/posts/5/ok-na', 'http://localhost/sites/zamaju-forums/p/5', '2016-08-04 00:49:11'),
(6, 'http://localhost/sites/zamaju-forums/posts/6/with-daily-motion-video', 'http://localhost/sites/zamaju-forums/p/6', '2016-08-04 01:25:06'),
(7, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals', 'http://localhost/sites/zamaju-forums/p/7', '2016-08-05 10:57:28'),
(8, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals#post-response-8', 'http://localhost/sites/zamaju-forums/c/8', '2016-08-05 13:39:20'),
(9, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals#post-response-9', 'http://localhost/sites/zamaju-forums/c/9', '2016-08-05 13:46:04'),
(10, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals#post-response-9', 'http://localhost/sites/zamaju-forums/c/9', '2016-08-20 00:05:22'),
(11, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals#post-response-10', 'http://localhost/sites/zamaju-forums/c/10', '2016-08-20 00:07:05'),
(12, 'http://localhost/sites/zamaju-forums/posts/6/with-daily-motion-video#post-response-11', 'http://localhost/sites/zamaju-forums/c/11', '2016-08-20 00:09:14');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_app_settings`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_app_settings` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(255) DEFAULT NULL,
  `setting_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_app_settings`
--

INSERT INTO `zf_app_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'site-name', 'Zamaju Forums'),
(2, 'active-theme', 'default'),
(3, 'session-lifetime', 60),
(4, 'password-min-length', 6),
(5, 'default-user-image-url', 'http://localhost/sites/zamaju-forums/resources/images/default-user-avatar.png'),
(6, 'registration-success-message', '&lt;p&gt;Dear {{username}},&lt;/p&gt;&lt;p&gt;Thank you for signing up on {{site_name}}, your reliable and trusted knowledge community.&lt;br&gt;We hope you find the answers you seek through your use of {{site_name}} services.&lt;br&gt;And we look forward to learning from your wealth of knowledge and experience.&lt;/p&gt;&lt;p&gt;-- The {{site_name}} team'),
(7, 'registration-success-mail-sender', 'admin@mysite.com'),
(8, 'password-recovery-mail', '&lt;p&gt;You are receiving this mail because someone has requested a password reset for your {{site_name}} account&lt;br /&gt;If you did not initiate this request, then you need not do anything further.&lt;br /&gt;However, if you would like to reset your password, click on the link below: &lt;br /&gt;&lt;a href=&quot;{{password_reset_url}}?nonce={{nonce}}&quot;&gt;Reset Password&lt;/a&gt;&lt;br/&gt;&lt;br /&gt;(NOTE: This operation must be performed within 24 hours of receiving this email)&lt;/p&gt;'),
(9, 'password-recovery-mail-sender', 'password-recovery@mysite.com');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_categories`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `creator_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_categories`
--

INSERT INTO `zf_categories` (`id`, `name`, `description`, `creator_id`, `date_added`) VALUES
(1, 'bollywood', '', 1, '2016-07-31 13:07:16'),
(2, 'celebrities', '', 1, '2016-07-31 13:07:16'),
(3, 'christianity', '', 1, '2016-07-31 13:07:16'),
(4, 'computers', '', 1, '2016-07-31 13:07:16'),
(5, 'ghollywood', '', 1, '2016-07-31 13:07:16'),
(6, 'hollywood', '', 1, '2016-07-31 13:07:16'),
(7, 'investment', '', 1, '2016-07-31 13:07:16'),
(8, 'islam', '', 1, '2016-07-31 13:07:16'),
(9, 'jokes', '', 1, '2016-07-31 13:07:16'),
(10, 'literature', '', 1, '2016-07-31 13:07:16'),
(11, 'money', '', 1, '2016-07-31 13:07:16'),
(12, 'movies', '', 1, '2016-07-31 13:07:16'),
(13, 'music', '', 1, '2016-07-31 13:07:16'),
(14, 'nollywood', '', 1, '2016-07-31 13:07:16'),
(15, 'phones', '', 1, '2016-07-31 13:07:16'),
(16, 'radio', '', 1, '2016-07-31 13:07:16'),
(17, 'romance', '', 1, '2016-07-31 13:07:16'),
(18, 'stocks', '', 1, '2016-07-31 13:07:16'),
(19, 'television', '', 1, '2016-07-31 13:07:16'),
(20, 'vacancies', '', 1, '2016-07-31 13:07:16'),
(21, 'video', '', 1, '2016-07-31 13:07:16'),
(22, 'web design', '', 1, '2016-07-31 13:07:16');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_category_posts`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_category_posts` (
  `category_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_category_posts`
--

INSERT INTO `zf_category_posts` (`category_id`, `post_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7);



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_comments`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `author_id` int(11) NOT NULL,
  `content` longtext,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_forum_categories`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_forum_categories` (
  `forum_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`forum_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_forum_categories`
--

INSERT INTO `zf_forum_categories` (`forum_id`, `category_id`) VALUES
(7, 1);



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_forum_posts`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_forum_posts` (
  `forum_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`forum_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_forum_posts`
--

INSERT INTO `zf_forum_posts` (`forum_id`, `post_id`) VALUES
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(7, 7);



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_forums`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_forums`
--

INSERT INTO `zf_forums` (`id`, `creator_id`, `name`, `description`, `date_added`) VALUES
(1, 1, 'Agriculture', '', '2016-07-31 13:06:17'),
(2, 1, 'Arts', '', '2016-07-31 13:06:17'),
(3, 1, 'Business', '', '2016-07-31 13:06:17'),
(4, 1, 'Jobs and Careers', '', '2016-07-31 13:06:17'),
(5, 1, 'Culture', '', '2016-07-31 13:06:17'),
(6, 1, 'Education', '', '2016-07-31 13:06:17'),
(7, 1, 'Events and Entertainment', '', '2016-07-31 13:06:18'),
(8, 1, 'Family', '', '2016-07-31 13:06:18'),
(9, 1, 'Fashion', '', '2016-07-31 13:06:18'),
(10, 1, 'Games', '', '2016-07-31 13:06:18'),
(11, 1, 'Health', '', '2016-07-31 13:06:18'),
(12, 1, 'Politics', '', '2016-07-31 13:06:18'),
(13, 1, 'Programming', '', '2016-07-31 13:06:18'),
(14, 1, 'Real Estate', '', '2016-07-31 13:06:18'),
(15, 1, 'Relationship', '', '2016-07-31 13:06:18'),
(16, 1, 'Religion', '', '2016-07-31 13:06:18'),
(17, 1, 'Science', '', '2016-07-31 13:06:18'),
(18, 1, 'Sports', '', '2016-07-31 13:06:18'),
(19, 1, 'Technology', '', '2016-07-31 13:06:18'),
(20, 1, 'Vacation', '', '2016-07-31 13:06:18');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_item_meta`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_item_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(15) DEFAULT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1009 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_item_meta`
--

INSERT INTO `zf_item_meta` (`id`, `item_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'name', 'Super Admin'),
(2, 1, 'category', 'available-user-roles'),
(3, 2, 'name', 'Access Admin Portal'),
(4, 2, 'category', 'available-user-capabilities'),
(5, 3, 'name', 'View Site Settings'),
(6, 3, 'category', 'available-user-capabilities'),
(7, 4, 'name', 'Edit Site Settings'),
(8, 4, 'category', 'available-user-capabilities'),
(9, 5, 'name', 'Manage Site Settings'),
(10, 5, 'category', 'available-user-capabilities'),
(11, 6, 'name', 'View Users'),
(12, 6, 'category', 'available-user-capabilities'),
(13, 7, 'name', 'Edit Users'),
(14, 7, 'category', 'available-user-capabilities'),
(15, 8, 'name', 'Delete Users'),
(16, 8, 'category', 'available-user-capabilities'),
(17, 9, 'name', 'Manage Users'),
(18, 9, 'category', 'available-user-capabilities'),
(19, 10, 'name', 'View Capabilities'),
(20, 10, 'category', 'available-user-capabilities'),
(21, 11, 'name', 'Edit Capabilities'),
(22, 11, 'category', 'available-user-capabilities'),
(23, 12, 'name', 'Manage Capabilities'),
(24, 12, 'category', 'available-user-capabilities'),
(25, 13, 'name', 'View Roles'),
(26, 13, 'category', 'available-user-capabilities'),
(27, 14, 'name', 'Edit Roles'),
(28, 14, 'category', 'available-user-capabilities'),
(29, 15, 'name', 'Manage Roles'),
(30, 15, 'category', 'available-user-capabilities'),
(31, 16, 'category', 'user-activities'),
(32, 16, 'object_id', 1),
(33, 16, 'object_type', 'post'),
(34, 16, 'subject_id', 1),
(35, 16, 'subject_action', 'create'),
(36, 16, 'time_created', 1469967091),
(37, 17, 'category', 'post-views-track'),
(38, 17, 'post-id', 1),
(39, 17, 'viewer-id', 1),
(40, 17, 'ip-address', '::1'),
(41, 17, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(42, 17, 'view-time', 1469967105),
(43, 18, 'category', 'post-views-track'),
(44, 18, 'post-id', 1),
(45, 18, 'viewer-id', 1),
(46, 18, 'ip-address', '::1'),
(47, 18, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(48, 18, 'view-time', 1469967258),
(49, 19, 'category', 'post-views-track'),
(50, 19, 'post-id', 1),
(51, 19, 'viewer-id', 1),
(52, 19, 'ip-address', '::1'),
(53, 19, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(54, 19, 'view-time', 1469967913),
(55, 20, 'category', 'post-views-track'),
(56, 20, 'post-id', 1),
(57, 20, 'ip-address', '::1'),
(58, 20, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(59, 20, 'view-time', 1470000682),
(60, 21, 'category', 'post-views-track'),
(61, 21, 'post-id', 1),
(62, 21, 'viewer-id', 1),
(63, 21, 'ip-address', '::1'),
(64, 21, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(65, 21, 'view-time', 1470005565),
(66, 22, 'category', 'post-views-track'),
(67, 22, 'post-id', 1),
(68, 22, 'ip-address', '::1'),
(69, 22, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(70, 22, 'view-time', 1470125059),
(71, 23, 'category', 'post-views-track'),
(72, 23, 'post-id', 1),
(73, 23, 'viewer-id', 1),
(74, 23, 'ip-address', '::1'),
(75, 23, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(76, 23, 'view-time', 1470233595),
(77, 24, 'category', 'post-views-track'),
(78, 24, 'post-id', 1),
(79, 24, 'viewer-id', 1),
(80, 24, 'ip-address', '::1'),
(81, 24, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(82, 24, 'view-time', 1470233830),
(83, 25, 'category', 'post-views-track'),
(84, 25, 'post-id', 1),
(85, 25, 'viewer-id', 1),
(86, 25, 'ip-address', '::1'),
(87, 25, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(88, 25, 'view-time', 1470238748),
(89, 26, 'category', 'post-views-track'),
(90, 26, 'post-id', 1),
(91, 26, 'viewer-id', 1),
(92, 26, 'ip-address', '::1'),
(93, 26, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(94, 26, 'view-time', 1470238943),
(95, 27, 'category', 'post-views-track'),
(96, 27, 'post-id', 1),
(97, 27, 'viewer-id', 1),
(98, 27, 'ip-address', '::1'),
(99, 27, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(100, 27, 'view-time', 1470239009),
(101, 28, 'category', 'post-views-track'),
(102, 28, 'post-id', 1),
(103, 28, 'viewer-id', 1),
(104, 28, 'ip-address', '::1'),
(105, 28, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(106, 28, 'view-time', 1470239144),
(107, 29, 'category', 'post-views-track'),
(108, 29, 'post-id', 1),
(109, 29, 'viewer-id', 1),
(110, 29, 'ip-address', '::1'),
(111, 29, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(112, 29, 'view-time', 1470239289),
(113, 30, 'category', 'post-views-track'),
(114, 30, 'post-id', 1),
(115, 30, 'viewer-id', 1),
(116, 30, 'ip-address', '::1'),
(117, 30, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(118, 30, 'view-time', 1470239443),
(119, 31, 'category', 'user-activities'),
(120, 31, 'object_id', 2),
(121, 31, 'object_type', 'post'),
(122, 31, 'subject_id', 1),
(123, 31, 'subject_action', 'create'),
(124, 31, 'time_created', 1470250019),
(125, 32, 'category', 'post-views-track'),
(126, 32, 'post-id', 2),
(127, 32, 'viewer-id', 1),
(128, 32, 'ip-address', '::1'),
(129, 32, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(130, 32, 'view-time', 1470250559),
(131, 33, 'category', 'post-views-track'),
(132, 33, 'post-id', 2),
(133, 33, 'viewer-id', 1),
(134, 33, 'ip-address', '::1'),
(135, 33, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(136, 33, 'view-time', 1470250576),
(137, 34, 'category', 'user-activities'),
(138, 34, 'object_id', 3),
(139, 34, 'object_type', 'post'),
(140, 34, 'subject_id', 1),
(141, 34, 'subject_action', 'create'),
(142, 34, 'time_created', 1470250887),
(143, 35, 'category', 'post-views-track'),
(144, 35, 'post-id', 3),
(145, 35, 'viewer-id', 1),
(146, 35, 'ip-address', '::1'),
(147, 35, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(148, 35, 'view-time', 1470250899),
(149, 36, 'category', 'post-views-track'),
(150, 36, 'post-id', 2),
(151, 36, 'viewer-id', 1),
(152, 36, 'ip-address', '::1'),
(153, 36, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(154, 36, 'view-time', 1470251058),
(155, 37, 'category', 'user-activities'),
(156, 37, 'object_id', 4),
(157, 37, 'object_type', 'post'),
(158, 37, 'subject_id', 1),
(159, 37, 'subject_action', 'create'),
(160, 37, 'time_created', 1470255104),
(161, 38, 'category', 'post-views-track'),
(162, 38, 'post-id', 4),
(163, 38, 'viewer-id', 1),
(164, 38, 'ip-address', '::1'),
(165, 38, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(166, 38, 'view-time', 1470255146),
(167, 39, 'category', 'post-views-track'),
(168, 39, 'post-id', 4),
(169, 39, 'viewer-id', 1),
(170, 39, 'ip-address', '::1'),
(171, 39, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(172, 39, 'view-time', 1470255568),
(173, 40, 'category', 'post-views-track'),
(174, 40, 'post-id', 2),
(175, 40, 'viewer-id', 1),
(176, 40, 'ip-address', '::1'),
(177, 40, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(178, 40, 'view-time', 1470260274),
(179, 41, 'category', 'post-views-track'),
(180, 41, 'post-id', 4),
(181, 41, 'viewer-id', 1),
(182, 41, 'ip-address', '::1'),
(183, 41, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(184, 41, 'view-time', 1470260287),
(185, 42, 'category', 'post-views-track'),
(186, 42, 'post-id', 4),
(187, 42, 'viewer-id', 1),
(188, 42, 'ip-address', '::1'),
(189, 42, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(190, 42, 'view-time', 1470260415),
(191, 43, 'category', 'post-views-track'),
(192, 43, 'post-id', 4),
(193, 43, 'viewer-id', 1),
(194, 43, 'ip-address', '::1'),
(195, 43, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(196, 43, 'view-time', 1470260456),
(197, 44, 'category', 'post-views-track'),
(198, 44, 'post-id', 4),
(199, 44, 'viewer-id', 1),
(200, 44, 'ip-address', '::1'),
(201, 44, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(202, 44, 'view-time', 1470260553),
(203, 45, 'category', 'post-views-track'),
(204, 45, 'post-id', 4),
(205, 45, 'viewer-id', 1),
(206, 45, 'ip-address', '::1'),
(207, 45, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(208, 45, 'view-time', 1470260676),
(209, 46, 'category', 'post-views-track'),
(210, 46, 'post-id', 4),
(211, 46, 'viewer-id', 1),
(212, 46, 'ip-address', '::1'),
(213, 46, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(214, 46, 'view-time', 1470260980),
(215, 47, 'category', 'post-views-track'),
(216, 47, 'post-id', 4),
(217, 47, 'viewer-id', 1),
(218, 47, 'ip-address', '::1'),
(219, 47, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(220, 47, 'view-time', 1470261044),
(221, 48, 'category', 'post-views-track'),
(222, 48, 'post-id', 4),
(223, 48, 'viewer-id', 1),
(224, 48, 'ip-address', '::1'),
(225, 48, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(226, 48, 'view-time', 1470261258),
(227, 49, 'category', 'post-views-track'),
(228, 49, 'post-id', 4),
(229, 49, 'viewer-id', 1),
(230, 49, 'ip-address', '::1'),
(231, 49, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(232, 49, 'view-time', 1470261414),
(233, 50, 'category', 'post-views-track'),
(234, 50, 'post-id', 4),
(235, 50, 'viewer-id', 1),
(236, 50, 'ip-address', '::1'),
(237, 50, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(238, 50, 'view-time', 1470261502),
(239, 51, 'category', 'post-views-track'),
(240, 51, 'post-id', 4),
(241, 51, 'viewer-id', 1),
(242, 51, 'ip-address', '::1'),
(243, 51, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(244, 51, 'view-time', 1470261542),
(245, 52, 'category', 'post-views-track'),
(246, 52, 'post-id', 4),
(247, 52, 'viewer-id', 1),
(248, 52, 'ip-address', '::1'),
(249, 52, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(250, 52, 'view-time', 1470261726),
(251, 53, 'category', 'post-views-track'),
(252, 53, 'post-id', 4),
(253, 53, 'viewer-id', 1),
(254, 53, 'ip-address', '::1'),
(255, 53, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(256, 53, 'view-time', 1470261771),
(257, 54, 'category', 'post-views-track'),
(258, 54, 'post-id', 4),
(259, 54, 'viewer-id', 1),
(260, 54, 'ip-address', '::1'),
(261, 54, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(262, 54, 'view-time', 1470261880),
(263, 55, 'category', 'post-views-track'),
(264, 55, 'post-id', 4),
(265, 55, 'viewer-id', 1),
(266, 55, 'ip-address', '::1'),
(267, 55, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(268, 55, 'view-time', 1470261971),
(269, 56, 'category', 'post-views-track'),
(270, 56, 'post-id', 4),
(271, 56, 'viewer-id', 1),
(272, 56, 'ip-address', '::1'),
(273, 56, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(274, 56, 'view-time', 1470262056),
(275, 57, 'category', 'post-views-track'),
(276, 57, 'post-id', 4),
(277, 57, 'viewer-id', 1),
(278, 57, 'ip-address', '::1'),
(279, 57, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(280, 57, 'view-time', 1470262362),
(281, 58, 'category', 'post-views-track'),
(282, 58, 'post-id', 4),
(283, 58, 'viewer-id', 1),
(284, 58, 'ip-address', '::1'),
(285, 58, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(286, 58, 'view-time', 1470262656),
(287, 59, 'category', 'post-views-track'),
(288, 59, 'post-id', 4),
(289, 59, 'viewer-id', 1),
(290, 59, 'ip-address', '::1'),
(291, 59, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(292, 59, 'view-time', 1470262963),
(293, 60, 'category', 'post-views-track'),
(294, 60, 'post-id', 4),
(295, 60, 'viewer-id', 1),
(296, 60, 'ip-address', '::1'),
(297, 60, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(298, 60, 'view-time', 1470263410),
(299, 61, 'category', 'post-views-track'),
(300, 61, 'post-id', 4),
(301, 61, 'viewer-id', 1),
(302, 61, 'ip-address', '::1'),
(303, 61, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(304, 61, 'view-time', 1470263523),
(305, 62, 'category', 'post-views-track'),
(306, 62, 'post-id', 4),
(307, 62, 'viewer-id', 1),
(308, 62, 'ip-address', '::1'),
(309, 62, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(310, 62, 'view-time', 1470264019),
(311, 63, 'category', 'post-views-track'),
(312, 63, 'post-id', 4),
(313, 63, 'viewer-id', 1),
(314, 63, 'ip-address', '::1'),
(315, 63, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(316, 63, 'view-time', 1470264304),
(317, 64, 'category', 'user-activities'),
(318, 64, 'object_id', 5),
(319, 64, 'object_type', 'post'),
(320, 64, 'subject_id', 1),
(321, 64, 'subject_action', 'create'),
(322, 64, 'time_created', 1470264550),
(323, 65, 'category', 'post-views-track'),
(324, 65, 'post-id', 5),
(325, 65, 'viewer-id', 1),
(326, 65, 'ip-address', '::1'),
(327, 65, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(328, 65, 'view-time', 1470264561),
(329, 66, 'category', 'user-activities'),
(330, 66, 'object_id', 6),
(331, 66, 'object_type', 'post'),
(332, 66, 'subject_id', 1),
(333, 66, 'subject_action', 'create'),
(334, 66, 'time_created', 1470266704),
(335, 67, 'category', 'post-views-track'),
(336, 67, 'post-id', 6),
(337, 67, 'viewer-id', 1),
(338, 67, 'ip-address', '::1'),
(339, 67, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(340, 67, 'view-time', 1470266724),
(341, 68, 'category', 'post-views-track'),
(342, 68, 'post-id', 6),
(343, 68, 'viewer-id', 1),
(344, 68, 'ip-address', '::1'),
(345, 68, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(346, 68, 'view-time', 1470266817),
(347, 69, 'category', 'post-views-track'),
(348, 69, 'post-id', 6),
(349, 69, 'viewer-id', 1),
(350, 69, 'ip-address', '::1'),
(351, 69, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(352, 69, 'view-time', 1470266931),
(353, 70, 'category', 'post-views-track'),
(354, 70, 'post-id', 6),
(355, 70, 'viewer-id', 1),
(356, 70, 'ip-address', '::1'),
(357, 70, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(358, 70, 'view-time', 1470266942),
(359, 71, 'category', 'user_profile_viewers'),
(360, 71, 'user_id', 1),
(361, 71, 'viewer_id', 1),
(362, 72, 'category', 'user_profile_viewers'),
(363, 72, 'user_id', 1),
(364, 72, 'viewer_id', 1),
(365, 73, 'category', 'user_profile_viewers'),
(366, 73, 'user_id', 1),
(367, 73, 'viewer_id', 1),
(368, 74, 'category', 'user_profile_viewers'),
(369, 74, 'user_id', 1),
(370, 74, 'viewer_id', 1),
(371, 75, 'category', 'post-views-track'),
(372, 75, 'post-id', 6),
(373, 75, 'viewer-id', 1),
(374, 75, 'ip-address', '::1'),
(375, 75, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(376, 75, 'view-time', 1470307072),
(377, 76, 'category', 'post-views-track'),
(378, 76, 'post-id', 6),
(379, 76, 'viewer-id', 1),
(380, 76, 'ip-address', '::1'),
(381, 76, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(382, 76, 'view-time', 1470307204),
(383, 77, 'category', 'post-views-track'),
(384, 77, 'post-id', 6),
(385, 77, 'viewer-id', 1),
(386, 77, 'ip-address', '::1'),
(387, 77, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(388, 77, 'view-time', 1470307602),
(389, 78, 'category', 'post-views-track'),
(390, 78, 'post-id', 6),
(391, 78, 'viewer-id', 1),
(392, 78, 'ip-address', '::1'),
(393, 78, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(394, 78, 'view-time', 1470307625),
(395, 79, 'category', 'user_profile_viewers'),
(396, 79, 'user_id', 1),
(397, 79, 'viewer_id', 1),
(398, 80, 'category', 'user_profile_viewers'),
(399, 80, 'user_id', 1),
(400, 80, 'viewer_id', 1),
(401, 81, 'category', 'post-views-track'),
(402, 81, 'post-id', 4),
(403, 81, 'viewer-id', 1),
(404, 81, 'ip-address', '::1'),
(405, 81, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(406, 81, 'view-time', 1470307792),
(407, 82, 'category', 'user-activities'),
(408, 82, 'object_id', 7),
(409, 82, 'object_type', 'post'),
(410, 82, 'subject_id', 1),
(411, 82, 'subject_action', 'create'),
(412, 82, 'time_created', 1470387447),
(413, 83, 'category', 'post-views-track'),
(414, 83, 'post-id', 7),
(415, 83, 'viewer-id', 1),
(416, 83, 'ip-address', '::1'),
(417, 83, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(418, 83, 'view-time', 1470387468),
(419, 84, 'category', 'post-views-track'),
(420, 84, 'post-id', 1),
(421, 84, 'viewer-id', 1),
(422, 84, 'ip-address', '::1'),
(423, 84, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(424, 84, 'view-time', 1470389227),
(425, 85, 'category', 'post-views-track'),
(426, 85, 'post-id', 2),
(427, 85, 'viewer-id', 1),
(428, 85, 'ip-address', '::1'),
(429, 85, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(430, 85, 'view-time', 1470389245),
(431, 86, 'category', 'post-views-track'),
(432, 86, 'post-id', 2),
(433, 86, 'viewer-id', 1),
(434, 86, 'ip-address', '::1'),
(435, 86, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(436, 86, 'view-time', 1470389368),
(437, 87, 'category', 'post-views-track'),
(438, 87, 'post-id', 2),
(439, 87, 'viewer-id', 1),
(440, 87, 'ip-address', '::1'),
(441, 87, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(442, 87, 'view-time', 1470389390),
(443, 88, 'category', 'post-views-track'),
(444, 88, 'post-id', 2),
(445, 88, 'viewer-id', 1),
(446, 88, 'ip-address', '::1'),
(447, 88, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(448, 88, 'view-time', 1470389419),
(449, 89, 'category', 'post-views-track'),
(450, 89, 'post-id', 2),
(451, 89, 'viewer-id', 1),
(452, 89, 'ip-address', '::1'),
(453, 89, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(454, 89, 'view-time', 1470389447),
(455, 90, 'category', 'post-views-track'),
(456, 90, 'post-id', 2),
(457, 90, 'viewer-id', 1),
(458, 90, 'ip-address', '::1'),
(459, 90, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(460, 90, 'view-time', 1470389616),
(461, 91, 'category', 'post-views-track'),
(462, 91, 'post-id', 2),
(463, 91, 'viewer-id', 1),
(464, 91, 'ip-address', '::1'),
(465, 91, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(466, 91, 'view-time', 1470389706),
(467, 92, 'category', 'post-views-track'),
(468, 92, 'post-id', 2),
(469, 92, 'viewer-id', 1),
(470, 92, 'ip-address', '::1'),
(471, 92, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(472, 92, 'view-time', 1470389884),
(473, 93, 'category', 'post-views-track'),
(474, 93, 'post-id', 2),
(475, 93, 'viewer-id', 1),
(476, 93, 'ip-address', '::1'),
(477, 93, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(478, 93, 'view-time', 1470389985),
(479, 94, 'category', 'post-views-track'),
(480, 94, 'post-id', 2),
(481, 94, 'viewer-id', 1),
(482, 94, 'ip-address', '::1'),
(483, 94, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(484, 94, 'view-time', 1470390054),
(485, 95, 'category', 'post-views-track'),
(486, 95, 'post-id', 2),
(487, 95, 'viewer-id', 1),
(488, 95, 'ip-address', '::1'),
(489, 95, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(490, 95, 'view-time', 1470390213),
(491, 96, 'category', 'post-views-track'),
(492, 96, 'post-id', 2),
(493, 96, 'viewer-id', 1),
(494, 96, 'ip-address', '::1'),
(495, 96, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(496, 96, 'view-time', 1470390485),
(497, 97, 'category', 'post-views-track'),
(498, 97, 'post-id', 2),
(499, 97, 'viewer-id', 1),
(500, 97, 'ip-address', '::1'),
(501, 97, 'referrer', 'http://localhost/sites/zamaju-forums/?v=list'),
(502, 97, 'view-time', 1470390563),
(503, 98, 'category', 'post-views-track'),
(504, 98, 'post-id', 6),
(505, 98, 'viewer-id', 1),
(506, 98, 'ip-address', '::1'),
(507, 98, 'referrer', 'http://localhost/sites/zamaju-forums/posts/tagged/oembed/'),
(508, 98, 'view-time', 1470390796),
(509, 99, 'category', 'post-views-track'),
(510, 99, 'post-id', 6),
(511, 99, 'viewer-id', 1),
(512, 99, 'ip-address', '::1'),
(513, 99, 'referrer', 'http://localhost/sites/zamaju-forums/posts/tagged/oembed/'),
(514, 99, 'view-time', 1470390847),
(515, 100, 'category', 'post-views-track'),
(516, 100, 'post-id', 6),
(517, 100, 'viewer-id', 1),
(518, 100, 'ip-address', '::1'),
(519, 100, 'referrer', 'http://localhost/sites/zamaju-forums/posts/tagged/oembed/'),
(520, 100, 'view-time', 1470390864),
(521, 101, 'category', 'post-views-track'),
(522, 101, 'post-id', 7),
(523, 101, 'viewer-id', 1),
(524, 101, 'ip-address', '::1'),
(525, 101, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(526, 101, 'view-time', 1470396462),
(527, 102, 'category', 'post-views-track'),
(528, 102, 'post-id', 7),
(529, 102, 'viewer-id', 1),
(530, 102, 'ip-address', '::1'),
(531, 102, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(532, 102, 'view-time', 1470396634),
(533, 103, 'category', 'post-views-track'),
(534, 103, 'post-id', 7),
(535, 103, 'viewer-id', 1),
(536, 103, 'ip-address', '::1'),
(537, 103, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(538, 103, 'view-time', 1470396694),
(539, 104, 'category', 'post-views-track'),
(540, 104, 'post-id', 7),
(541, 104, 'viewer-id', 1),
(542, 104, 'ip-address', '::1'),
(543, 104, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(544, 104, 'view-time', 1470396723),
(545, 105, 'category', 'post-views-track'),
(546, 105, 'post-id', 7),
(547, 105, 'viewer-id', 1),
(548, 105, 'ip-address', '::1'),
(549, 105, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(550, 105, 'view-time', 1470396754),
(551, 106, 'category', 'post-views-track'),
(552, 106, 'post-id', 7),
(553, 106, 'viewer-id', 1),
(554, 106, 'ip-address', '::1'),
(555, 106, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(556, 106, 'view-time', 1470396778),
(557, 107, 'category', 'post-views-track'),
(558, 107, 'post-id', 7),
(559, 107, 'viewer-id', 1),
(560, 107, 'ip-address', '::1'),
(561, 107, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(562, 107, 'view-time', 1470396953),
(563, 108, 'category', 'post-views-track'),
(564, 108, 'post-id', 7),
(565, 108, 'viewer-id', 1),
(566, 108, 'ip-address', '::1'),
(567, 108, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(568, 108, 'view-time', 1470397070),
(569, 109, 'category', 'user-activities'),
(570, 109, 'object_id', 8),
(571, 109, 'object_type', 'post'),
(572, 109, 'subject_id', 1),
(573, 109, 'subject_action', 'create'),
(574, 109, 'time_created', 1470397158),
(575, 110, 'category', 'user-notifications'),
(576, 110, 'subscriber_id', 1),
(577, 110, 'activity_id', 109),
(578, 110, 'status', 'seen'),
(579, 111, 'category', 'post-views-track'),
(580, 111, 'post-id', 7),
(581, 111, 'viewer-id', 1),
(582, 111, 'ip-address', '::1'),
(583, 111, 'referrer', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals'),
(584, 111, 'view-time', 1470397161),
(585, 112, 'category', 'user-activities'),
(586, 112, 'object_id', 9),
(587, 112, 'object_type', 'post'),
(588, 112, 'subject_id', 1),
(589, 112, 'subject_action', 'create'),
(590, 112, 'time_created', 1470397561),
(591, 113, 'category', 'user-notifications'),
(592, 113, 'subscriber_id', 1),
(593, 113, 'activity_id', 112),
(594, 113, 'status', 'seen'),
(595, 114, 'category', 'post-views-track'),
(596, 114, 'post-id', 7),
(597, 114, 'viewer-id', 1),
(598, 114, 'ip-address', '::1'),
(599, 114, 'referrer', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals'),
(600, 114, 'view-time', 1470397565),
(601, 115, 'category', 'user_profile_viewers'),
(602, 115, 'user_id', 1),
(603, 115, 'viewer_id', 1),
(604, 116, 'category', 'user_profile_viewers'),
(605, 116, 'user_id', 1),
(606, 116, 'viewer_id', 1),
(607, 117, 'category', 'user_profile_viewers'),
(608, 117, 'user_id', 1),
(609, 117, 'viewer_id', 1),
(610, 118, 'category', 'user_profile_viewers'),
(611, 118, 'user_id', 1),
(612, 118, 'viewer_id', 1),
(613, 119, 'category', 'user_profile_viewers'),
(614, 119, 'user_id', 1),
(615, 119, 'viewer_id', 1),
(616, 120, 'category', 'user_profile_viewers'),
(617, 120, 'user_id', 1),
(618, 120, 'viewer_id', 1),
(619, 121, 'category', 'user_profile_viewers'),
(620, 121, 'user_id', 1),
(621, 121, 'viewer_id', 1),
(622, 122, 'category', 'user_profile_viewers'),
(623, 122, 'user_id', 1),
(624, 122, 'viewer_id', 1),
(625, 123, 'category', 'user_profile_viewers'),
(626, 123, 'user_id', 1),
(627, 123, 'viewer_id', 1),
(628, 124, 'category', 'user_profile_viewers'),
(629, 124, 'user_id', 1),
(630, 124, 'viewer_id', 1),
(631, 125, 'category', 'user_profile_viewers'),
(632, 125, 'user_id', 1),
(633, 125, 'viewer_id', 1),
(634, 126, 'category', 'user_profile_viewers'),
(635, 126, 'user_id', 1),
(636, 126, 'viewer_id', 1),
(637, 127, 'category', 'post-views-track'),
(638, 127, 'post-id', 7),
(639, 127, 'viewer-id', 1),
(640, 127, 'ip-address', '::1'),
(641, 127, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(642, 127, 'view-time', 1470417469),
(643, 128, 'category', 'post-views-track'),
(644, 128, 'post-id', 7),
(645, 128, 'viewer-id', 1),
(646, 128, 'ip-address', '::1'),
(647, 128, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(648, 128, 'view-time', 1470417860),
(649, 129, 'category', 'post-views-track'),
(650, 129, 'post-id', 7),
(651, 129, 'viewer-id', 1),
(652, 129, 'ip-address', '::1'),
(653, 129, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(654, 129, 'view-time', 1470417918),
(655, 130, 'category', 'post-views-track'),
(656, 130, 'post-id', 7),
(657, 130, 'viewer-id', 1),
(658, 130, 'ip-address', '::1'),
(659, 130, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(660, 130, 'view-time', 1470417969),
(661, 131, 'category', 'user_profile_viewers'),
(662, 131, 'user_id', 1),
(663, 131, 'viewer_id', 1),
(664, 132, 'category', 'user_profile_viewers'),
(665, 132, 'user_id', 1),
(666, 132, 'viewer_id', 1),
(667, 133, 'category', 'post-views-track'),
(668, 133, 'post-id', 6),
(669, 133, 'viewer-id', 1),
(670, 133, 'ip-address', '::1'),
(671, 133, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(672, 133, 'view-time', 1470485708),
(673, 134, 'category', 'post-views-track'),
(674, 134, 'post-id', 6),
(675, 134, 'ip-address', '::1'),
(676, 134, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(677, 134, 'view-time', 1470639936),
(678, 135, 'category', 'post-views-track'),
(679, 135, 'post-id', 6),
(680, 135, 'ip-address', '::1'),
(681, 135, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(682, 135, 'view-time', 1470639974),
(683, 136, 'category', 'post-views-track'),
(684, 136, 'post-id', 6),
(685, 136, 'ip-address', '::1'),
(686, 136, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(687, 136, 'view-time', 1470639983),
(688, 137, 'category', 'post-views-track'),
(689, 137, 'post-id', 1),
(690, 137, 'ip-address', '::1'),
(691, 137, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(692, 137, 'view-time', 1470640049),
(693, 138, 'category', 'post-views-track'),
(694, 138, 'post-id', 1),
(695, 138, 'ip-address', '::1'),
(696, 138, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(697, 138, 'view-time', 1470641127),
(698, 139, 'category', 'post-views-track'),
(699, 139, 'post-id', 1),
(700, 139, 'ip-address', '::1'),
(701, 139, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(702, 139, 'view-time', 1470641284),
(703, 140, 'category', 'post-views-track'),
(704, 140, 'post-id', 1),
(705, 140, 'ip-address', '::1'),
(706, 140, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(707, 140, 'view-time', 1470641656),
(708, 141, 'category', 'post-views-track'),
(709, 141, 'post-id', 1),
(710, 141, 'ip-address', '::1'),
(711, 141, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(712, 141, 'view-time', 1470641711),
(713, 142, 'category', 'post-views-track'),
(714, 142, 'post-id', 1),
(715, 142, 'ip-address', '::1'),
(716, 142, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(717, 142, 'view-time', 1470641786),
(718, 143, 'category', 'post-views-track'),
(719, 143, 'post-id', 1),
(720, 143, 'ip-address', '::1'),
(721, 143, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(722, 143, 'view-time', 1470641910),
(723, 144, 'category', 'post-views-track'),
(724, 144, 'post-id', 1),
(725, 144, 'ip-address', '::1'),
(726, 144, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(727, 144, 'view-time', 1470641933),
(728, 145, 'category', 'post-views-track'),
(729, 145, 'post-id', 1),
(730, 145, 'ip-address', '::1'),
(731, 145, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(732, 145, 'view-time', 1470641968),
(733, 146, 'category', 'post-views-track'),
(734, 146, 'post-id', 1),
(735, 146, 'ip-address', '::1'),
(736, 146, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(737, 146, 'view-time', 1470642099),
(738, 147, 'category', 'post-views-track'),
(739, 147, 'post-id', 1),
(740, 147, 'ip-address', '::1'),
(741, 147, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(742, 147, 'view-time', 1470642269),
(743, 148, 'category', 'post-views-track'),
(744, 148, 'post-id', 1),
(745, 148, 'ip-address', '::1'),
(746, 148, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(747, 148, 'view-time', 1470642351),
(748, 149, 'category', 'post-views-track'),
(749, 149, 'post-id', 1),
(750, 149, 'ip-address', '::1'),
(751, 149, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(752, 149, 'view-time', 1470642446),
(753, 150, 'category', 'post-views-track'),
(754, 150, 'post-id', 1),
(755, 150, 'ip-address', '::1'),
(756, 150, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(757, 150, 'view-time', 1470642486),
(758, 151, 'category', 'post-views-track'),
(759, 151, 'post-id', 7),
(760, 151, 'ip-address', '::1'),
(761, 151, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(762, 151, 'view-time', 1470642568),
(763, 152, 'category', 'post-views-track'),
(764, 152, 'post-id', 7),
(765, 152, 'ip-address', '::1'),
(766, 152, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(767, 152, 'view-time', 1470642624),
(768, 153, 'category', 'third-party-user-login'),
(769, 153, 'auth-provider', 'linkedin'),
(770, 153, 'user-id', 2),
(771, 154, 'category', 'user_profile_viewers'),
(772, 154, 'user_id', 2),
(773, 154, 'viewer_id', 2),
(774, 155, 'category', 'third-party-user-login'),
(775, 155, 'auth-provider', 'linkedin'),
(776, 155, 'user-id', 3),
(777, 156, 'category', 'third-party-user-login'),
(778, 156, 'auth-provider', 'linkedin'),
(779, 156, 'user-id', 3),
(780, 157, 'category', 'user_profile_viewers'),
(781, 157, 'user_id', 3),
(782, 157, 'viewer_id', 3),
(783, 158, 'category', 'user_profile_viewers'),
(784, 158, 'user_id', 3),
(785, 158, 'viewer_id', 3),
(786, 159, 'category', 'user_profile_viewers'),
(787, 159, 'user_id', 3),
(788, 159, 'viewer_id', 3),
(789, 160, 'category', 'third-party-user-login'),
(790, 160, 'auth-provider', 'linkedin'),
(791, 160, 'user-id', 3),
(792, 161, 'category', 'third-party-user-login'),
(793, 161, 'auth-provider', 'linkedin'),
(794, 161, 'user-id', 3),
(795, 162, 'category', 'third-party-user-login'),
(796, 162, 'auth-provider', 'linkedin'),
(797, 162, 'user-id', 3),
(798, 163, 'category', 'third-party-user-login'),
(799, 163, 'auth-provider', 'linkedin'),
(800, 163, 'user-id', 3),
(801, 164, 'category', 'third-party-user-login'),
(802, 164, 'auth-provider', 'linkedin'),
(803, 164, 'user-id', 3),
(804, 165, 'category', 'third-party-user-login'),
(805, 165, 'auth-provider', 'google'),
(806, 165, 'user-id', 4),
(807, 166, 'category', 'third-party-user-login'),
(808, 166, 'auth-provider', 'google'),
(809, 166, 'user-id', 4),
(810, 167, 'category', 'third-party-user-login'),
(811, 167, 'auth-provider', 'google'),
(812, 167, 'user-id', 4),
(813, 168, 'category', 'third-party-user-login'),
(814, 168, 'auth-provider', 'google'),
(815, 168, 'user-id', 4),
(816, 169, 'category', 'user_profile_viewers'),
(817, 169, 'user_id', 4),
(818, 169, 'viewer_id', 4),
(819, 170, 'category', 'third-party-user-login'),
(820, 170, 'auth-provider', 'google'),
(821, 170, 'user-id', 5),
(822, 171, 'category', 'user_profile_viewers'),
(823, 171, 'user_id', 5),
(824, 171, 'viewer_id', 5),
(825, 172, 'category', 'third-party-user-login'),
(826, 172, 'auth-provider', 'twitter'),
(827, 172, 'user-id', 6),
(828, 173, 'category', 'user_profile_viewers'),
(829, 173, 'user_id', 6),
(830, 173, 'viewer_id', 6),
(831, 174, 'category', 'third-party-user-login'),
(832, 174, 'auth-provider', 'twitter'),
(833, 174, 'user-id', 6),
(834, 175, 'category', 'user_profile_viewers'),
(835, 175, 'user_id', 6),
(836, 175, 'viewer_id', 6),
(837, 176, 'category', 'post-views-track'),
(838, 176, 'post-id', 6),
(839, 176, 'ip-address', '::1'),
(840, 176, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(841, 176, 'view-time', 1471190150),
(842, 177, 'category', 'post-views-track'),
(843, 177, 'post-id', 6),
(844, 177, 'ip-address', '::1'),
(845, 177, 'view-time', 1471190242),
(846, 178, 'category', 'post-views-track'),
(847, 178, 'post-id', 6),
(848, 178, 'ip-address', '::1'),
(849, 178, 'view-time', 1471190253),
(850, 179, 'category', 'post-views-track'),
(851, 179, 'post-id', 7),
(852, 179, 'ip-address', '::1'),
(853, 179, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(854, 179, 'view-time', 1471255719),
(855, 180, 'category', 'post-views-track'),
(856, 180, 'post-id', 7),
(857, 180, 'ip-address', '::1'),
(858, 180, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(859, 180, 'view-time', 1471255797),
(860, 181, 'category', 'post-views-track'),
(861, 181, 'post-id', 7),
(862, 181, 'ip-address', '::1'),
(863, 181, 'view-time', 1471261618),
(864, 182, 'category', 'post-views-track'),
(865, 182, 'post-id', 4),
(866, 182, 'ip-address', '::1'),
(867, 182, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(868, 182, 'view-time', 1471380955),
(869, 183, 'category', 'post-views-track'),
(870, 183, 'post-id', 7),
(871, 183, 'ip-address', '::1'),
(872, 183, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(873, 183, 'view-time', 1471644227),
(874, 184, 'category', 'post-views-track'),
(875, 184, 'post-id', 7),
(876, 184, 'viewer-id', 1),
(877, 184, 'ip-address', '::1'),
(878, 184, 'referrer', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals'),
(879, 184, 'view-time', 1471644252),
(880, 185, 'category', 'post-views-track'),
(881, 185, 'post-id', 7),
(882, 185, 'viewer-id', 1),
(883, 185, 'ip-address', '::1'),
(884, 185, 'referrer', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals'),
(885, 185, 'view-time', 1471644278),
(886, 186, 'category', 'user-activities'),
(887, 186, 'object_id', 9),
(888, 186, 'object_type', 'post'),
(889, 186, 'subject_id', 1),
(890, 186, 'subject_action', 'create'),
(891, 186, 'time_created', 1471644319),
(892, 187, 'category', 'user-notifications'),
(893, 187, 'subscriber_id', 1),
(894, 187, 'activity_id', 186),
(895, 187, 'status', 'seen'),
(896, 188, 'category', 'post-views-track'),
(897, 188, 'post-id', 7),
(898, 188, 'viewer-id', 1),
(899, 188, 'ip-address', '::1'),
(900, 188, 'referrer', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals'),
(901, 188, 'view-time', 1471644323),
(902, 189, 'category', 'user-activities'),
(903, 189, 'object_id', 10),
(904, 189, 'object_type', 'post'),
(905, 189, 'subject_id', 1),
(906, 189, 'subject_action', 'create'),
(907, 189, 'time_created', 1471644423),
(908, 190, 'category', 'user-notifications'),
(909, 190, 'subscriber_id', 1),
(910, 190, 'activity_id', 189),
(911, 190, 'status', 'seen'),
(912, 191, 'category', 'post-views-track'),
(913, 191, 'post-id', 7),
(914, 191, 'viewer-id', 1),
(915, 191, 'ip-address', '::1'),
(916, 191, 'referrer', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals'),
(917, 191, 'view-time', 1471644428),
(918, 192, 'category', 'post-views-track'),
(919, 192, 'post-id', 6),
(920, 192, 'viewer-id', 1),
(921, 192, 'ip-address', '::1'),
(922, 192, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(923, 192, 'view-time', 1471644525),
(924, 193, 'category', 'user-activities'),
(925, 193, 'object_id', 11),
(926, 193, 'object_type', 'post'),
(927, 193, 'subject_id', 1),
(928, 193, 'subject_action', 'create'),
(929, 193, 'time_created', 1471644551),
(930, 194, 'category', 'user-notifications'),
(931, 194, 'subscriber_id', 1),
(932, 194, 'activity_id', 193),
(933, 194, 'status', 'seen'),
(934, 195, 'category', 'post-views-track'),
(935, 195, 'post-id', 6),
(936, 195, 'viewer-id', 1),
(937, 195, 'ip-address', '::1'),
(938, 195, 'referrer', 'http://localhost/sites/zamaju-forums/posts/6/with-daily-motion-video'),
(939, 195, 'view-time', 1471644556),
(940, 196, 'category', 'post-views-track'),
(941, 196, 'post-id', 7),
(942, 196, 'viewer-id', 1),
(943, 196, 'ip-address', '::1'),
(944, 196, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(945, 196, 'view-time', 1471711741),
(946, 197, 'category', 'post-views-track'),
(947, 197, 'post-id', 7),
(948, 197, 'viewer-id', 1),
(949, 197, 'ip-address', '::1'),
(950, 197, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(951, 197, 'view-time', 1471712067),
(952, 198, 'category', 'post-views-track'),
(953, 198, 'post-id', 7),
(954, 198, 'viewer-id', 1),
(955, 198, 'ip-address', '::1'),
(956, 198, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(957, 198, 'view-time', 1471712081),
(958, 199, 'category', 'post-views-track'),
(959, 199, 'post-id', 7),
(960, 199, 'ip-address', '::1'),
(961, 199, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(962, 199, 'view-time', 1471712100),
(963, 200, 'category', 'post-views-track'),
(964, 200, 'post-id', 7),
(965, 200, 'ip-address', '::1'),
(966, 200, 'view-time', 1471712161),
(967, 201, 'category', 'post-views-track'),
(968, 201, 'post-id', 7),
(969, 201, 'ip-address', '::1'),
(970, 201, 'view-time', 1471712199),
(971, 202, 'category', 'post-views-track'),
(972, 202, 'post-id', 7),
(973, 202, 'ip-address', '::1'),
(974, 202, 'view-time', 1471712439),
(975, 203, 'category', 'post-views-track'),
(976, 203, 'post-id', 6),
(977, 203, 'ip-address', '::1'),
(978, 203, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(979, 203, 'view-time', 1471712470),
(980, 204, 'category', 'post-views-track'),
(981, 204, 'post-id', 6),
(982, 204, 'ip-address', '::1'),
(983, 204, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(984, 204, 'view-time', 1471712839),
(985, 205, 'category', 'post-views-track'),
(986, 205, 'post-id', 6),
(987, 205, 'ip-address', '::1'),
(988, 205, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(989, 205, 'view-time', 1471712931),
(990, 206, 'category', 'post-views-track'),
(991, 206, 'post-id', 6),
(992, 206, 'ip-address', '::1'),
(993, 206, 'referrer', 'http://localhost/sites/zamaju-forums/'),
(994, 206, 'view-time', 1471712933),
(995, 207, 'category', 'post-views-track'),
(996, 207, 'post-id', 6),
(997, 207, 'viewer-id', 1),
(998, 207, 'ip-address', '::1'),
(999, 207, 'referrer', 'http://localhost/sites/zamaju-forums/posts/6/with-daily-motion-video'),
(1000, 207, 'view-time', 1471713031),
(1001, 208, 'category', 'user_profile_viewers'),
(1002, 208, 'user_id', 1),
(1003, 208, 'viewer_id', 1),
(1004, 209, 'name', 'Create Tags'),
(1005, 209, 'category', 'available-user-capabilities'),
(1006, 209, 'creator_id', 1),
(1007, 209, 'enabled-for-all-users', 'yes'),
(1008, 209, 'enabled-for-all-admins', 'no');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_items`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_items`
--

INSERT INTO `zf_items` (`id`, `date_added`) VALUES
(1, '2016-07-30 12:21:41'),
(2, '2016-07-30 12:21:41'),
(3, '2016-07-30 12:21:41'),
(4, '2016-07-30 12:21:42'),
(5, '2016-07-30 12:21:42'),
(6, '2016-07-30 12:21:42'),
(7, '2016-07-30 12:21:42'),
(8, '2016-07-30 12:21:43'),
(9, '2016-07-30 12:21:43'),
(10, '2016-07-30 12:21:43'),
(11, '2016-07-30 12:21:43'),
(12, '2016-07-30 12:21:44'),
(13, '2016-07-30 12:21:44'),
(14, '2016-07-30 12:21:44'),
(15, '2016-07-30 12:21:44'),
(16, '2016-07-31 13:11:31'),
(17, '2016-07-31 13:11:45'),
(18, '2016-07-31 13:14:18'),
(19, '2016-07-31 13:25:13'),
(20, '2016-07-31 22:31:22'),
(21, '2016-07-31 23:52:45'),
(22, '2016-08-02 09:04:19'),
(23, '2016-08-03 15:13:15'),
(24, '2016-08-03 15:17:10'),
(25, '2016-08-03 16:39:08'),
(26, '2016-08-03 16:42:23'),
(27, '2016-08-03 16:43:30'),
(28, '2016-08-03 16:45:44'),
(29, '2016-08-03 16:48:09'),
(30, '2016-08-03 16:50:43'),
(31, '2016-08-03 19:46:59'),
(32, '2016-08-03 19:55:59'),
(33, '2016-08-03 19:56:16'),
(34, '2016-08-03 20:01:27'),
(35, '2016-08-03 20:01:39'),
(36, '2016-08-03 20:04:18'),
(37, '2016-08-03 21:11:44'),
(38, '2016-08-03 21:12:27'),
(39, '2016-08-03 21:19:28'),
(40, '2016-08-03 22:37:54'),
(41, '2016-08-03 22:38:07'),
(42, '2016-08-03 22:40:15'),
(43, '2016-08-03 22:40:56'),
(44, '2016-08-03 22:42:33'),
(45, '2016-08-03 22:44:36'),
(46, '2016-08-03 22:49:40'),
(47, '2016-08-03 22:50:44'),
(48, '2016-08-03 22:54:19'),
(49, '2016-08-03 22:56:54'),
(50, '2016-08-03 22:58:22'),
(51, '2016-08-03 22:59:02'),
(52, '2016-08-03 23:02:06'),
(53, '2016-08-03 23:02:51'),
(54, '2016-08-03 23:04:41'),
(55, '2016-08-03 23:06:11'),
(56, '2016-08-03 23:07:36'),
(57, '2016-08-03 23:12:42'),
(58, '2016-08-03 23:17:37'),
(59, '2016-08-03 23:22:43'),
(60, '2016-08-03 23:30:10'),
(61, '2016-08-03 23:32:04'),
(62, '2016-08-03 23:40:19'),
(63, '2016-08-03 23:45:04'),
(64, '2016-08-03 23:49:10'),
(65, '2016-08-03 23:49:21'),
(66, '2016-08-04 00:25:04'),
(67, '2016-08-04 00:25:24'),
(68, '2016-08-04 00:26:57'),
(69, '2016-08-04 00:28:51'),
(70, '2016-08-04 00:29:02'),
(71, '2016-08-04 11:30:32'),
(72, '2016-08-04 11:32:51'),
(73, '2016-08-04 11:33:56'),
(74, '2016-08-04 11:37:37'),
(75, '2016-08-04 11:37:53'),
(76, '2016-08-04 11:40:04'),
(77, '2016-08-04 11:46:42'),
(78, '2016-08-04 11:47:05'),
(79, '2016-08-04 11:48:15'),
(80, '2016-08-04 11:49:20'),
(81, '2016-08-04 11:49:52'),
(82, '2016-08-05 09:57:27'),
(83, '2016-08-05 09:57:48'),
(84, '2016-08-05 10:27:07'),
(85, '2016-08-05 10:27:25'),
(86, '2016-08-05 10:29:28'),
(87, '2016-08-05 10:29:50'),
(88, '2016-08-05 10:30:19'),
(89, '2016-08-05 10:30:47'),
(90, '2016-08-05 10:33:36'),
(91, '2016-08-05 10:35:06'),
(92, '2016-08-05 10:38:04'),
(93, '2016-08-05 10:39:45'),
(94, '2016-08-05 10:40:54'),
(95, '2016-08-05 10:43:33'),
(96, '2016-08-05 10:48:05'),
(97, '2016-08-05 10:49:23'),
(98, '2016-08-05 10:53:16'),
(99, '2016-08-05 10:54:07'),
(100, '2016-08-05 10:54:24'),
(101, '2016-08-05 12:27:43'),
(102, '2016-08-05 12:30:34'),
(103, '2016-08-05 12:31:34'),
(104, '2016-08-05 12:32:03'),
(105, '2016-08-05 12:32:34'),
(106, '2016-08-05 12:32:58'),
(107, '2016-08-05 12:35:53'),
(108, '2016-08-05 12:37:50'),
(109, '2016-08-05 12:39:18'),
(110, '2016-08-05 12:39:19'),
(111, '2016-08-05 12:39:21'),
(112, '2016-08-05 12:46:01'),
(113, '2016-08-05 12:46:03'),
(114, '2016-08-05 12:46:05'),
(115, '2016-08-05 16:39:20'),
(116, '2016-08-05 16:41:39'),
(117, '2016-08-05 16:42:14'),
(118, '2016-08-05 16:42:44'),
(119, '2016-08-05 16:43:22'),
(120, '2016-08-05 16:43:45'),
(121, '2016-08-05 16:45:24'),
(122, '2016-08-05 16:54:08'),
(123, '2016-08-05 16:54:37'),
(124, '2016-08-05 17:29:01'),
(125, '2016-08-05 17:39:33'),
(126, '2016-08-05 17:40:53'),
(127, '2016-08-05 18:17:49'),
(128, '2016-08-05 18:24:20'),
(129, '2016-08-05 18:25:19'),
(130, '2016-08-05 18:26:09'),
(131, '2016-08-05 18:34:50'),
(132, '2016-08-05 18:35:08'),
(133, '2016-08-06 13:15:08'),
(134, '2016-08-08 08:05:36'),
(135, '2016-08-08 08:06:14'),
(136, '2016-08-08 08:06:24'),
(137, '2016-08-08 08:07:30'),
(138, '2016-08-08 08:25:27'),
(139, '2016-08-08 08:28:04'),
(140, '2016-08-08 08:34:16'),
(141, '2016-08-08 08:35:11'),
(142, '2016-08-08 08:36:26'),
(143, '2016-08-08 08:38:30'),
(144, '2016-08-08 08:38:53'),
(145, '2016-08-08 08:39:28'),
(146, '2016-08-08 08:41:39'),
(147, '2016-08-08 08:44:30'),
(148, '2016-08-08 08:45:51'),
(149, '2016-08-08 08:47:27'),
(150, '2016-08-08 08:48:06'),
(151, '2016-08-08 08:49:28'),
(152, '2016-08-08 08:50:24'),
(153, '2016-08-13 12:10:40'),
(154, '2016-08-13 12:12:18'),
(155, '2016-08-13 12:30:34'),
(156, '2016-08-13 12:32:14'),
(157, '2016-08-13 12:46:52'),
(158, '2016-08-13 12:48:53'),
(159, '2016-08-13 12:49:12'),
(160, '2016-08-13 12:49:49'),
(161, '2016-08-13 12:52:10'),
(162, '2016-08-13 12:53:28'),
(163, '2016-08-13 12:54:22'),
(164, '2016-08-13 13:10:04'),
(165, '2016-08-13 20:45:22'),
(166, '2016-08-13 20:47:22'),
(167, '2016-08-13 21:03:48'),
(168, '2016-08-13 21:04:57'),
(169, '2016-08-13 21:05:13'),
(170, '2016-08-13 21:07:33'),
(171, '2016-08-13 21:07:45'),
(172, '2016-08-14 10:25:01'),
(173, '2016-08-14 10:27:38'),
(174, '2016-08-14 10:29:28'),
(175, '2016-08-14 10:29:38'),
(176, '2016-08-14 16:55:50'),
(177, '2016-08-14 16:57:22'),
(178, '2016-08-14 16:57:33'),
(179, '2016-08-15 11:08:40'),
(180, '2016-08-15 11:09:57'),
(181, '2016-08-15 12:46:58'),
(182, '2016-08-16 21:55:55'),
(183, '2016-08-19 23:03:47'),
(184, '2016-08-19 23:04:12'),
(185, '2016-08-19 23:04:38'),
(186, '2016-08-19 23:05:19'),
(187, '2016-08-19 23:05:21'),
(188, '2016-08-19 23:05:24'),
(189, '2016-08-19 23:07:03'),
(190, '2016-08-19 23:07:05'),
(191, '2016-08-19 23:07:08'),
(192, '2016-08-19 23:08:45'),
(193, '2016-08-19 23:09:11'),
(194, '2016-08-19 23:09:13'),
(195, '2016-08-19 23:09:16'),
(196, '2016-08-20 17:49:01'),
(197, '2016-08-20 17:54:27'),
(198, '2016-08-20 17:54:41'),
(199, '2016-08-20 17:55:00'),
(200, '2016-08-20 17:56:01'),
(201, '2016-08-20 17:56:39'),
(202, '2016-08-20 18:00:39'),
(203, '2016-08-20 18:01:10'),
(204, '2016-08-20 18:07:19'),
(205, '2016-08-20 18:08:51'),
(206, '2016-08-20 18:08:54'),
(207, '2016-08-20 18:10:32'),
(208, '2016-08-20 18:27:15'),
(209, '2016-08-21 08:56:34');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_login_attempts`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_login_attempts` (
  `ipaddress` varchar(50) DEFAULT NULL,
  `attempts` int(10) NOT NULL DEFAULT '0',
  `lastlogin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_login_attempts`
--

INSERT INTO `zf_login_attempts` (`ipaddress`, `attempts`, `lastlogin`) VALUES
('::1', 0, '2016-08-02 09:05:36');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_post_views`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_post_views` (
  `post_id` int(11) NOT NULL,
  `viewer_id` int(11) NOT NULL,
  `date_viewed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_post_views`
--

INSERT INTO `zf_post_views` (`post_id`, `viewer_id`, `date_viewed`) VALUES
(1, 1, '2016-07-31 13:11:45'),
(1, 0, '2016-07-31 22:31:23'),
(1, 1, '2016-07-31 23:52:46'),
(1, 0, '2016-08-02 09:04:20'),
(1, 1, '2016-08-03 15:13:16'),
(2, 1, '2016-08-03 19:56:00'),
(3, 1, '2016-08-03 20:01:40'),
(4, 1, '2016-08-03 21:12:27'),
(5, 1, '2016-08-03 23:49:21'),
(6, 1, '2016-08-04 00:25:25'),
(6, 1, '2016-08-04 11:37:53'),
(4, 1, '2016-08-04 11:49:53'),
(7, 1, '2016-08-05 09:57:49'),
(1, 1, '2016-08-05 10:27:08'),
(2, 1, '2016-08-05 10:27:25'),
(6, 1, '2016-08-05 10:53:17'),
(7, 1, '2016-08-05 18:17:50'),
(6, 1, '2016-08-06 13:15:09'),
(6, 0, '2016-08-08 08:05:37'),
(1, 0, '2016-08-08 08:07:30'),
(7, 0, '2016-08-08 08:49:29'),
(6, 0, '2016-08-14 16:55:51'),
(7, 0, '2016-08-15 10:08:40'),
(4, 0, '2016-08-16 20:55:56'),
(7, 0, '2016-08-19 22:03:48'),
(7, 1, '2016-08-19 22:04:13'),
(6, 1, '2016-08-19 22:08:46'),
(7, 1, '2016-08-20 16:49:02'),
(7, 0, '2016-08-20 16:55:01'),
(6, 0, '2016-08-20 17:01:11'),
(6, 1, '2016-08-20 17:10:33');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_posts`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `author_id` int(11) NOT NULL,
  `title` text,
  `content` longtext,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_posts`
--

INSERT INTO `zf_posts` (`id`, `parent_id`, `author_id`, `title`, `content`, `date_added`) VALUES
(1, 0, 1, 'This is the first post', '', '2016-07-31 13:11:30'),
(2, 0, 1, 'First test of auto-embed', '<p><a href="https://en.wikipedia.org/wiki/The_Lego_Movie"><img src="https://upload.wikimedia.org/wikipedia/en/thumb/1/10/The_Lego_Movie_poster.jpg/220px-The_Lego_Movie_poster.jpg" alt="The Lego Movie - Wikipedia, the free encyclopedia" width="220" height="326"></a>&nbsp;&nbsp;</p>\n<p>This is my first test of auto-inline embed</p>', '2016-08-03 19:46:59'),
(3, 0, 1, 'This is a youtube video', '<p><iframe class="embedly-embed" src="//cdn.embedly.com/widgets/media.html?src=https%3A%2F%2Fwww.youtube.com%2Fembed%2FdMH0bHeiRNg%3Ffeature%3Doembed&amp;url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DdMH0bHeiRNg&amp;image=https%3A%2F%2Fi.ytimg.com%2Fvi%2FdMH0bHeiRNg%2Fhqdefault.jpg&amp;key=3122acb5e4914d619607098da5a92b1d&amp;type=text%2Fhtml&amp;schema=youtube" width="640" height="480" scrolling="no" frameborder="0" allowfullscreen=""></iframe>&nbsp;&nbsp;</p>', '2016-08-03 20:01:26'),
(4, 0, 1, 'Testing All', '<p>&nbsp;</p>\n<p></p><div class="inline-url-embed-content video-embed-content YouTube-embed-content"><div class="provider-info"><a href="http://www.youtube.com/watch?v=dMH0bHeiRNg" rel="external" target="_blank">YouTube</a></div><div class="resource-url"><a href="http://www.youtube.com/watch?v=dMH0bHeiRNg" rel="external" target="_blank">Evolution of Dance</a></div><img class="resource-image" src="https://i.ytimg.com/vi/dMH0bHeiRNg/hqdefault.jpg" alt="Evolution of Dance" width="480" height="360"><div class="resource-description">If you enjoyed this video please subscribe!</div></div><iframe class="embedly-embed" src="//cdn.embedly.com/widgets/media.html?src=https%3A%2F%2Fwww.youtube.com%2Fembed%2FdMH0bHeiRNg%3Ffeature%3Doembed&amp;url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DdMH0bHeiRNg&amp;image=https%3A%2F%2Fi.ytimg.com%2Fvi%2FdMH0bHeiRNg%2Fhqdefault.jpg&amp;key=3122acb5e4914d619607098da5a92b1d&amp;type=text%2Fhtml&amp;schema=youtube" width="640" height="480" scrolling="no" frameborder="0" allowfullscreen=""></iframe>&nbsp;<p></p>\n<p></p><div class="inline-url-embed-content link-embed-content Wikipedia-embed-content"><div class="provider-info"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">Wikipedia</a></div><div class="resource-url"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">The Lego Movie - Wikipedia, the free encyclopedia</a></div><img class="resource-image" src="https://upload.wikimedia.org/wikipedia/en/thumb/1/10/The_Lego_Movie_poster.jpg/220px-The_Lego_Movie_poster.jpg" alt="The Lego Movie - Wikipedia, the free encyclopedia" width="220" height="326"><div class="resource-description">The Lego Movie (stylized as The LEGO Movie ) is a 2014 3D computer-animated adventure- comedy film directed and written by Phil Lord and Christopher Miller, from a story by Dan and Kevin Hageman, as well as Lord and Miller, and produced by Dan Lin and Roy Lee.</div></div>&nbsp;&nbsp;<p></p>\n<p></p><div class="inline-url-embed-content link-embed-content Flickr-embed-content"><div class="provider-info"><a href="https://www.flickr.com/photos/bees/2341623661/" rel="external" target="_blank">Flickr</a></div><div class="resource-url"><a href="https://www.flickr.com/photos/bees/2341623661/" rel="external" target="_blank">ZB8T0193</a></div><img class="resource-image" src="https://c2.staticflickr.com/4/3123/2341623661_7c99f48bbf_b.jpg" alt="ZB8T0193" width="1024" height="683"><div class="resource-description">Explore ‮‭‬bees‬''s photos on Flickr. ‮‭‬bees‬ has uploaded 10229 photos to Flickr.</div></div>&nbsp;&nbsp;<p></p>', '2016-08-03 21:11:44'),
(5, 0, 1, 'Ok na', '<p></p><aside class="inline-url-embed-content video-embed-content YouTube-embed-content"><div class="provider-info"><a href="http://www.youtube.com/watch?v=dMH0bHeiRNg" rel="external" target="_blank">www.youtube.com</a></div><div class="resource-url"><a href="http://www.youtube.com/watch?v=dMH0bHeiRNg" rel="external" target="_blank">Evolution of Dance</a></div><img class="resource-image" src="https://i.ytimg.com/vi/dMH0bHeiRNg/hqdefault.jpg" alt="Evolution of Dance" width="480" height="360"><div class="resource-description">If you enjoyed this video please subscribe!</div><div class="resource-clear"></div><div class="resource-media"><iframe class="embedly-embed" src="//cdn.embedly.com/widgets/media.html?src=https%3A%2F%2Fwww.youtube.com%2Fembed%2FdMH0bHeiRNg%3Ffeature%3Doembed&amp;url=http%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DdMH0bHeiRNg&amp;image=https%3A%2F%2Fi.ytimg.com%2Fvi%2FdMH0bHeiRNg%2Fhqdefault.jpg&amp;key=3122acb5e4914d619607098da5a92b1d&amp;type=text%2Fhtml&amp;schema=youtube" width="640" height="480" scrolling="no" frameborder="0" allowfullscreen=""></iframe></div></aside>&nbsp;<p></p>\n<p>why is you tube doing this?</p>\n<p></p><aside class="inline-url-embed-content link-embed-content Wikipedia-embed-content"><div class="provider-info"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">en.wikipedia.org</a></div><div class="resource-url"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">The Lego Movie - Wikipedia, the free encyclopedia</a></div><img class="resource-image" src="https://upload.wikimedia.org/wikipedia/en/thumb/1/10/The_Lego_Movie_poster.jpg/220px-The_Lego_Movie_poster.jpg" alt="The Lego Movie - Wikipedia, the free encyclopedia" width="220" height="326"><div class="resource-description">The Lego Movie (stylized as The LEGO Movie ) is a 2014 3D computer-animated adventure- comedy film directed and written by Phil Lord and Christopher Miller, from a story by Dan and Kevin Hageman, as well as Lord and Miller, and produced by Dan Lin and Roy Lee.</div><div class="resource-clear"></div></aside>&nbsp;&nbsp;<p></p>\n<p></p><aside class="inline-url-embed-content link-embed-content Flickr-embed-content"><div class="provider-info"><a href="https://www.flickr.com/photos/bees/2341623661/" rel="external" target="_blank">www.flickr.com</a></div><div class="resource-url"><a href="https://www.flickr.com/photos/bees/2341623661/" rel="external" target="_blank">ZB8T0193</a></div><img class="resource-image" src="https://c2.staticflickr.com/4/3123/2341623661_7c99f48bbf_b.jpg" alt="ZB8T0193" width="1024" height="683"><div class="resource-description">Explore ‮‭‬bees‬''s photos on Flickr. ‮‭‬bees‬ has uploaded 10229 photos to Flickr.</div><div class="resource-clear"></div></aside>&nbsp;<p></p>', '2016-08-03 23:49:09'),
(6, 0, 1, 'With daily motion video', '<p></p><div class="resource-media"><iframe class="embedly-embed" src="//cdn.embedly.com/widgets/media.html?src=http%3A%2F%2Fwww.dailymotion.com%2Fembed%2Fvideo%2Fxxwxe1&amp;src_secure=1&amp;url=http%3A%2F%2Fwww.dailymotion.com%2Fvideo%2Fxxwxe1_harlem-shake-de-los-simpsons_fun%3FGK_FACEBOOK_OG_HTML5%3D1&amp;image=http%3A%2F%2Fs2.dmcdn.net%2FBLYzs%2Fx240-mSr.jpg&amp;key=3122acb5e4914d619607098da5a92b1d&amp;type=text%2Fhtml&amp;schema=dailymotion" width="480" height="269" scrolling="no" frameborder="0" allowfullscreen=""></iframe></div>&nbsp;<p></p>\n<p></p><aside class="inline-url-embed-content link-embed-content Wikipedia-embed-content"><div class="provider-info"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">en.wikipedia.org</a></div><div class="resource-url"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">The Lego Movie - Wikipedia, the free encyclopedia</a></div><img class="resource-image" src="https://upload.wikimedia.org/wikipedia/en/thumb/1/10/The_Lego_Movie_poster.jpg/220px-The_Lego_Movie_poster.jpg" alt="The Lego Movie - Wikipedia, the free encyclopedia" width="220" height="326"><div class="resource-description">The Lego Movie (stylized as The LEGO Movie ) is a 2014 3D computer-animated adventure- comedy film directed and written by Phil Lord and Christopher Miller, from a story by Dan and Kevin Hageman, as well as Lord and Miller, and produced by Dan Lin and Roy Lee.</div><div class="resource-clear"></div></aside>&nbsp;<p></p>', '2016-08-04 00:25:04'),
(7, 0, 1, 'UAE banks possibly blacklisting Nigerian nationals...', '', '2016-08-05 09:57:26'),
(8, 7, 1, '', '<p>This is a reply to test the notification counter working.&nbsp;</p>\n<p></p><aside class="inline-url-embed-content link-embed-content Wikipedia-embed-content"><div class="provider-info"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">en.wikipedia.org</a></div><div class="resource-url"><a href="https://en.wikipedia.org/wiki/The_Lego_Movie" rel="external" target="_blank">The Lego Movie - Wikipedia, the free encyclopedia</a></div><img class="resource-image" src="https://upload.wikimedia.org/wikipedia/en/thumb/1/10/The_Lego_Movie_poster.jpg/220px-The_Lego_Movie_poster.jpg" alt="The Lego Movie - Wikipedia, the free encyclopedia" width="220" height="326"><div class="resource-description">The Lego Movie (stylized as The LEGO Movie ) is a 2014 3D computer-animated adventure- comedy film directed and written by Phil Lord and Christopher Miller, from a story by Dan and Kevin Hageman, as well as Lord and Miller, and produced by Dan Lin and Roy Lee.</div><div class="resource-clear"></div></aside>&nbsp;<p></p>', '2016-08-05 12:39:18'),
(9, 7, 1, '', '<p>This is a reply to test the notification function</p>', '2016-08-19 22:05:19'),
(10, 7, 1, '', '<p>Still testing the notification function</p>', '2016-08-19 22:07:03'),
(11, 6, 1, '', '<p>Still on to the notification testing level</p>', '2016-08-19 22:09:10');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_sessions`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_sessions` (
  `id` varchar(50) NOT NULL DEFAULT '',
  `data` longblob NOT NULL,
  `useragent` varchar(200) NOT NULL DEFAULT '',
  `starttime` int(11) NOT NULL DEFAULT '0',
  `lastused` int(11) NOT NULL DEFAULT '0',
  `expiry` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_sessions`
--

INSERT INTO `zf_sessions` (`id`, `data`, `useragent`, `starttime`, `lastused`, `expiry`) VALUES
('5lhj5j40lrneh7370a4vm53ce4', 'czoxNjM6Imxua2Rpbl9DU1JGX2NoZWNrX3N0YXRlfHM6MTA6IjE0MjI0MzQwMDciO2xvZ2luX2lkfGk6NTM7Y3VycmVudF91c2VyX2lkfHM6MToiMSI7Y3VycmVudF91c2VyX2xvZ2lufHM6MTg6Im9yamk0eUBob3RtYWlsLmNvbSI7Y3VycmVudF91c2VyX3Bhc3N3b3JkfHM6OToib3JqaW1la3dlIjsiOw==', 'f595da6833fc6a03ca88549e7c494116', 1471761138, 1471777628, 1471779428);



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_tag_posts`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_tag_posts` (
  `tag_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_tag_posts`
--

INSERT INTO `zf_tag_posts` (`tag_id`, `post_id`) VALUES
(17, 2),
(17, 4),
(17, 5),
(17, 6),
(18, 4),
(18, 6),
(18, 7),
(19, 2),
(20, 6),
(21, 1),
(21, 3),
(22, 2),
(22, 4),
(23, 2),
(23, 4),
(23, 6),
(25, 3),
(29, 3);



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_tags`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  `creator_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_tags`
--

INSERT INTO `zf_tags` (`id`, `name`, `description`, `creator_id`, `date_added`) VALUES
(1, 'football', '', 1, '2016-07-31 13:09:35'),
(2, 'basketball', '', 1, '2016-07-31 13:09:35'),
(3, 'baseball', '', 1, '2016-07-31 13:09:35'),
(4, 'tennis', '', 1, '2016-07-31 13:09:35'),
(5, 'table-tennis', '', 1, '2016-07-31 13:09:35'),
(6, 'english-premier-league', '', 1, '2016-07-31 13:09:35'),
(7, 'la-liga', '', 1, '2016-07-31 13:09:35'),
(8, 'ligue-1', '', 1, '2016-07-31 13:09:35'),
(9, 'bundesliga', '', 1, '2016-07-31 13:09:35'),
(10, 'serie-a', '', 1, '2016-07-31 13:09:35'),
(11, 'arsenal', '', 1, '2016-07-31 13:09:35'),
(12, 'man-u', '', 1, '2016-07-31 13:09:35'),
(13, 'everton', '', 1, '2016-07-31 13:09:35'),
(14, 'stoke-city', '', 1, '2016-07-31 13:09:35'),
(15, 'fc-barcelona', '', 1, '2016-07-31 13:09:35'),
(16, 'jquery', '', 1, '2016-07-31 13:09:35'),
(17, 'javascript', '', 1, '2016-07-31 13:09:35'),
(18, 'php', '', 1, '2016-07-31 13:09:35'),
(19, 'html', '', 1, '2016-07-31 13:09:35'),
(20, 'css', '', 1, '2016-07-31 13:09:35'),
(21, 'web-design', '', 1, '2016-07-31 13:09:35'),
(22, 'open-graph', '', 1, '2016-07-31 13:09:35'),
(23, 'oembed', '', 1, '2016-07-31 13:09:35'),
(24, 'facebook', '', 1, '2016-07-31 13:09:35'),
(25, 'twitter', '', 1, '2016-07-31 13:09:35'),
(26, 'google', '', 1, '2016-07-31 13:09:35'),
(27, 'google-plus', '', 1, '2016-07-31 13:09:35'),
(28, 'instagram', '', 1, '2016-07-31 13:09:35'),
(29, 'you-tube', '', 1, '2016-07-31 13:09:35'),
(30, 'tumblr', '', 1, '2016-07-31 13:09:36'),
(31, 'reddit', '', 1, '2016-07-31 13:09:36');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_user_logins`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_user_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `ip_address` varchar(20) DEFAULT NULL,
  `login_page` varchar(150) DEFAULT NULL,
  `login_type` int(2) DEFAULT NULL,
  `login_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_user_logins`
--

INSERT INTO `zf_user_logins` (`id`, `user_id`, `ip_address`, `login_page`, `login_type`, `login_time`) VALUES
(1, 1, '::1', 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-07-30 12:23:15'),
(2, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-07-31 07:34:08'),
(3, 1, '::1', 'http://localhost/sites/zamaju-forums/?s=1469965207', 2, '2016-07-31 12:40:07'),
(4, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-07-31 22:37:36'),
(5, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-02 09:05:49'),
(6, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-02 21:20:53'),
(7, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-03 08:55:53'),
(8, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-03 15:11:21'),
(9, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-03 18:58:10'),
(10, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-04 11:21:40'),
(11, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-04 11:33:26'),
(12, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-04 12:15:31'),
(13, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-04 23:48:51'),
(14, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-05 09:38:44'),
(15, 1, '::1', 'http://localhost/sites/zamaju-forums/?s=1470410986', 2, '2016-08-05 16:29:46'),
(16, 1, '::1', 'http://localhost/sites/zamaju-forums/?s=1470482184', 2, '2016-08-06 12:16:24'),
(17, 1, '::1', 'http://localhost/sites/zamaju-forums/?s=1470497570', 2, '2016-08-06 16:32:50'),
(18, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-07 22:12:50'),
(19, 2, '::1', 'https://www.linkedin.com/', 1, '2016-08-13 12:10:39'),
(20, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:30:34'),
(21, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:32:14'),
(22, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:49:49'),
(23, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:52:10'),
(24, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:53:28'),
(25, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:54:21'),
(26, 3, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 13:10:03'),
(27, 4, '::1', 'base_url', 1, '2016-08-13 20:45:21'),
(28, 4, '::1', 'base_url', 1, '2016-08-13 20:47:22'),
(29, 4, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 21:03:48'),
(30, 4, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 21:04:57'),
(31, 5, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 21:07:32'),
(32, 5, '::1', 'http://localhost/sites/zamaju-forums/?s=1471124205', 2, '2016-08-13 22:36:46'),
(33, 5, '::1', 'http://localhost/sites/zamaju-forums/?s=1471159816', 2, '2016-08-14 08:30:16'),
(34, 6, '::1', 'base_url', 1, '2016-08-14 10:25:01'),
(35, 6, '::1', 'base_url', 1, '2016-08-14 10:29:27'),
(36, 1, '::1', 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-08-14 12:23:36'),
(37, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-15 17:52:27'),
(38, 1, '::1', 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-08-15 19:01:13'),
(39, 1, '::1', 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-08-15 21:28:49'),
(40, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-17 18:06:05'),
(41, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-17 18:40:16'),
(42, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-17 23:07:18'),
(43, 1, '::1', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals', 1, '2016-08-19 23:04:11'),
(44, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-20 10:27:28'),
(45, 1, '::1', 'http://localhost/sites/zamaju-forums/', 2, '2016-08-20 12:35:23'),
(46, 1, '::1', 'http://localhost/sites/zamaju-forums/', 2, '2016-08-20 12:35:23'),
(47, 1, '::1', 'http://localhost/sites/zamaju-forums/?s=1471711730', 2, '2016-08-20 17:48:50'),
(48, 1, '::1', 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals', 2, '2016-08-20 17:54:23'),
(49, 1, '::1', 'http://localhost/sites/zamaju-forums/', 2, '2016-08-20 17:54:27'),
(50, 1, '::1', 'http://localhost/sites/zamaju-forums/', 2, '2016-08-20 17:54:27'),
(51, 1, '::1', 'http://localhost/sites/zamaju-forums/', 2, '2016-08-20 17:54:27'),
(52, 1, '::1', 'http://localhost/sites/zamaju-forums/posts/6/with-daily-motion-video', 1, '2016-08-20 18:10:31'),
(53, 1, '::1', 'http://localhost/sites/zamaju-forums/', 1, '2016-08-21 07:34:32');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_user_logouts`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_user_logouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_id` int(10) NOT NULL,
  `logout_page` varchar(150) DEFAULT NULL,
  `logout_type` int(2) DEFAULT NULL,
  `logout_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_user_logouts`
--

INSERT INTO `zf_user_logouts` (`id`, `login_id`, `logout_page`, `logout_type`, `logout_time`) VALUES
(1, 1, 'http://localhost/sites/zamaju-forums/logout/?s=1469877920', 1, '2016-07-30 12:25:20'),
(2, 2, '', 2, '2016-07-31 13:09:26'),
(3, 3, 'http://localhost/sites/zamaju-forums/logout/?s=1469968585', 1, '2016-07-31 13:36:25'),
(4, 4, '', 2, '2016-08-02 09:04:19'),
(5, 5, '', 2, '2016-08-02 20:59:15'),
(6, 6, '', 2, '2016-08-03 08:37:47'),
(7, 7, '', 2, '2016-08-03 15:09:44'),
(8, 7, '', 2, '2016-08-03 15:09:44'),
(9, 7, '', 2, '2016-08-03 15:09:44'),
(10, 7, '', 2, '2016-08-03 15:09:44'),
(11, 8, '', 2, '2016-08-03 18:56:19'),
(12, 9, '', 2, '2016-08-04 08:18:55'),
(13, 10, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-04 11:33:11'),
(14, 11, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-04 12:13:49'),
(15, 12, '', 2, '2016-08-04 12:58:20'),
(16, 12, '', 2, '2016-08-04 12:58:22'),
(17, 13, '', 2, '2016-08-05 07:41:45'),
(18, 14, '', 2, '2016-08-05 16:29:46'),
(19, 15, '', 2, '2016-08-06 12:16:24'),
(20, 16, '', 2, '2016-08-06 16:32:50'),
(21, 17, 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-08-06 16:34:58'),
(22, 18, '', 2, '2016-08-08 05:47:36'),
(23, 19, 'http://localhost/sites/zamaju-forums/logout/?s=1471087445', 1, '2016-08-13 12:24:05'),
(24, 21, 'http://localhost/sites/zamaju-forums/users/3/user482812/?tab=e-presence', 1, '2016-08-13 12:49:34'),
(25, 22, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:51:57'),
(26, 23, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:53:17'),
(27, 24, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:54:09'),
(28, 25, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 12:54:49'),
(29, 26, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 13:10:16'),
(30, 28, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 20:56:43'),
(31, 29, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-13 21:04:38'),
(32, 30, 'http://localhost/sites/zamaju-forums/users/4/user526274/?tab=e-presence', 1, '2016-08-13 21:05:57'),
(33, 31, '', 2, '2016-08-13 22:36:45'),
(34, 32, '', 2, '2016-08-14 08:30:16'),
(35, 33, 'http://localhost/sites/zamaju-forums/', 1, '2016-08-14 08:30:29'),
(36, 34, 'http://localhost/sites/zamaju-forums/users', 1, '2016-08-14 10:29:08'),
(37, 35, 'http://localhost/sites/zamaju-forums/users', 1, '2016-08-14 10:45:07'),
(38, 36, 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-08-14 12:27:57'),
(39, 37, '', 2, '2016-08-15 19:01:01'),
(40, 38, '', 2, '2016-08-15 21:22:41'),
(41, 39, 'http://localhost/sites/zamaju-forums/admin/', 1, '2016-08-15 23:41:30'),
(42, 40, '', 2, '2016-08-17 18:39:56'),
(43, 41, '', 2, '2016-08-17 23:06:55'),
(44, 41, '', 2, '2016-08-17 23:06:56'),
(45, 42, '', 2, '2016-08-18 11:02:52'),
(46, 42, '', 2, '2016-08-18 11:02:55'),
(47, 43, '', 2, '2016-08-20 10:10:48'),
(48, 44, '', 2, '2016-08-20 12:35:23'),
(49, 44, '', 2, '2016-08-20 12:35:23'),
(50, 46, '', 2, '2016-08-20 17:48:50'),
(51, 47, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals', 1, '2016-08-20 17:54:22'),
(52, 51, 'http://localhost/sites/zamaju-forums/posts/7/uae-banks-possibly-blacklisting-nigerian-nationals', 1, '2016-08-20 17:54:48'),
(53, 52, '', 2, '2016-08-21 07:32:18');



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_user_meta`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_user_meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_user_meta`
--

INSERT INTO `zf_user_meta` (`id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'email', 'orji4y@hotmail.com'),
(2, 1, 'role', 'Super Admin'),
(3, 1, 'username', 'user_385435'),
(4, 1, 'last-seen-time', 1471777608),
(5, 1, 'last-seen-url', 'http://localhost/sites/zamaju-forums/'),
(18, 3, 'signup-type', 'third-party-authorization'),
(19, 3, 'auth-provider', 'linkedin'),
(20, 3, 'email', 'orji4y@yahoo.com'),
(21, 3, 'firstname', 'Michael'),
(22, 3, 'lastname', 'O'),
(23, 3, 'location', 'Nigeria'),
(24, 3, 'linked-in-url', 'https://www.linkedin.com/in/michael-o-52640444'),
(25, 3, 'role', 'User'),
(26, 3, 'username', 'user_482812'),
(27, 3, 'last-seen-time', 1471090210),
(28, 3, 'last-seen-url', 'http://localhost/sites/zamaju-forums/'),
(38, 5, 'signup-type', 'third-party-authorization'),
(39, 5, 'auth-provider', 'google'),
(40, 5, 'email', 'mikkyorji@gmail.com'),
(41, 5, 'firstname', 'Michael'),
(42, 5, 'lastname', 'O'),
(43, 5, 'google-plus-url', 'https://plus.google.com/108981709459909519963'),
(44, 5, 'role', 'User'),
(45, 5, 'username', 'user_872163'),
(46, 5, 'last-seen-time', 1471159821),
(47, 5, 'last-seen-url', 'http://localhost/sites/zamaju-forums/'),
(48, 6, 'signup-type', 'third-party-authorization'),
(49, 6, 'auth-provider', 'twitter'),
(50, 6, 'email', 'Michael05907608@twitter.com'),
(51, 6, 'firstname', 'Michael'),
(52, 6, 'lastname', 'O'),
(53, 6, 'location', 'Lagos'),
(54, 6, 'twitter-url', 'https://twitter.com/Michael05907608'),
(55, 6, 'role', 'User'),
(56, 6, 'username', 'user_643644'),
(57, 6, 'last-seen-time', 1471167904),
(58, 6, 'last-seen-url', 'http://localhost/sites/zamaju-forums/users'),
(59, 1, 'profile-view-count', 1);



-- ---------------------------------------------------------
--
-- Table structure for table : `zf_users`
--
-- ---------------------------------------------------------

CREATE TABLE `zf_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(60) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `date_registered` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zf_users`
--

INSERT INTO `zf_users` (`id`, `login`, `password`, `date_registered`) VALUES
(1, 'orji4y@hotmail.com', '1fb889f5d6e72d85870c8d218a787a67', '2016-07-30 12:22:09'),
(3, 'orji4y@yahoo.com', '0ede70115dabddd730e004fd1b2358b2', '2016-08-13 12:30:32'),
(5, 'mikkyorji@gmail.com', 'd5d02d5bd3820a987a6873c11146bc7c', '2016-08-13 21:07:30'),
(6, 'Michael05907608@twitter.com', '2f8819b71f1965133be32a00a285870e', '2016-08-14 10:24:59');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;