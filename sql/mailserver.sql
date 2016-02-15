SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mailserver`
--

-- --------------------------------------------------------

--
-- Table structure for table `virtual_admins`
--

CREATE TABLE `virtual_admins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `virtual_admins`
--

INSERT INTO `virtual_admins` (`id`, `user_id`, `domain_id`) VALUES
(1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `virtual_aliases`
--

CREATE TABLE `virtual_aliases` (
  `id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `source` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `virtual_domains`
--

CREATE TABLE `virtual_domains` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `virtual_domains`
--

INSERT INTO `virtual_domains` (`id`, `name`) VALUES
(1, 'example.org');

--
-- Triggers `virtual_domains`
--
DELIMITER $$
CREATE TRIGGER `virtual_domains_after_delete` AFTER DELETE ON `virtual_domains` FOR EACH ROW DELETE FROM virtual_users WHERE virtual_users.domain_id=old.id
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `virtual_domains_after_update` AFTER UPDATE ON `virtual_domains` FOR EACH ROW UPDATE virtual_users SET virtual_users.email=REPLACE(virtual_users.email,CONCAT('@',old.name),CONCAT('@',new.name)) WHERE virtual_users.email LIKE CONCAT('%',old.name)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `virtual_txt`
--

CREATE TABLE `virtual_txt` (
  `txt` varchar(256) CHARACTER SET latin1 NOT NULL,
  `en` longtext CHARACTER SET latin1 NOT NULL,
  `de` longtext CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `virtual_txt`
--

INSERT INTO `virtual_txt` (`txt`, `en`, `de`) VALUES
('txt_alias_add', 'Add alias', 'Neuen Alias'),
('txt_alias_email_addresses', 'Aliases emails', 'Aliase Emails'),
('txt_alias_source', 'Alias source', 'Alias Quelle'),
('txt_all_domanins', 'All domains', 'Alle Domains'),
('txt_all_users', 'All users', 'Alle Benutzer'),
('txt_common_information', 'Common information', 'Allgemein'),
('txt_common_information_d', 'With this interface virtual domains and the corresponding users ca be managed.<br>A user with the role of a Administrator can create, edit or delete virtual domains and their users. He also can decide about who can manage the domains in the role of a Administrator. So the administration can be done by other users.<br><br>This software is published under GPL license and comes without any warranty.', ''),
('txt_de', 'German', 'Deutsch'),
('txt_domain_add', 'Add domain', 'Neue Domain'),
('txt_domain_count', 'Domain count', 'Anzahl Domains'),
('txt_domain_list', 'Domain list', 'Domains Liste'),
('txt_empty_alias_destination', 'The alias destination cannot be empty!', 'Das Alias Ziel kann nicht leer sein!'),
('txt_empty_alias_source', 'The alias source cannot be empty!', 'Die Alias Quelle kann nicht leer sein!'),
('txt_empty_domain_name', 'The domain name cannot be empty!', 'Der Domain Name kann nicht leer sein!'),
('txt_empty_user_email', 'The user email cannot be empty!', 'Das Benutzer Email kann nich leer sein!'),
('txt_empty_user_password', 'The user password cannot be empty!', 'Das Benutzer Passwort kann nicht lehr sein!'),
('txt_en', 'English', 'English'),
('txt_existing_alias', 'The alias already exists!', 'Das Alias existiert bereits in der Datenbank!'),
('txt_existing_user_email', 'The user email already exists!', 'Das Benutzer Email existiert bereits in der Datenbank!'),
('txt_exiting_domain_name', 'The domain already exists!', 'Das Domain existiert bereits in der Datenbank!'),
('txt_invalid_alias_destination', 'The alias destination is invalid! Check the info page for details.', ''),
('txt_invalid_alias_source', 'The alias source is invalid! Check the info page for details.', ''),
('txt_invalid_domain_name', 'The domain name is invalid! Check the info page for details.', 'Domainname ist ungültig. Überprüfen Sie die Regeln für Domainnamen.'),
('txt_invalid_user_email', 'The user email is invalid! Check the info page for more details.', ''),
('txt_invalid_user_password', 'The user password is invalid! Check the info page for more details.', ''),
('txt_login_as', 'Logged in as', 'Angemeldet als'),
('txt_login_email_not_found', 'The provided email does not exist!', ''),
('txt_login_no_access', 'The selected user does not have access to this application!', ''),
('txt_login_wrong_password', 'The provided password is invalid!', ''),
('txt_managing_aliases', 'Managing virtual aliases', ''),
('txt_managing_aliases_d', 'This section is permitted to Administrator.<br><br>You can edit your user name and change your password. The corresponding email addresses will be fixed recursively.<br>You also can add, edit or delete virtual aliases. Every input is validated against the rules and changes are made recursively.', ''),
('txt_managing_domains', 'Managing virtual domains', ''),
('txt_managing_domains_d', 'The usage of this section is permitted only to Administrator.<br><br>When creating a new virtual domain, the given name is checked against the platform rules. If problems with the name appear the check can be disabled. If you disable the name check please check the correctness by yourself, otherwise the domain may not function correctly.<br><br>Allowed chars are a-z, A-Z, 0-9 and -. There have to be at least 3 chars before the dot 2-3 chars after the dot.<br><br>If a virtual domain is changed, all of the registered email addressees of this domain are changed too automatically. Also when a domain is deleted, all users are deleted too.<br>', ''),
('txt_managing_users', 'Managing virtual users', ''),
('txt_managing_users_d', 'Usage of this section is permitted to Administrator. <br><br>To wok with the user manager first you have to select a virtual domain. After selection of a domain, you will be able to manage DomainMasters and the users of the selected domain. Users can be created, edited or deleted.<br><br>Given name and passwords will be checked against the rules of this platform.<br>Allowed chars for names are a-z, A-Z, 0-9 . and -. A name has to have between 3 and 30 chars.<br>Allowed chars for passwords are a-z, A-Z, 0-9 $ and @. A password has to have between 8 and 15 chars.<br>If you create or edit a virtual user, a generated password will be given. You can overwrite this password but still matching the rules.', ''),
('txt_non_existing_alias', 'The alias does not exist!', 'Der Alias existiert nicht!'),
('txt_non_existing_alias_domain', 'The alias domain does not exist!', 'Die Alias Domain existiert nicht!'),
('txt_non_existing_domain_name', 'The domain does not exist!', 'Die Domain existiert nicht!'),
('txt_non_existing_user_domain', 'User domain does not exist!', 'Benutzer Domain existiert nicht!'),
('txt_non_existing_user_email', 'The user email does not exist!', 'Der Benutzer Email existiert nicht!'),
('txt_no_page', 'There is no page defined!', 'Es gibt keine Seiten definiert!'),
('txt_overview', 'Overview', 'Überblick'),
('txt_page_aliases', 'Aliases', 'Aliases'),
('txt_page_domains', 'Domains', 'Domains'),
('txt_page_info', 'Info', 'Info'),
('txt_page_logout', 'Sign out', 'Abmelden'),
('txt_page_users', 'Users', 'Benutzer'),
('txt_users_count', 'User count', 'Anzahl Benutzer'),
('txt_user_add', 'Add user', 'Neue Benutzer'),
('txt_user_admin', 'Admin', 'Admin'),
('txt_user_email', 'Email', 'Email'),
('txt_user_email_addresses', 'Email addresses', 'Email Adressen'),
('txt_user_password', 'Password', 'Passwort'),
('txt_version', 'Version', 'Version'),
('txt_virtual_aliases_manager', 'Virtual Aliases Manager', 'Verwaltung virtueller Aliases'),
('txt_virtual_domain_manager', 'Virtual Domain Manager', 'Verwaltung virtueller Domains'),
('txt_virtual_manager_info', 'Virtual Manager Info', 'Virtuelle Manager Info'),
('txt_virtual_user_manager', 'Virtual User Manager', 'Verwaltung virtueller Benutzer');

-- --------------------------------------------------------

--
-- Table structure for table `virtual_users`
--

CREATE TABLE `virtual_users` (
  `id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `virtual_users`
--

INSERT INTO `virtual_users` (`id`, `domain_id`, `email`, `password`) VALUES
(1, 1, 'master@example.org', '$5$rounds=5000$BZWQcOD00ZRaLjtU$kCMHM3DLdGdVJVzpwchVt4LBExtJgnMgvWSKchrjLw7');

--
-- Triggers `virtual_users`
--
DELIMITER $$
CREATE TRIGGER `virtual_users_after_delete` AFTER DELETE ON `virtual_users` FOR EACH ROW BEGIN
DELETE FROM virtual_aliases WHERE virtual_aliases.destination=old.email;
DELETE FROM virtual_admins WHERE virtual_admins.user_id=old.id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `virtual_users_after_update` AFTER UPDATE ON `virtual_users` FOR EACH ROW UPDATE virtual_aliases SET virtual_aliases.destination = new.email WHERE virtual_aliases.destination=old.email
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `virtual_admins`
--
ALTER TABLE `virtual_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `virtual_aliases`
--
ALTER TABLE `virtual_aliases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `virtual_domains`
--
ALTER TABLE `virtual_domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `virtual_txt`
--
ALTER TABLE `virtual_txt`
  ADD PRIMARY KEY (`txt`);

--
-- Indexes for table `virtual_users`
--
ALTER TABLE `virtual_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `virtual_admins`
--
ALTER TABLE `virtual_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `virtual_aliases`
--
ALTER TABLE `virtual_aliases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `virtual_domains`
--
ALTER TABLE `virtual_domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `virtual_users`
--
ALTER TABLE `virtual_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
