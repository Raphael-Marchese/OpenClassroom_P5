CREATE TABLE `user` (
                        `id` integer AUTO_INCREMENT PRIMARY KEY,
                        `first_name` varchar(255),
                        `last_name` varchar(255),
                        `username` varchar(255),
                        `email` varchar(255) UNIQUE NOT NULL,
                        `password` varchar(255),
                        `role` ENUM ('ROLE_ADMIN', 'ROLE_USER')
);

CREATE TABLE `blog_post` (
                             `id` integer AUTO_INCREMENT PRIMARY KEY,
                             `title` varchar(255),
                             `chapo` varchar(255),
                             `created_at` dateTime,
                             `updated_at` dateTime,
                             `content` text,
                             `status` ENUM ('draft', 'pending', 'published'),
                             `author` integer
);

CREATE TABLE `comment` (
                           `id` integer AUTO_INCREMENT PRIMARY KEY,
                           `content` text,
                           `created_at` dateTime,
                           `updated_at` datetime,
                            `status` ENUM ('draft', 'pending', 'published'),
                           `author` integer,
                           `post` integer
);

ALTER TABLE `blog_post` ADD FOREIGN KEY (`author`) REFERENCES `user` (`id`);

ALTER TABLE `comment` ADD FOREIGN KEY (`author`) REFERENCES `user` (`id`);

ALTER TABLE `comment` ADD CONSTRAINT comment_ibfk_2 FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`) ON DELETE CASCADE;
