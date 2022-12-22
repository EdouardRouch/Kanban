-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : jeu. 22 déc. 2022 à 14:12
-- Version du serveur :  5.7.34
-- Version de PHP : 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Structure de la table `Can_Access`
--

CREATE TABLE `Can_Access` (
  `id` int(11) NOT NULL,
  `user` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Columns`
--

CREATE TABLE `Columns` (
  `id` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `id_Kanban` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Kanban`
--

CREATE TABLE `Kanban` (
  `id` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `title` varchar(40) NOT NULL,
  `creation_date` date NOT NULL,
  `owner` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Task`
--

CREATE TABLE `Task` (
  `id` int(11) NOT NULL,
  `title` varchar(20) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `creation_date` date NOT NULL,
  `deadline` date NOT NULL,
  `assigned_user` varchar(20) DEFAULT NULL,
  `id_Columns` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE `User` (
  `name` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Can_Access`
--
ALTER TABLE `Can_Access`
  ADD PRIMARY KEY (`id`,`user`),
  ADD KEY `can_access_User0_FK` (`user`);

--
-- Index pour la table `Columns`
--
ALTER TABLE `Columns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Columns_Kanban_FK` (`id_Kanban`);

--
-- Index pour la table `Kanban`
--
ALTER TABLE `Kanban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Kanban_User_FK` (`owner`);

--
-- Index pour la table `Task`
--
ALTER TABLE `Task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Task_User_FK` (`assigned_user`),
  ADD KEY `Task_Columns0_FK` (`id_Columns`);

--
-- Index pour la table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`name`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Columns`
--
ALTER TABLE `Columns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Kanban`
--
ALTER TABLE `Kanban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Task`
--
ALTER TABLE `Task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Can_Access`
--
ALTER TABLE `Can_Access`
  ADD CONSTRAINT `can_access_Kanban_FK` FOREIGN KEY (`id`) REFERENCES `Kanban` (`id`),
  ADD CONSTRAINT `can_access_User0_FK` FOREIGN KEY (`user`) REFERENCES `User` (`name`);

--
-- Contraintes pour la table `Columns`
--
ALTER TABLE `Columns`
  ADD CONSTRAINT `Columns_Kanban_FK` FOREIGN KEY (`id_Kanban`) REFERENCES `Kanban` (`id`);

--
-- Contraintes pour la table `Kanban`
--
ALTER TABLE `Kanban`
  ADD CONSTRAINT `Kanban_User_FK` FOREIGN KEY (`owner`) REFERENCES `User` (`name`);

--
-- Contraintes pour la table `Task`
--
ALTER TABLE `Task`
  ADD CONSTRAINT `Task_Columns0_FK` FOREIGN KEY (`id_Columns`) REFERENCES `Columns` (`id`),
  ADD CONSTRAINT `Task_User_FK` FOREIGN KEY (`assigned_user`) REFERENCES `User` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
