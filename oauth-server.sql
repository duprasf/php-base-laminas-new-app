-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: test-db
-- Generation Time: Aug 29, 2023 at 09:02 PM
-- Server version: 10.6.4-MariaDB-1:10.6.4+maria~focal
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oauth`
--

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_token`
--

CREATE TABLE `oauth_access_token` (
  `accessTokenId` int(10) UNSIGNED NOT NULL,
  `access_token` text NOT NULL,
  `clientId` int(10) UNSIGNED NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_authorization_code`
--

CREATE TABLE `oauth_authorization_code` (
  `authorization_code` varchar(40) NOT NULL,
  `clientId` int(10) UNSIGNED NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL,
  `id_token` varchar(2000) DEFAULT NULL,
  `code_challenge` text NOT NULL,
  `code_challenge_method` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client`
--

CREATE TABLE `oauth_client` (
  `clientId` int(10) UNSIGNED NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `name` varchar(200) NOT NULL,
  `client_secret` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client_redirect_uri`
--

CREATE TABLE `oauth_client_redirect_uri` (
  `clientId` int(10) UNSIGNED NOT NULL,
  `redirect_uri` varchar(650) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_client_scope`
--

CREATE TABLE `oauth_client_scope` (
  `clientId` int(10) UNSIGNED NOT NULL,
  `scopeId` tinyint(3) UNSIGNED NOT NULL,
  `lang` varchar(2) NOT NULL,
  `reason` text NOT NULL,
  `added_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_token`
--

CREATE TABLE `oauth_refresh_token` (
  `refreshTokenId` int(10) UNSIGNED NOT NULL,
  `refresh_token` text NOT NULL,
  `clientId` int(10) UNSIGNED NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_scope`
--

CREATE TABLE `oauth_scope` (
  `scopeId` tinyint(3) UNSIGNED NOT NULL,
  `lang` varchar(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `clean_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `since` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `oauth_scope`
--

INSERT INTO `oauth_scope` (`scopeId`, `lang`, `name`, `clean_name`, `description`, `since`, `updated`) VALUES
(1, 'en', 'Identity', 'identity', 'Will share the username of the user', '2023-08-03 14:27:41', '2023-08-23 21:22:22'),
(1, 'fr', 'Identité', 'identity', 'Partageront l\'identifiant de l\'usager', '2023-08-03 14:27:41', '2023-08-23 21:22:30'),
(2, 'en', 'Groups', 'groups', 'Will share the groups the user belongs to', '2023-08-03 14:27:41', '2023-08-23 21:22:35'),
(2, 'fr', 'Groupes', 'groups', 'Partageront les groupes dans lesquelles l\'usager est membre', '2023-08-03 14:27:41', '2023-08-23 21:22:36'),
(3, 'en', 'Contact', 'contact', 'Will share the user\'s email, phone number, address, etc.', '2023-08-03 14:28:26', '2023-08-23 21:22:41'),
(3, 'fr', 'Contacte', 'contact', 'Patagera le courriel, numéro de téléphone, l\'adresse, etc. de l\'usager', '2023-08-03 14:28:26', '2023-08-23 21:22:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `oauth_access_token`
--
ALTER TABLE `oauth_access_token`
  ADD PRIMARY KEY (`accessTokenId`);

--
-- Indexes for table `oauth_authorization_code`
--
ALTER TABLE `oauth_authorization_code`
  ADD PRIMARY KEY (`authorization_code`);

--
-- Indexes for table `oauth_client`
--
ALTER TABLE `oauth_client`
  ADD PRIMARY KEY (`clientId`);

--
-- Indexes for table `oauth_client_redirect_uri`
--
ALTER TABLE `oauth_client_redirect_uri`
  ADD PRIMARY KEY (`clientId`,`redirect_uri`);

--
-- Indexes for table `oauth_client_scope`
--
ALTER TABLE `oauth_client_scope`
  ADD PRIMARY KEY (`clientId`,`scopeId`,`lang`) USING BTREE;

--
-- Indexes for table `oauth_refresh_token`
--
ALTER TABLE `oauth_refresh_token`
  ADD PRIMARY KEY (`refreshTokenId`);

--
-- Indexes for table `oauth_scope`
--
ALTER TABLE `oauth_scope`
  ADD PRIMARY KEY (`scopeId`,`lang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `oauth_access_token`
--
ALTER TABLE `oauth_access_token`
  MODIFY `accessTokenId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_client`
--
ALTER TABLE `oauth_client`
  MODIFY `clientId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_refresh_token`
--
ALTER TABLE `oauth_refresh_token`
  MODIFY `refreshTokenId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
