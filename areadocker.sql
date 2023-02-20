-- phpMyAdmin SQL Dump
-- version 5.1.4-1.fc34
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : lun. 13 fév. 2023 à 14:40
-- Version du serveur : 10.5.15-MariaDB
-- Version de PHP : 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `areatest`
--

-- --------------------------------------------------------

--
-- Structure de la table `action`
--

CREATE TABLE `action` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(20) CHARACTER SET utf8 NOT NULL,
  `identifier` varchar(255) CHARACTER SET utf8 NOT NULL,
  `fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fields`))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `action`
--

INSERT INTO `action` (`id`, `service_id`, `name`, `type`, `identifier`, `fields`) VALUES
(1, 1, 'Changer les détails d\'une playlist', 'reaction', 'change_playlist_details', '\"[{\\\"name\\\":\\\"name\\\",\\\"type\\\":\\\"text\\\"},{\\\"name\\\":\\\"description\\\",\\\"type\\\":\\\"text\\\"},{\\\"name\\\":\\\"playlist_id\\\",\\\"type\\\":\\\"dropdown\\\",\\\"uri\\\":\\\"\\\\\\/spotify\\\\\\/get_user_playlists\\\"}]\"'),
(2, 1, 'Lors de l\'ajout d\'une musique dans la playlist', 'action', 'check_music_playlist', '\"[{\\\"name\\\":\\\"playlist_id\\\",\\\"type\\\":\\\"dropdown\\\",\\\"uri\\\":\\\"\\\\\\/spotify\\\\\\/get_user_playlists\\\"}]\"'),
(3, 1, 'Ajouter une musique d\'une liste d\'artiste à une playlist', 'reaction', 'add_artist_music_to_playlist', '\"[{\\\"name\\\":\\\"artist_id\\\",\\\"type\\\":\\\"search\\\",\\\"uri\\\":\\\"\\\\\\/spotify\\\\\\/search?type=artist&search=\\\"},{\\\"name\\\":\\\"playlist_id\\\",\\\"type\\\":\\\"dropdown\\\",\\\"uri\\\":\\\"\\\\\\/spotify\\\\\\/get_user_playlists\\\"}]\"'),
(4, 2, 'Lors du changement du pseudonyme', 'action', 'check_username', NULL),
(5, 2, 'Écrire un message dans un canal', 'reaction', 'send_channel_message', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `automation`
--

CREATE TABLE `automation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `automation`
--

INSERT INTO `automation` (`id`, `user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `automation_action`
--

CREATE TABLE `automation_action` (
  `id` int(11) NOT NULL,
  `automation_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `informations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`informations`))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `automation_action`
--

INSERT INTO `automation_action` (`id`, `automation_id`, `action_id`, `number`, `informations`) VALUES
(1, 1, 1, 2, '\"{\\\"name\\\":\\\"a\\\",\\\"description\\\":\\\"a\\\",\\\"playlist_id\\\":\\\"6YkcNlLRk57qm9FRYnW5Uu\\\"}\"'),
(2, 1, 2, 1, '\"{\\\"playlist_id\\\":\\\"6YkcNlLRk57qm9FRYnW5Uu\\\"}\"');

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `identifiers` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `name`, `identifiers`) VALUES
(1, 'spotify', 'e200f970d99b4c6daefea25cccc5d822;db29317e72994c5d93324754c361c937'),
(2, 'discord', '1070728516188000336;we-5xRtY-80xnRpHkT05Pv3per7-JRn3;MTA3MDcyODUxNjE4ODAwMDMzNg.GhAwCY.RmyWL0U7SoCjcSai3WNVJuWWhq3vp-PdjZjWRU');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 NOT NULL,
  `token` varchar(64) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `token`) VALUES
(1, 'a', 'a', '836879e263b6e2a3ac80bc7679fb5adb', 'ea469788fb5e36ebe666b294a449360e62522eeb5658c1998be297e0ac5553f9');

-- --------------------------------------------------------

--
-- Structure de la table `user_service`
--

CREATE TABLE `user_service` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `access_token` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `refresh_token` varchar(255) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user_service`
--

INSERT INTO `user_service` (`id`, `user_id`, `service_id`, `access_token`, `refresh_token`) VALUES
(2, 1, 1, 'BQB40SRXPWxsJ6MoctnDWK1oMQFqIxb_mXv7bnaWpQ420Z4CLNZZqUegRIXlrEQ7U0_tCjszWFv-2kX-q4oYvdPOwg2gxcDUmiayztBEGVCaiDWMh5pR7VyyF3GE8w2P0bJoNdF-JhSpnLh7TJ12wBEsL6uhhfBui3AQ0LhxamMsAeqXEa2ywwvPB17kUPuco6oONaJ4o31NttRr7KpVYMnhNiNNmzjh0DmtreJxRQZ1wvfDJ82OKqjpKumq-1S-d8aKm-SGs_RJOjeO9_XB1StmjHX6KC50YCKHr6Zpm4tqzcpz1IEluiEM8MQPZDRw1qtJnqo6-95oJigx94k', 'AQDqRThF4BdhkFAjJApUtDKDnR4LJu5uE3nOcNeji60XHDK1FeKqNtQwnpcR-2Uywb64CKi8lMRoxlrGu7PH4zgbtyZkDfLbG2agx1QoqiHbPAGzUhTkC9QgvPS6IHbBffE');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `action`
--
ALTER TABLE `action`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Index pour la table `automation`
--
ALTER TABLE `automation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `automation_action`
--
ALTER TABLE `automation_action`
  ADD PRIMARY KEY (`id`),
  ADD KEY `automation_id` (`automation_id`),
  ADD KEY `action_id` (`action_id`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user_service`
--
ALTER TABLE `user_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `action`
--
ALTER TABLE `action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `automation`
--
ALTER TABLE `automation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `automation_action`
--
ALTER TABLE `automation_action`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user_service`
--
ALTER TABLE `user_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `action`
--
ALTER TABLE `action`
  ADD CONSTRAINT `action_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`);

--
-- Contraintes pour la table `automation`
--
ALTER TABLE `automation`
  ADD CONSTRAINT `automation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `automation_action`
--
ALTER TABLE `automation_action`
  ADD CONSTRAINT `automation_action_ibfk_1` FOREIGN KEY (`automation_id`) REFERENCES `automation` (`id`),
  ADD CONSTRAINT `automation_action_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `action` (`id`);

--
-- Contraintes pour la table `user_service`
--
ALTER TABLE `user_service`
  ADD CONSTRAINT `user_service_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `user_service_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
