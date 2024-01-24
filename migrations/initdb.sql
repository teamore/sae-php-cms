USE `sae-cms`;
CREATE table `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `posts` 
    (`id`, `title`, `author`, `content`, `created_at`, `updated_at`)
VALUES
    (1, 'Hello World', 'John Doe', 'Lorem Ipsum' , '2019-01-01 00:00:00', '2019-01-01 00:00:00'),
    (2, 'Hello World 2', 'John Doe', 'Lorem Ipsum' , '2019-01-01 00:00:00', '2019-01-01 00:00:00'),
    (3, 'Hello World 3', 'John Doe', 'Lorem Ipsum' , '2019-01-01 00:00:00', '2019-01-01 00:00:00');

CREATE table `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `password` TEXT NOT NULL,
  `email` varchar(320) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `users` 
    (`id`, `username`, `password`, `email`, `created_at`, `updated_at`)
VALUES
    (1, 'testuser1', '123', 'user@example.com' , '2019-01-01 00:00:00', '2019-01-01 00:00:00'),
    (2, 'testuser2', '123', 'user@example.com' , '2019-01-01 00:00:00', '2019-01-01 00:00:00'),
    (3, 'testuser3', '123', 'user@example.com' , '2019-01-01 00:00:00', '2019-01-01 00:00:00');
