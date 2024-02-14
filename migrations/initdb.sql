SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Datenbank: `sae-cms`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts`
--

CREATE TABLE `posts` (
  `id` int UNSIGNED NOT NULL,
  `user` int UNSIGNED NOT NULL,
  `category` enum('A','B') NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `media` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `posts`
--

INSERT INTO `posts` (`id`, `user`, `category`, `title`, `author`, `content`, `media`, `created_at`, `updated_at`) VALUES
(1, 1, 'B', 'Testeintrag', '', 'Lorem Ipsum', '[{\"path\": \"posts/1/UXAnsL\", \"size\": 38893, \"type\": \"image/jpeg\", \"thumb\": \"posts/1/t_UXAnsL\", \"original\": \"Timor.jpg\"}]', '2024-01-31 18:26:32', '2024-02-08 22:58:11'),
(2, 2, 'B', 'Mein toller neuer Beitrag', '', 'Mit hochinteressantem Inhalt', NULL, '2024-01-31 18:44:17', '2024-01-31 18:44:17'),

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int UNSIGNED NOT NULL,
  `user` int UNSIGNED NOT NULL,
  `post` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `post_likes`
--

INSERT INTO `post_likes` (`id`, `user`, `post`, `created_at`) VALUES
(1, 2, 2, '2024-02-01 21:43:10'),
(2, 2, 1, '2024-02-01 21:43:20'),
(3, 1, 2, '2024-02-08 23:09:30');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(320) NOT NULL,
  `media` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `media`, `created_at`, `updated_at`) VALUES
(1, 'Timor', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'timor@kodal.de', NULL, '2024-01-31 18:25:41', '2024-02-14 22:15:36'),
(2, 'Ellie', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'timor@kodal.de', NULL, '2024-01-31 18:43:45', '2024-01-31 18:43:45'),
(3, 'Joel', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'timor@kodal.de', NULL, '2024-02-06 11:09:46', '2024-02-06 11:09:46');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user`),
  ADD KEY `category` (`category`);

--
-- Indizes für die Tabelle `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_post` (`user`,`post`) USING BTREE,
  ADD KEY `post` (`post`),
  ADD KEY `user` (`user`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
