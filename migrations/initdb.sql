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
  `category` enum('A','B','C') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
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
(1, 1, 'A', 'Riesenrad im Luna-Park', '', 'Der Luna-Park in Odessa ist einer der bekanntesten Freizeitparks in der Ukraine und befindet sich in der Stadt Odessa am Ufer des Schwarzen Meeres. Der Park wurde 2002 eröffnet und ist seitdem zu einer beliebten Attraktion für Einheimische und Touristen gleichermaßen geworden.\r\n\r\nDer Luna-Park bietet eine Vielzahl von Unterhaltungsmöglichkeiten für Besucher jeden Alters, darunter Fahrgeschäfte, Spielhallen, Restaurants und Souvenirläden. Zu den Hauptattraktionen gehören das Riesenrad, eine Achterbahn, Karussells, Schießstände und viele andere Vergnügungseinrichtungen.', '[{\"path\": \"posts/1/IPByGu\", \"size\": 300085, \"type\": \"image/jpeg\", \"thumb\": \"posts/1/t_IPByGu\", \"original\": \"51375230566_d0ab855d1b_c.jpg\"}]', '2024-02-14 23:36:31', '2024-02-14 23:43:05'),
(2, 1, 'A', 'Sonnenblumenfelder', '', 'Die Nordukraine, insbesondere die Oblasten Sumy, Tschernihiw und Charkiw, ist für ihre fruchtbaren Böden und das gemäßigte Klima bekannt, die ideale Bedingungen für den Anbau von Sonnenblumen bieten. Sonnenblumen sind eine wichtige Nutzpflanze in der Ukraine und werden für die Produktion von Sonnenblumenöl, Samen und Viehfutter angebaut.\r\n\r\nWährend der Sommermonate erstrecken sich die endlosen Felder mit Sonnenblumen über das Land und bilden ein beeindruckendes Schauspiel. Die leuchtend gelben Blüten neigen sich sanft im Wind und schaffen ein malerisches Panorama, das Besucher aus der ganzen Welt anlockt.\r\n\r\nDie Sonnenblumenfelder der Nordukraine sind nicht nur landschaftlich reizvoll, sondern haben auch eine kulturelle Bedeutung für die Region. Sonnenblumen gelten oft als Symbol für Wohlstand, Hoffnung und Freude und werden in der ukrainischen Kultur hoch geschätzt.', '[{\"path\": \"posts/2/Ef05RF\", \"size\": 216214, \"type\": \"image/jpeg\", \"thumb\": \"posts/2/t_Ef05RF\", \"original\": \"51351331982_5e124f9a8f_c.jpg\"}]', '2024-02-14 23:37:06', '2024-02-14 23:44:29'),
(3, 1, 'B', 'Schiffswrak in Vestvågøy', '', 'Dieses an der Steuerbordseite des Rumpfes und am Oberdeck stark beschädigte und daraufhin wohl mindestens zum Teil gekenterte Fischerboot wurde zu einem an der Südküste der Lofoteninsel Vestvågøy gelegenen Schiffsfriedhof geschleppt und hat dort seine letzte Ruhestätte gefunden, bevor ich es während meiner Norwegenreise mit meiner Kamera verewigte.', '[{\"path\": \"posts/3/kwX5j8\", \"size\": 239825, \"type\": \"image/jpeg\", \"thumb\": \"posts/3/t_kwX5j8\", \"original\": \"48271424992_cc0c1694a9_c.jpg\"}]', '2024-02-14 23:38:19', '2024-02-14 23:46:00'),
(4, 1, 'C', 'Im Nebelwald', '', 'Die nebelverhangenen Wälder der Kanarischen Inseln sind ein faszinierendes und einzigartiges Ökosystem, das sich durch seine besondere Flora und Fauna auszeichnet. Diese Wälder, auch als \"Laurisilva\" bekannt, sind Relikte aus der Tertiärzeit und gehören zu den ältesten Wäldern der Welt. Sie sind ein wichtiges Naturerbe und haben daher eine besondere Bedeutung für die Kanarischen Inseln.\r\n\r\nDie Laurisilva-Wälder erstrecken sich hauptsächlich in höheren Lagen der Inseln, wo die feuchten Passatwinde auf die Berge treffen und für die Bildung von Nebel sorgen. Diese Nebel, die in den Wäldern hängen bleiben, tragen dazu bei, die Feuchtigkeit zu erhalten und schaffen so ein einzigartiges Mikroklima, das für das Wachstum einer Vielzahl von Pflanzenarten entscheidend ist.\r\n\r\nDie Vegetation in den nebelverhangenen Wäldern der Kanarischen Inseln ist äußerst vielfältig und umfasst viele endemische Arten, die nirgendwo sonst auf der Welt vorkommen. Zu den charakteristischen Pflanzen zählen Lorbeerbäume, Baumheide, Baumfarnarten und verschiedene Moose und Flechten. Diese Artenvielfalt bietet Lebensraum für eine Vielzahl von Tieren, darunter seltene Vogelarten wie den Kanarengirlitz und den Kanarenzeisig, sowie zahlreiche Insekten- und Spinnenarten.\r\n\r\nDie nebelverhangenen Wälder der Kanarischen Inseln sind nicht nur ein Paradies für Naturliebhaber und Wanderer, sondern spielen auch eine wichtige ökologische Rolle für den Wasserhaushalt und den Erhalt der Biodiversität auf den Inseln. Aus diesem Grund sind viele dieser Wälder als Naturreservate geschützt und stehen unter dem Schutz der UNESCO als Weltnaturerbe.', '[{\"path\": \"posts/4/S6f0yW\", \"size\": 184132, \"type\": \"image/jpeg\", \"thumb\": \"posts/4/t_S6f0yW\", \"original\": \"39135077682_41d0e39bfb_c.jpg\"}]', '2024-02-14 23:38:27', '2024-02-14 23:47:39'),
(5, 1, 'C', 'Roque de Agando', '', 'Der Roque de Agando ist ein markantes geologisches Wahrzeichen der Kanarischen Insel La Gomera. Dieser monolithische Felsen, der sich majestätisch aus der umgebenden Landschaft erhebt und eine Höhe von etwa 1.250 Metern über dem Meeresspiegel erreicht, befindet sich im Zentrum der Insel im Nationalpark Garajonay.\r\n\r\nDie Formation des Roque de Agando ist das Ergebnis vulkanischer Aktivität und Erosion über Millionen von Jahren hinweg. Er besteht hauptsächlich aus Basaltgestein und ist durch seine markante kegelförmige Spitze gekennzeichnet. Der Felsen ist Teil einer Reihe von ähnlichen geologischen Formationen auf La Gomera, die als \"Roques\" bekannt sind und charakteristisch für die Landschaft der Insel sind.', '[{\"path\": \"posts/5/lTKX7L\", \"size\": 179421, \"type\": \"image/jpeg\", \"thumb\": \"posts/5/t_lTKX7L\", \"original\": \"38259971925_071541106a_c.jpg\"}]', '2024-02-14 23:38:32', '2024-02-14 23:51:54'),
(6, 1, 'B', 'Mitternachtssonne am Jarfjord', '', 'Die Mitternachtssonne taucht den nahe der norwegisch-russischen Grenze gelegenen Jarfjord, der sich gen Horizont zur Barentssee hin öffnet, in mystische Farben.', '[{\"path\": \"posts/6/Z8mRLi\", \"size\": 99277, \"type\": \"image/jpeg\", \"thumb\": \"posts/6/t_Z8mRLi\", \"original\": \"48254089156_42bdae2dcf_c.jpg\"}]', '2024-02-14 23:57:49', '2024-02-14 23:57:49'),
(7, 1, 'B', 'Botnoya Bucht', '', 'Dieses Bild zeigt den Blick über die norwegische Botnoya-Bucht im Scheine der Mitternachtssonne.\r\n\r\nDas am Horizont auszumachende Gebirgsmassiv  beherbergt die Berge Stortinden, Hoglhornet, Blomholatinden und Kobbenestinden, deren Silhouetten sich an der Wasseroberfläche des sich in der linken Bildhälfte erstreckenden Tysfjords widerspiegeln.', '[{\"path\": \"posts/7/SneSfl\", \"size\": 193409, \"type\": \"image/jpeg\", \"thumb\": \"posts/7/t_SneSfl\", \"original\": \"48271357141_1e2f3a8dfb_c.jpg\"}]', '2024-02-14 23:58:06', '2024-02-15 00:00:40');

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
(1, 'Timor', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'timor@kodal.de', NULL, '2024-01-31 18:25:41', '2024-02-14 23:23:16'),
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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

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
