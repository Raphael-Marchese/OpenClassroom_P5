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
                             `chapô` varchar(255),
                             `createdAt` dateTime,
                             `updatedAt` dateTime,
                             `content` varchar(255),
                             `author` integer
);

CREATE TABLE `comment` (
                           `id` integer AUTO_INCREMENT PRIMARY KEY,
                           `content` varchar(255),
                           `createdAt` dateTime,
                           `updatedAt` datetime,
                           `author` integer,
                           `post` integer
);

ALTER TABLE `blog_post` ADD FOREIGN KEY (`author`) REFERENCES `user` (`id`);

ALTER TABLE `comment` ADD FOREIGN KEY (`author`) REFERENCES `user` (`id`);

ALTER TABLE `comment` ADD FOREIGN KEY (`post`) REFERENCES `blog_post` (`id`);
